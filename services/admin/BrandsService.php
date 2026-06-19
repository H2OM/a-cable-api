<?php

namespace app\services\admin;

use app\repositories\BrandsRepository;
use app\repositories\ProductsRepository;

/** Сервис для управления брендами */
readonly class BrandsService {
    public function __construct(
        private BrandsRepository $brandsRepository,
        private ProductsRepository $productsRepository
    ) {}

    /**
     * Получение всех брендов, с лимитом
     *
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAllByLimit(int $page, int $limit): array {
        if($limit > 100) $limit = 100;

        return $this->brandsRepository->getAllByLimit(factor: $page - 1, limit: $limit);
    }

    /**
     * Получение количества брендов
     *
     * @return int
     */
    public function getCount(): int {
        return $this->brandsRepository->getCount();
    }

    /**
     * Удаление брендов
     *
     * @param array $ids
     * @return bool
     */
    public function delete(array $ids): bool {
        if(($index = array_search(4, $ids)) !== false) {
            unset($ids[$index]);
        }

        if(empty($ids)) return false;

        return $this->brandsRepository->delete($ids);
    }

    /**
     * Безопасное удаление брендов
     *
     * @param array $ids
     * @return bool
     */
    public function safeDelete(array $ids): bool {
        if(!$this->productsRepository->updateByBrands($ids, ['brand_id' => 4])) {
            return false;
        }

        return $this->delete($ids);
    }

}