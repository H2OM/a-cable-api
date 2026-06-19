<?php

namespace app\repositories;

use app\core\Db;
use app\core\Hydrator;

/** Репозиторий для управления товарами */
readonly class ProductsRepository {
    private const string PRODUCTS_WITH_VARIATIONS_SQL = "
        SELECT 
            products.*, 
            categories_types.name AS category, 
            categories_types.code as category_code,
            brands.name as brand, 
            products_stocks.count as stock,
            IF(COUNT(var_p.id) > 0,
               JSON_ARRAYAGG(JSON_OBJECT('id', var_p.id, 'image', var_p.image)),
               JSON_ARRAY()
            ) AS variations
        FROM products 
        JOIN categories_types ON products.category_type_id = categories_types.id
        LEFT JOIN products_stocks ON products_stocks.product_id = products.id
        LEFT JOIN brands ON products.brand_id = brands.id
        LEFT JOIN products_variations ON products.id = products_variations.product_id
        LEFT JOIN products AS var_p ON products_variations.variation_id = var_p.id
        %s
        WHERE (%s)
        GROUP BY products.id 
        %s";

    private const string PRODUCT_WITH_DETAILS = "
        SELECT 
            products.*, 
            categories_types.name AS category, 
            categories_types.code AS category_code,
            brands.name AS brand, 
            products_stocks.count AS stock,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'id', vp.id, 
                    'image', vp.image,
                    'title', vp.title,
                    'price', vp.price,
                    'article', vp.article
                )), JSON_ARRAY())
                FROM products_variations p_v
                JOIN products vp ON p_v.variation_id = vp.id
                WHERE p_v.product_id = products.id
            ) AS variations,
            (
                SELECT COALESCE(
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'filter_name', f.filter, 
                            'filter_code', f.code, 
                            'value_name', f_v.value, 
                            'value_code', f_v.code, 
                            'value_id', f_v.id,
                            'product_id', f_v_p.product_id 
                        )
                    ), 
                    JSON_ARRAY()
                )
                FROM filters_values_products f_v_p
                JOIN filters_values f_v ON f_v_p.filter_value_id = f_v.id
                JOIN filters f ON f_v.filter_id = f.id
                LEFT JOIN products_variations p_v ON f_v_p.product_id = p_v.variation_id AND p_v.product_id = products.id
                WHERE f_v_p.product_id = products.id OR p_v.variation_id IS NOT NULL
            ) AS local_filters
        FROM products 
        JOIN categories_types ON products.category_type_id = categories_types.id
        LEFT JOIN products_stocks ON products_stocks.product_id = products.id
        LEFT JOIN brands ON products.brand_id = brands.id
        WHERE (%s);
    ";

    public function __construct(private Db $db, private Hydrator $hydrator) {}

    /**
     * Получение всех товаров, с лимитом
     *
     * @param int $factor
     * @param int $limit
     * @return array
     */
    public function getAllByLimit(int $factor, int $limit): array {
        return $this->hydrator->decodeJson(
            $this->db->fetchAll(sprintf(
                self::PRODUCTS_WITH_VARIATIONS_SQL,
                '',
                "1",
                'LIMIT ?, ?'
            ), [$factor * $limit, $limit]),
            ['variations']
        );
    }

    /**
     * Получение количества товаров, с возможностью подсчета по подкатегории и бренду
     *
     * @param int|null $categoryTypeId
     * @param int|null $brandId
     * @return int
     */
    public function getCount(?int $categoryTypeId = null, ?int $brandId = null): int {
        $queryBuilder = $this->db->query()
            ->table('products')
            ->select('COUNT(*) AS count');

        if ($categoryTypeId !== null) {
            $queryBuilder->where('category_type_id', $categoryTypeId);
        }

        if ($brandId !== null) {
            $queryBuilder->where('brand_id', $brandId);
        }

        return (int)$queryBuilder->first()['count'];
    }

    /**
     * Получение популярных и скидочных товаров
     *
     * @return array
     */
    public function getHitAndSales(): array {
        return $this->hydrator->decodeJson(
            $this->db->fetchAll(sprintf(
                self::PRODUCTS_WITH_VARIATIONS_SQL,
                '',
                "products.hit = '1' OR products.price_old > 0",
                ''
            )),
            ['variations']
        );
    }

    /**
     * Получение товаров по фильтрам
     *
     * @param array $filters
     * @return array
     */
    public function getByFilters(array $filters): array {
        $whereCondition = [];
        $filtersCondition = [];
        $endCondition = 'GROUP BY products.id';
        $wherePrepareValues = [];
        $filtersPrepareValues = [];

        foreach ($filters as $key => $value) {
            switch($key) {
                case "sort":
                    $endCondition .= match ($value) {
                        'low-to-high' => ' ORDER BY products.price ASC',
                        'high-to-low' => ' ORDER BY products.price DESC',
                        default => ''
                    };

                    break;
                case "category":
                    $whereCondition[] = "categories.code = ?";
                    $wherePrepareValues[] = $value;

                    break;
                case "price":
                    $price = explode(",", $value);

                    if(count($price) === 2 && is_numeric($price[0]) && is_numeric($price[1])) {
                        array_push($whereCondition, "products.price <= ?", "products.price >= ?");
                        array_push($wherePrepareValues, $price[1], $price[0]);
                    }

                    break;
                case "sale":
                    switch($value) {
                        case "yes":
                            $whereCondition[] = "products.price <= (products.price_old * 1)";

                            break;
                        case "no":
                            $whereCondition[] = "products.price_old  = 0";

                            break;
                        default:
                            break;
                    }

                    break;
                case "brand":
                case "type":
                    $filterCode = match ($key) {
                        'brand' => "brands",
                        'type' => "categories_types",
                    };

                    $filterValues = explode(",", $value);
                    $wherePrepareValues = [...$wherePrepareValues, ...$filterValues];
                    $wheres = implode(",", array_fill(0, count($filterValues), '?'));

                    $whereCondition[] = "$filterCode.code IN ($wheres)";

                    break;
                default:
                    $filterValues = explode(",", $value);
                    $wheres = implode(",", array_fill(0, count($filterValues), '?'));

                    $filtersCondition[] = "(filters.code = ? AND filters_values.code IN ($wheres))";

                    array_push($filtersPrepareValues, $key, ...$filterValues);

                    break;
            }
        }

        if(count($filtersCondition) > 0) {
            $whereCondition[] = "(" . implode(' OR ', $filtersCondition) . ")";
            $wherePrepareValues = [...$wherePrepareValues, ...$filtersPrepareValues];

            $endCondition .= " HAVING COUNT(DISTINCT filters.code) = " . count($filtersCondition);
        }

        if(count($whereCondition) > 0) {
            $whereCondition = " WHERE ". implode(' AND ', $whereCondition);

        } else {
            $whereCondition = "";
        }

        return $this->hydrator->decodeJson($this->db->fetchAll("
            SELECT 
                products.*, 
                categories.title AS category_parent,
                categories_types.name AS category, 
                categories_types.code AS category_code,
                brands.name as brand, 
                products_stocks.count AS stock,
                (
                    SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT('id', vp.id, 'image', vp.image)), JSON_ARRAY())
                    FROM products_variations p_v
                    JOIN products vp ON p_v.variation_id = vp.id AND vp.id != products.id
                    WHERE p_v.product_id = products.id
                ) AS variations
            FROM products
            JOIN filters_values_products ON products.id = filters_values_products.product_id
            JOIN filters_values ON filters_values_products.filter_value_id = filters_values.id 
            JOIN filters ON filters_values.filter_id = filters.id
            JOIN categories_types ON products.category_type_id = categories_types.id
            JOIN categories ON categories_types.category_id = categories.id 
            LEFT JOIN products_stocks ON products_stocks.product_id = products.id
            LEFT JOIN brands ON products.brand_id = brands.id
            $whereCondition 
            $endCondition
        ", $wherePrepareValues), ['variations']);
    }

    /**
     * Получение отдельного товара с подробностями по id
     *
     * @param int $id
     * @return array|null
     */
    public function getProductDetailsById(int $id): array|null {
        $response = $this->db->fetchOne(sprintf(
            self::PRODUCT_WITH_DETAILS,
            "products.id = ?"
        ), [$id]);

        if(!is_array($response)) return null;

        return $this->hydrator->decodeJson($response, ['variations', 'local_filters']);
    }

    /**
     * Получение связанных товаров по id
     *
     * @param int $id
     * @return array
     */
    public function getRelatedById(int $id): array {
        return $this->hydrator->decodeJson(
            $this->db->fetchAll(sprintf(
                self::PRODUCTS_WITH_VARIATIONS_SQL,
                'JOIN products_related ON products.id = products_related.related_id',
                "products_related.product_id = ?",
                ''
            ), [$id]),
            ['variations']
        );
    }

    /**
     * Получение id товаров по артикулу
     *
     * @param array $articles
     * @return array
     */
    public function getProductsIdsByArticle(array $articles): array {
        return $this->db->query()
            ->table('products')
            ->select(['id', 'article'])
            ->where('article', 'IN', $articles)
            ->get();
    }

    /**
     * Получение товаров по id
     *
     * @param array $id
     * @return array
     */
    public function getProductsById(array $id): array {
        return $this->db->fetchAll($this->prepareProductsById([$id]), [$id]);
    }

    /**
     * Получение отдельного товара по id
     *
     * @param int $id
     * @return array|null
     */
    public function getProductById(int $id): array|null {
        return $this->db->fetchOne($this->prepareProductsById([$id]), [$id]);
    }

    /**
     * Получение товаров по id с вариациями
     *
     * @param array $ids
     * @return array
     */
    public function getProductsWithVariationsById(array $ids): array {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        return $this->hydrator->decodeJson(
            $this->db->fetchAll(sprintf(
                self::PRODUCTS_WITH_VARIATIONS_SQL,
                '',
                "products.id IN ($placeholders)",
                ''
            ), $ids),
            ['variations']
        );
    }

    /**
     * Получение товаров по id с вариациями
     *
     * @param int $userId
     * @return array
     */
    public function getProductsWithVariationsByUserId(int $userId): array {
        return $this->hydrator->decodeJson(
            $this->db->fetchAll(sprintf(
                self::PRODUCTS_WITH_VARIATIONS_SQL,
                'LEFT JOIN favorites ON products.id = favorites.product_id',
                "favorites.user_id = ?",
                ''
            ), [$userId]),
            ['variations']
        );
    }

    /**
     * Получение отдельного товара по id с вариациями
     *
     * @param int $id
     * @return array|null
     */
    public function getProductWithVariationsById(int $id): array|null {
        $response = $this->db->fetchOne(sprintf(
            self::PRODUCTS_WITH_VARIATIONS_SQL,
            '',
            "products.id = ?",
            ''
        ), [$id]);

        if(!is_array($response)) return null;

        return $this->hydrator->decodeJson($response, ['variations']);
    }

    /**
     * Добавление
     *
     * @param array $data
     * @return bool
     */
    public function insert(array $data): bool {
        return $this->db->query()
            ->table('products')
            ->insert($data)
            ->execute();
    }

    /**
     * Добавление с возвращением id
     *
     * @param array $data
     * @return false|string
     */
    public function insertId(array $data): false|string {
        return $this->db->query()
            ->table('products')
            ->insert($data)
            ->insertId();
    }

    /**
     * Добавление наличия товаров
     *
     * @param array $data формат:
     *
     *                  ['product_id' => int, 'count' => int]
     *
     * ИЛИ
     *
     *                  [
     *                      ['product_id' => int, 'count' => int], ...
     *                  ]
     *
     * @return bool
     */
    public function insertStock(array $data): bool {
        return $this->db->query()
            ->table('products_stocks')
            ->insert($data)
            ->execute();
    }

    /**
     * Обновление товара
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data): bool {
        return $this->db->query()
            ->table('products')
            ->where('id', $data['id'])
            ->update($data)
            ->execute();
    }

    /**
     * Обновление товаров
     *
     * @param array $ids
     * @param array $data
     * @return bool
     */
    public function updateMany(array $ids, array $data): bool {
        return $this->db->query()
            ->table('products')
            ->where('id', 'IN', $ids)
            ->update($data)
            ->execute();
    }

    /**
     * Обновление остатка товара
     *
     * @param int $id
     * @param int $stock
     * @return bool
     */
    public function updateStock(int $id, int $stock): bool {
        return $this->db->query()
            ->table('products_stocks')
            ->where('product_id', $id)
            ->update(['count' => $stock])
            ->execute();
    }

    /**
     * Обновление товаров по брендам
     *
     * @param array $brandsId
     * @param array $data
     * @return bool
     */
    public function updateByBrands(array $brandsId, array $data): bool {
        return $this->db->query()
            ->table('products')
            ->where('brand_id', 'IN', $brandsId)
            ->update($data)
            ->execute();
    }

    /**
     * Удаление товаров по Id
     *
     * @param array $ids
     * @return bool
     */
    public function deleteByIds(array $ids): bool {
        return $this->db->query()
            ->table('products')
            ->where('id', 'IN', $ids)
            ->delete()
            ->execute();
    }

    /**
     * Привязка вариаций
     *
     * @param array $data
     * @return bool
     */
    public function pairVariations(array $data): bool {
        return $this->db->query()
            ->table('products_variations')
            ->insert($data)
            ->execute();
    }

    /**
     * Привязка связанных товаров
     *
     * @param array $data
     * @return bool
     */
    public function pairRelated(array $data): bool {
        return $this->db->query()
            ->table('products_related')
            ->insert($data)
            ->execute();
    }

    /**
     * Присваивание товарам статуса хит продаж
     *
     * @param array $ids
     * @param bool $status
     * @return bool
     */
    public function changeHit(array $ids, bool $status): bool {
        return $this->db->query()
            ->table('products')
            ->where('id', 'IN', $ids)
            ->update(['hit' => $status ? 1 : 0])
            ->execute();
    }

    /**
     * Поиск товаров по строке запроса
     *
     * @param string $query
     * @return array
     */
    public function searchByQuery(string $query): array {
        return $this->db->query()
            ->table('products')
            ->select(['id', 'CONCAT(title, " | ", article) AS name', 'image'])
            ->where('id', 'LIKE', "$query%")
            ->orWhere('title', 'LIKE', "%$query%")
            ->orWhere('article', 'LIKE', "%$query%")
            ->limit(15)
            ->get();
    }

    /**
     * Подготовка запроса на получения товара/товаров по id
     *
     * @param array $ids
     * @return string
     */
    private function prepareProductsById(array $ids): string {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        return "
            SELECT 
                products.*, 
                categories_types.name AS category, 
                categories_types.code as category_code, 
                brands.name as brand, 
                products_stocks.count as stock
            FROM products 
            JOIN categories_types ON products.category_type_id = categories_types.id
            LEFT JOIN products_stocks ON products_stocks.product_id = products.id
            LEFT JOIN brands ON products.brand_id = brands.id
            WHERE products.id IN ($placeholders)
            GROUP BY products.id";
    }
}