<?php

namespace app\services\admin;

use app\repositories\FiltersRepository;

/** Сервис для управления фильтрами */
readonly class FiltersService {
    public function __construct(private FiltersRepository $filtersRepository) {}

    /**
     * Получение всех фильтров, с лимитом
     *
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAllByLimit(int $page, int $limit): array {
        if($limit > 100) $limit = 100;

        return $this->filtersRepository->getAllByLimit(factor: $page - 1, limit: $limit);
    }

    public function getCount(?int $categoryId): int {
        return $this->filtersRepository->getCount($categoryId);
    }

    /**
     * Вставка новых фильтров
     *
     * @param array $filters
     * @return string|false
     */
    public function insert(array $filters): string|false {
        return $this->filtersRepository->insert($filters);
    }

    /**
     * Вставка новых значений фильтров
     *
     * @param array $filtersValues
     * @return string|false
     */
    public function insertFiltersValues(array $filtersValues): string|false {
        return $this->filtersRepository->insertFiltersValues($filtersValues);

    }

    /**
     * Вставка новых связей значений фильтра и id товара
     *
     * @param array $filtersValuesProducts
     * @return string|false
     */
    public function insertFiltersValuesProducts(array $filtersValuesProducts): string|false {
        return $this->filtersRepository->insertValuesProducts($filtersValuesProducts);
    }

    /**
     * Удаление по id
     *
     * @param array $ids
     * @return bool
     */
    public function deleteByIds(array $ids): bool {
        return $this->filtersRepository->deleteByIds($ids);
    }
}