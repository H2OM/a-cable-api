<?php

namespace app\services\admin;

use app\repositories\OrdersRepository;

/** Сервис для управления заказами */
readonly class OrdersService {
    public function __construct(
        private OrdersRepository $ordersRepository
    ) {}

    /**
     * Получение всех заказов, с лимитом
     *
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAllByLimit(int $page, int $limit): array {
        if($limit > 100) $limit = 100;

        $orders = $this->ordersRepository->getAllByLimit(factor: $page - 1, limit: $limit);

        foreach($orders as &$order) {
            foreach ($order as $key => $value) {
                if(str_contains($key, 'user_')) {
                    $order['user'][str_replace('user_', '', $key)] = $value;
                    unset($order[$key]);
                }
            }
        }

        return $orders;
    }

    /**
     * Получение кол-ва всех заказов
     *
     * @param int|null $userId
     * @return int
     */
    public function getCount(?int $userId = null): int {
        return $this->ordersRepository->getCount($userId);
    }

    /**
     * Обновление статуса
     *
     * @param array $ids
     * @param string $status
     * @return bool
     */
    public function updateStatus(array $ids, string $status): bool {
        return $this->ordersRepository->updateStatus($ids, $status);
    }

    /**
     * Удаление
     *
     * @param array $ids
     * @return bool
     */
    public function delete(array $ids): bool {
        return $this->ordersRepository->delete($ids);
    }
}