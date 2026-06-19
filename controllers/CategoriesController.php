<?php

namespace app\controllers;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\CategoriesService;

/** Контроллер для получения категорий */
readonly class CategoriesController {
    public function __construct(private Request $request, private CategoriesService $categoriesService) {}

    /**
     * Получение всех категорий
     *
     * @return Response
     */
    public function getAllAction(): Response {
        $categories = $this->categoriesService->getAll();

        return Response::jsonSuccess(data: $categories);
    }

    /**
     * Поиск подтипов категорий по строке запроса
     *
     * @return Response
     * @throws ResponseException
     */
    public function searchTypesAction(): Response {
        $query = $this->request->get('query');

        if(empty($query)) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_ENOUGH_DATA);

        }
        $categoriesTypes = $this->categoriesService->searchTypeByQuery($query);

        return Response::jsonSuccess(data: $categoriesTypes);
    }
}