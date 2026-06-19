<?php

namespace app\services\admin;

use app\repositories\CategoriesRepository;

/** Сервис для управления категориями */
readonly class CategoriesService {
    public function __construct(private CategoriesRepository $categoriesRepository) {}

    /**
     * Прикрепить фильтры к категориям
     *
     * @param array $values
     * @return bool
     */
    public function addFiltersToCategories(array $values): bool {
        return $this->categoriesRepository->addFiltersToCategories($values);
    }
}