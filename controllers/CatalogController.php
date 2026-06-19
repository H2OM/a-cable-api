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
        $page = $this->request->get('page');
        $limit = $this->request->get('limit');
        $filters_params = $this->request->get();

        if(empty($filters_params['category'])) {
            return Response::jsonError(message: ResponseMessage::ERROR_DATA);
        }

        unset($filters_params['page'], $filters_params['limit']);

        $catalog = $this->productsService->getCatalogByFilters($filters_params);
        $filters = $this->filtersService->getFiltersGroupByCode($filters_params['category']);
        $category = $catalog[0]['category_parent'] ?? null;

        if(empty($category)) {
            $category = $this->categoriesService->getByCode($filters_params['category'])['title'] ?? null;
        }

        return Response::jsonSuccess(data: [
            'category_title' => $category,
            'catalog' => $catalog,
            'filters' => $filters
        ]);
    }
}