<?php

namespace app\services;

use app\repositories\CategoriesRepository;

/** Сервис для получения категорий */
readonly class CategoriesService {
    public function __construct(private CategoriesRepository $categoriesRepository) {}

    /**
     * Получение всех категорий
     *
     * @return array
     */
    public function getAll(): array {
        return $this->categoriesRepository->getAll();
    }

    /**
     * Получить по коду
     *
     * @param string $code
     * @return array|null
     */
    public function getByCode(string $code): ?array {
        return $this->categoriesRepository->getByCode($code);
    }

    /**
     * Поиск подтипов категорий по строке запроса
     *
     * @param string $query
     * @return array
     */
    public function searchTypeByQuery(string $query): array {
        return $this->categoriesRepository->searchTypeByQuery($query);
    }

    /**
     * Получить основную категорию по id подкатегории
     *
     * @param int $id
     * @return array|null
     */
    public function getMainCategoryByTypeId(int $id): ?array {
        return $this->categoriesRepository->getMainCategoryByTypeId($id);
    }
}