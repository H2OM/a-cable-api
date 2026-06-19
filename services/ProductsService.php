<?php

namespace app\services;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Session;
use app\repositories\ProductsRepository;

/** Сервис для получения товаров */
readonly class ProductsService {
    public function __construct(
        private ProductsRepository $productsRepository,
        private FavoritesService   $favoritesService,
        private Session            $session,
    ) {}

    /**
     * Получение всех товаров, с лимитом
     *
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAllByLimit(int $page, int $limit): array {
        if($limit > 100) $limit = 100;

        return $this->productsRepository->getAllByLimit(factor: $page - 1, limit: $limit);
    }

    /**
     * Получение количества товаров, с возможностью подсчета по подкатегории и бренду
     *
     * @param int|null $categoryTypeId
     * @param int|null $brandId
     * @return int
     */
    public function getCount(?int $categoryTypeId = null, ?int $brandId = null): int {
        return $this->productsRepository->getCount($categoryTypeId, $brandId);
    }

    /**
     * Получение популярных и скидочных товаров
     *
     * @return array[]
     */
    public function getHitAndSales(): array {
        $products = $this->productsRepository->getHitAndSales();

        $hit = [];
        $sales = [];

        foreach($products as $product) {
            if($product['hit'] === '1') {
                $hit[] = $product;
            }
            if((float)$product['price_old'] > 0) {
                $sales[] = $product;
            }
        }

        return ['hit' => $hit, 'sales' => $sales];
    }

    /**
     * Получение каталога товаров с фильтрацией
     *
     * @param array $filters
     * @return array
     */
    public function getCatalogByFilters(array $filters): array {
        $catalog = $this->productsRepository->getByFilters($filters);

        if(count($catalog) === 0 || !isset($filters['favorite'])) return $catalog;

        $favorites = $this->session->get('favorites');

        if(empty($favorites)) $favorites = $this->favoritesService->get();

        $showOnlyFavorites = filter_var($filters['favorite'], FILTER_VALIDATE_BOOLEAN);

        return array_filter($catalog, function ($product) use ($favorites, $showOnlyFavorites) {
            $isFavorite = in_array($product['id'], $favorites);

            return $showOnlyFavorites ? $isFavorite : !$isFavorite;
        });
    }

    /**
     * Получение отдельного товара по id
     *
     * @param int $id
     * @return array
     * @throws ResponseException
     */
    public function getProductById(int $id): array {
        $product = $this->productsRepository->getProductDetailsById($id);

        if(!$product) {
            throw new ResponseException(ResponseMessage::ERROR_GET_DATA);
        }

        if(count($product) === 0) {
            throw new ResponseException(ResponseMessage::ERROR_PRODUCT_NOT_FOUND, 404);
        }

        $localFilters = [];

        foreach ($product['local_filters'] as $filter) {
            if(!isset($localFilters[$filter['filter_code']]['values'])) {
                $localFilters[$filter['filter_code']] = [
                    'name' => $filter['filter_name'],
                    'code' => $filter['filter_code'],
                    'values' => []
                ];
            }

            $localFilters[$filter['filter_code']]['values'][] = [
                'id' => $filter['value_id'],
                'name' => $filter['value_name'],
                'code' => $filter['value_code'],
                'product_id' => $filter['product_id'],
            ];
        }

        $product['local_filters'] = array_values($localFilters);

        return $product;
    }

    /**
     * Получение связанных товаров по id
     *
     * @param int $id
     * @return array
     */
    public function getRelatedById(int $id): array {
        return $this->productsRepository->getRelatedById($id);
    }

    /**
     * Поиск товаров по строке запроса
     *
     * @param string $query
     * @return array
     */
    public function searchByQuery(string $query): array {
        return $this->productsRepository->searchByQuery($query);
    }
}