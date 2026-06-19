<?php

namespace app\controllers;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\BrandsService;

/** Контроллер для получения брендов */
readonly class BrandsController {
    public function __construct(private Request $request, private BrandsService $brandsService) {}

    /**
     * Поиск по строке запроса
     *
     * @return Response
     * @throws ResponseException
     */
    public function searchAction(): Response {
        $query = $this->request->get('query');

        if(empty($query)) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_ENOUGH_DATA);

        }
        $brands = $this->brandsService->searchByQuery($query);

        return Response::jsonSuccess(data: $brands);
    }
}