<?php

namespace app\controllers;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\ProductsService;

/** Контроллер для получения товаров */
readonly class ProductsController {
    public function __construct(
        private ProductsService $productsService,
        private Request         $request
    ) {}

    /**
     * Получение всех товаров
     *
     * @return Response
     */
    public function getAllAction(): Response {
        $page = (int)$this->request->get('page', 1);
        $limit = (int)$this->request->get('limit', 20);

        $products = $this->productsService->getAllByLimit($page, $limit);

        return Response::jsonSuccess(data: $products);
    }

    /**
     * Получение отдельного товара
     *
     * @return Response
     * @throws ResponseException
     */
    public function getAction(): Response {
        $id = (int)$this->request->get('id');

        if(!$id || !is_numeric($id)) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_ENOUGH_DATA, status: 403);
        }

        $product = $this->productsService->getProductById($id);
        $relatedProducts = $this->productsService->getRelatedById($id);

        return Response::jsonSuccess(data: [
            'product' => $product,
            'related' => $relatedProducts,
        ]);
    }

    /**
     * Получение количества товаров
     *
     * @return Response
     */
    public function getCountAction(): Response {
        $categoryTypeId = $this->request->get('category_type_id');
        $brandId = $this->request->get('brand_id');

        $count = $this->productsService->getCount(
            categoryTypeId: (int)$categoryTypeId ?: null,
            brandId: (int)$brandId ?: null
        );

        return Response::jsonSuccess(data: $count);
    }

    /**
     * Поиск id товаров
     *
     * @return Response
     * @throws ResponseException
     */
    public function searchAction(): Response {
        $query = $this->request->get('query');

        if(empty($query)) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        $findProducts = $this->productsService->searchByQuery($query);

        return Response::jsonSuccess(data: $findProducts);
    }
}