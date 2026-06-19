<?php

namespace app\services\admin;

use app\repositories\CallbacksRepository;

/** Сервис для управления обратной связью */
readonly class CallbacksService {
    public function __construct(
        private CallbacksRepository $callbacksRepository
    ) {}

    /**
     * Получение всех запросов обратной связи, с лимитом
     *
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAllByLimit(int $page, int $limit): array {
        if($limit > 100) $limit = 100;

        $callbacks = $this->callbacksRepository->getAllByLimit(factor: $page - 1, limit: $limit);

        foreach($callbacks as &$callback) {
            foreach ($callback as $key => $value) {
                if(str_contains($key, 'user_') && $value !== null) {
                    $callback['user'][str_replace('user_', '', $key)] = $value;
                    unset($callback[$key]);

                } elseif (str_contains($key, 'user_')) {
                    unset($callback[$key]);
                }
            }
        }

        return $callbacks;
    }

    /**
     * Получение кол-ва всех запросов обратной связи
     *
     * @param int|null $userId
     * @return int
     */
    public function getCount(?int $userId = null): int {
        return $this->callbacksRepository->getCount($userId);
    }

    /**
     * Удаление по id
     *
     * @param array $ids
     * @return bool
     */
    public function deleteByIds(array $ids): bool {
        return $this->callbacksRepository->deleteByIds($ids);
    }
}