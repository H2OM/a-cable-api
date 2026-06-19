<?php

namespace app\repositories;

use app\core\Db;
use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Hydrator;
use Exception;

readonly class OrdersRepository {
    private const string GET_ORDER = "
        SELECT 
            orders.*, 
            delivery_types.name AS 'delivery', 
            payment_types.name AS 'payment', 
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'id', orders_products.product_id, 
                    'title', products.title, 
                    'count', orders_products.count,
                    'image', products.image,
                    'article', products.article,
                    'price', products.price,
                    'price_old', products.price_old,
                    'count', orders_products.count,
                    'unit', products.unit
                )), JSON_ARRAY())
                FROM orders_products
                JOIN products ON orders_products.product_id = products.id
                WHERE orders_products.order_id = orders.id
            ) AS products
            %s
        FROM `orders` 
        JOIN delivery_types ON orders.delivery_type_id = delivery_types.id
        JOIN payment_types ON orders.payment_type_id = payment_types.id
        %s
    ";

    public function __construct(private Db $db, private Hydrator $hydrator) {}

    /**
     * Получение всех заказов пользователя по его id
     *
     * @param int $userId
     * @return array
     */
    public function getByUserId(int $userId): array {
        return $this->db->fetchAll("
            SELECT 
                orders.*, 
                orders.id as order_id,
                products.*, 
                categories_types.name AS category, 
                categories_types.code as category_code, 
                brands.name as brand, 
                products_stocks.count as stock
            FROM orders 
                JOIN orders_products ON orders.id = orders_products.order_id 
                LEFT JOIN products ON orders_products.product_id = products.id
                JOIN categories_types ON products.category_type_id = categories_types.id
                LEFT JOIN brands ON products.brand_id = brands.id
                LEFT JOIN products_stocks ON products_stocks.product_id = products.id
            WHERE orders.user_id = ? 
            ORDER BY `orders`.`date` DESC
        ", [$userId]);
    }

    /**
     * Получение заказа по id
     *
     * @param int $id
     * @return array|null
     */
    public function getOrderById(int $id): ?array {
        return $this->hydrator->decodeJson(
            $this->db->fetchOne(sprintf(
                self::GET_ORDER,
                '',
                'WHERE orders.id = ?',
            ), [$id]),
            ['products']
        );
    }

    /**
     * Получение всех заказов, с лимитом
     *
     * @param int $factor
     * @param int $limit
     * @return array
     */
    public function getAllByLimit(int $factor, int $limit): array {
        $userSelect = "
            users.id AS user_id, 
            users.first_name AS user_first_name, 
            users.second_name AS user_second_name,
            users.age AS user_age,
            users.gender AS user_gender,
            users.email AS user_email,
            users.phone AS user_phone
        ";
        $condition = "
            LEFT JOIN users ON users.id = orders.user_id
            LIMIT ?, ?
        ";

        return $this->hydrator->decodeJson(
            $this->db->fetchAll(sprintf(
                self::GET_ORDER,
                ", $userSelect",
                $condition
            ), [$factor * $limit, $limit]),
            ['products']
        );
    }

    /**
     * Получение кол-во заказов
     *
     * @param int|null $userId
     * @return int
     */
    public function getCount(?int $userId = null): int {
        $queryBuilder = $this->db->query()
            ->table('orders')
            ->select('COUNT(*) AS count');

        if ($userId !== null) {
            $queryBuilder->where(field: 'user_id', value: $userId);
        }

        return (int)$queryBuilder->first()['count'];
    }

    /**
     * Получение всех способов доставки
     *
     * @return array
     */
    public function getDeliveryTypes(): array {
        return $this->db->query()
            ->table('delivery_types')
            ->get();
    }

    /**
     * Получение всех способов оплаты
     *
     * @return array
     */
    public function getPaymentTypes(): array {
        return $this->db->query()
            ->table('payment_types')
            ->get();
    }

    /**
     * Создание нового заказа
     * **Обязательные поля:**
     *
     *                      [
     *                          'number' => int(13),
     *                          'user_id' => int,
     *                          'delivery_type_id' => int,
     *                          'payment_type_id' => int,
     *                          'price' => int,
     *                          'delivery_price' => int,
     *                          'delivery_date' => string,
     *                          'delivery_address' => string,
     *                          'products' => [['id' => int, 'count' => int]]
     *                      ]
     *
     * @param array $data
     * @return int
     * @throws ResponseException
     */
    public function newOrder(array $data): int {
        try {
            $this->db->beginTransaction();

            $orderId = $this->db->query()
                ->table('orders')
                ->insert([
                    'number'           => $data['number'],
                    'user_id'          => $data['user_id'],
                    'delivery_type_id' => $data['delivery_type_id'],
                    'payment_type_id'  => $data['payment_type_id'],
                    'price'            => $data['price'],
                    'delivery_price'   => $data['delivery_price'],
                    'delivery_date'    => $data['delivery_date'],
                    'delivery_address' => $data['delivery_address'],
                    'comment'          => $data['comment'] ?? '',
                ])
                ->insertId();

            if (!$orderId) {
                throw new ResponseException(ResponseMessage::ERROR_CREATE_ORDER);
            }

            $result = $this->db->query()
                ->table('orders_products')
                ->insert(array_map(function ($product) use ($orderId) {
                    return [
                        'order_id' => $orderId,
                        'product_id' => $product['id'],
                        'count' => $product['count'],
                    ];
                }, $data['products']))
                ->execute();

            if (!$result) {
                throw new ResponseException(ResponseMessage::ERROR_CREATE_ORDER);
            }

            $productsStock = $this->db->query()
                ->table('products_stocks')
                ->where('product_id', 'IN', array_map(function ($product) {
                    return $product['id'];
                }, $data['products']))
                ->get();

            $result = $this->db->query()
                ->table('products_stocks')
                ->updateMany(array_map(function ($product) {
                    return [
                        'product_id' => $product['id'],
                        'count' => $product['count'],
                    ];
                }, $data['products']), [['count', '-']])
                ->execute();

            if (!$result) {
                throw new ResponseException(ResponseMessage::ERROR_CREATE_ORDER);
            }

            $this->db->commit();

            return $orderId;
        } catch (Exception $exception) {
            $this->db->rollBack();

            throw $exception;
        }
    }

    /**
     * Обновление статуса
     *
     * @param array $ids
     * @param string $status
     * @return bool
     */
    public function updateStatus(array $ids, string $status): bool {
        return $this->db->query()
            ->table('orders')
            ->where('id', 'IN', $ids)
            ->update(['status' => $status])
            ->execute();
    }

    /**
     * Удаление
     *
     * @param array $ids
     * @return bool
     */
    public function delete(array $ids): bool {
        return $this->db->query()
            ->table('orders')
            ->where('id', 'IN', $ids)
            ->delete()
            ->execute();

    }
}