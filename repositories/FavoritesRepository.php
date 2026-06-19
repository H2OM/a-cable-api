<?php

namespace app\repositories;


use app\core\Db;
use app\core\Hydrator;

/** Репозиторий по управлению избранными товарами */
readonly class FavoritesRepository {
    public function __construct(private Db $db) {}

    /**
     * Добавление
     *
     * @param int $userId
     * @param int $productId
     * @return int
     */
    public function add(int $userId, int $productId): int {
        return $this->db->query()
            ->table('favorites')
            ->insert(['user_id' => $userId, 'product_id' => $productId])
            ->affectedRows();
    }

    /**
     * Удаление
     *
     * @param int $userId
     * @param int $productId
     * @return int
     */
    public function remove(int $userId, int $productId): int {
        return $this->db->query()
            ->table('favorites')
                ->where('user_id', '=', $userId)
                ->where('product_id', '=', $productId)
                ->delete()
                ->affectedRows();
    }

    /**
     * Отчистка
     *
     * @param int $userId
     * @return bool
     */
    public function clear(int $userId): bool {
        return $this->db->query()
            ->table('favorites')
                ->where('user_id', '=', $userId)
                ->delete()
                ->execute();
    }
}
