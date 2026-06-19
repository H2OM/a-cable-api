<?php

namespace app\repositories;

use app\core\Db;
use app\core\Hydrator;

/** Репозиторий для управления фильтрами */
readonly class FiltersRepository {
    public function __construct(private Db $db, private Hydrator $hydrator) {}

    /**
     * Получение всех фильтров
     *
     * @param string $categoryCode
     * @return array
     */
    public function getFilters(string $categoryCode): array {
        return $this->db->fetchAll("
            WITH target_types AS (
                SELECT ct.id
                FROM categories_types ct
                JOIN categories c ON ct.category_id = c.id
                WHERE c.code = ?
            )
            SELECT
                filters.id,
                filters.filter AS filter_name,
                filters.code AS filter_code,
                filters.type,
                filters.position,
                CASE
                    WHEN filters.id = 5 THEN CONCAT(MIN(products.price), ',', MAX(products.price))
                    WHEN filters.id = 4 THEN brands.name
                    WHEN filters.id = 3 THEN categories_types.name
                    ELSE filters_values.value
                END AS value_name,
                CASE
                    WHEN filters.id = 5 THEN CONCAT(MIN(products.price), ',', MAX(products.price))
                    WHEN filters.id = 4 THEN brands.code
                    WHEN filters.id = 3 THEN categories_types.code
                    ELSE filters_values.code
                END AS value_code,
                CASE
                    WHEN filters.id = 5 THEN 'price_range'
                    WHEN filters.id = 4 THEN CONCAT('b_', brands.id)
                    WHEN filters.id = 3 THEN CONCAT('c_', categories_types.id)
                    ELSE filters_values.id
                END AS value_id
            FROM filters
            LEFT JOIN filters_values ON filters.id = filters_values.filter_id
            LEFT JOIN categories_filters ON filters.id = categories_filters.filter_id
            LEFT JOIN categories ON categories_filters.category_id = categories.id
            LEFT JOIN products ON products.category_type_id IN (SELECT id FROM target_types)
            LEFT JOIN brands ON filters.id = 4 AND products.brand_id = brands.id
            LEFT JOIN categories_types ON filters.id = 3 AND products.category_type_id = categories_types.id
            LEFT JOIN filters_values_products ON products.id = filters_values_products.product_id AND filters_values.id = filters_values_products.filter_value_id
            WHERE
                (categories.code = ? OR filters.id IN (1, 2, 3, 4, 5))
                AND (
                    (filters.id = 5 AND products.id IS NOT NULL) OR
                    (filters.id = 4 AND brands.id IS NOT NULL) OR
                    (filters.id = 3 AND categories_types.id IS NOT NULL) OR
                    (filters.id IN (1, 2) AND filters_values.id IS NOT NULL) OR
                    (filters.id NOT IN (1, 2, 3, 4, 5) AND filters_values.id IS NOT NULL AND filters_values_products.filter_value_id IS NOT NULL)
                )
            GROUP BY
                filters.id,
                IF(filters.id = 5, 'price', value_id)
            ORDER BY
                 filters.position ASC, value_name ASC;
        ", [$categoryCode, $categoryCode]);
    }

    /**
     * Получение id фильтров по коду
     *
     * @param array $codes
     * @return array
     */
    public function getFiltersIdsByCode(array $codes): array {
        return $this->db->query()
            ->table('filters')
            ->select(['id', 'code'])
            ->where('code', 'IN', $codes)
            ->get();
    }

    /**
     * Получение значений фильтров по их коду
     *
     * @param array $codes
     * @return array
     */
    public function getFiltersValuesByCode(array $codes): array {
        return $this->db->query()
            ->table('filters_values')
            ->select(['id', 'code', 'filter_id'])
            ->where('code', 'IN', $codes)
            ->get();
    }

    /**
     * Получение всех фильтров, с лимитом
     *
     * @param int $factor
     * @param int $limit
     * @return array
     */
    public function getAllByLimit(int $factor, int $limit): array {
        return $this->hydrator->decodeJson(
            $this->db->query()
                ->table('filters')
                ->select([
                    '*',
                    "(
                        SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                            'id', id, 
                            'value', value, 
                            'code', code, 
                            'filter_id', filter_id
                        )), JSON_ARRAY())
                        FROM filters_values
                        WHERE filters.id = filters_values.filter_id
                    ) AS 'values'"
                ])
                ->limit($limit, $factor * $limit)
                ->get(),
            ['values']
        );
    }

    /**
     * Получение количества
     *
     * @param int|null $categoryId
     * @return int
     */
    public function getCount(?int $categoryId): int {
        $sql = "SELECT COUNT(*) AS count FROM filters";
        $params = [];

        if ($categoryId !== null) {
            $sql .= " 
                JOIN categories_filters ON filters.id = categories_filters.filter_id 
                WHERE categories_filters.category_id = ?
            ";

            $params[] = $categoryId;
        }

        return (int)$this->db->fetchOne($sql, $params)['count'];

    }

    /**
     * Вставка новых фильтров
     *
     * @param array $filters
     * @return bool
     */
    public function insert(array $filters): bool {
        return $this->db->query()
            ->table('filters')
            ->insert($filters)
            ->execute();
    }

    /**
     * Вставка новых значений фильтров
     *
     * @param array $filtersValues
     * @return string|false
     */
    public function insertFiltersValues(array $filtersValues): string|false {
        return $this->db->query()
            ->table('filters_values')
            ->insert($filtersValues)
            ->execute();
    }

    /**
     * Вставка новых связей значений фильтра и id товара
     *
     * @param array $valuesProducts
     * @return bool
     */
    public function insertValuesProducts(array $valuesProducts): bool {
        return $this->db->query()
            ->table('filters_values_products')
            ->insert($valuesProducts)
            ->execute();
    }

    /**
     * Удаление по id
     *
     * @param array $ids
     * @return bool
     */
    public function deleteByIds(array $ids): bool {
        return $this->db->query()
            ->table('filters')
            ->where('id', 'IN', $ids)
            ->delete()
            ->execute();
    }
}