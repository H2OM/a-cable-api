<?php

namespace app\repositories;

use app\core\Db;

/** Репозиторий для управления брендами */
readonly class BrandsRepository {
    public function __construct(private Db $db) {}

    /**
     * Получение всех брендов, с лимитом
     *
     * @param int $factor
     * @param int $limit
     * @return array
     */
    public function getAllByLimit(int $factor, int $limit): array {
        return $this->db->query()
            ->table('brands')
            ->select([
                '*',
                '(
                    SELECT COUNT(*) 
                    FROM products 
                    WHERE brands.id = products.brand_id
                ) AS products_count'
            ])
            ->limit($limit, $factor * $limit)
            ->get();
    }

    /**
     * Получение количества брендов
     *
     * @return int
     */
    public function getCount(): int {
        return (int)$this->db->query()
            ->table('brands')
            ->select('COUNT(*) AS count')
            ->first()['count'] ?? 0;
    }

    /**
     * Получение по коду
     *
     * @param string $code
     * @return array|null
     */
    public function getByCode(string $code): ?array {
        return $this->db->query()
            ->table('brands')
            ->where('code', '=', $code)
            ->first();
    }

    /**
     * Поиск бренда по строке запроса
     *
     * @param string $query
     * @return array
     */
    public function searchByQuery(string $query): array {
        return $this->db->query()
            ->table('brands')
            ->where('id', 'LIKE', "$query%")
            ->orWhere('name', 'LIKE', "%$query%")
            ->orWhere('code', 'LIKE', "%$query%")
            ->limit(10)
            ->get();
    }

    /**
     * Удаление брендов
     *
     * @param array $ids
     * @return bool
     */
    public function delete(array $ids): bool {
        return $this->db->query()
            ->table('brands')
            ->where('id', 'IN', $ids)
            ->delete()
            ->execute();
    }
}