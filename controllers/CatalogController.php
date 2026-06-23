<?php

namespace app\controllers;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\CategoriesService;
use app\services\FiltersService;
use app\services\ProductsService;


/** Контроллер для получения каталога */
readonly class CatalogController {
    public function __construct(
        private Request           $request,
        private FiltersService    $filtersService,
        private ProductsService   $productsService,
        private CategoriesService $categoriesService
    ) {}

    /**
     * Получение каталога товара
     *
     * @return Response
     * @throws ResponseException
     */
    public function getAction(): Response {
        $page = $this->request->get('page') ?? 1;
        $limit = $this->request->get('limit') ?? 32;
        $filters_params = $this->request->get();

        if(empty($filters_params['category'])) {
            return Response::jsonError(message: ResponseMessage::ERROR_DATA);
        }

        $categoryTypeCode = $filters_params['category_type'] ?? null;

        unset($filters_params['page'], $filters_params['limit'], $filters_params['category_type']);

        $count = $this->productsService->getCountByFilters($filters_params);
        $catalog = $this->productsService->getCatalogByFilters((int)$page, (int)$limit, $filters_params);
        $filters = $this->filtersService->getFiltersGroupByCode(
            categoryCode: $filters_params['category'],
            categoryTypeCode: $categoryTypeCode
        );
        $category = $catalog[0]['category_parent'] ?? null;

        if(empty($category)) {
            $category = $this->categoriesService->getByCode($filters_params['category'])['title'] ?? null;
        }

        return Response::jsonSuccess(data: [
            'category_title' => $category,
            'catalog' => $catalog,
            'filters' => $filters,
            'count' => $count
        ]);
    }

    /**
     * Получение кол-во товаров в каталоге с заданными фильтрами
     *
     * @return Response
     */
    public function getCountAction(): Response {
        $filters_params = $this->request->get();

        if(empty($filters_params['category'])) {
            return Response::jsonError(message: ResponseMessage::ERROR_DATA);
        }

        $count = $this->productsService->getCountByFilters($filters_params);

        return Response::jsonSuccess(data: $count);
    }
}