<?php

namespace app\repositories;

use app\core\Db;

/** Репозиторий для управления обратной связью */
readonly class CallbacksRepository {
    public function __construct(private Db $db) {}

    /**
     * Получение всех запросов обратной связи, с лимитом
     *
     * @param int $factor
     * @param int $limit
     * @return array
     */
    public function getAllByLimit(int $factor, int $limit): array {
        return $this->db->fetchAll("
            SELECT 
                callbacks.*,
                users.id AS user_id, 
                users.first_name AS user_first_name, 
                users.second_name AS user_second_name,
                users.age AS user_age,
                users.gender AS user_gender,
                users.email AS user_email,
                users.phone AS user_phone
            FROM callbacks
            LEFT JOIN users ON callbacks.user_id = users.id
            LIMIT ?, ?
        ", [$factor * $limit, $limit]);
    }

    /**
     * Получение кол-во запросов обратной связи
     *
     * @param int|null $userId
     * @return int
     */
    public function getCount(?int $userId = null): int {
        $queryBuilder = $this->db->query()
            ->table('callbacks')
            ->select('COUNT(*) AS count');

        if ($userId !== null) {
            $queryBuilder->where(field: 'user_id', value: $userId);
        }

        return (int)$queryBuilder->first()['count'];
    }

    /**
     * Удаление по id
     *
     * @param array $ids
     * @return bool
     */
    public function deleteByIds(array $ids): bool {
        return $this->db->query()
            ->table('callbacks')
            ->where('id', 'IN', $ids)
            ->delete()
            ->execute();

    }
}