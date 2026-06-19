<?php

namespace app\controllers\admin;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\admin\BrandsService;

/** Контроллер для управления брендами */
readonly class BrandsController {
    public function __construct(private Request $request, private BrandsService $brandsService) {}

    /**
     * Получение всех брендов
     *
     * @return Response
     */
    public function getAllAction(): Response {
        $page = (int)$this->request->get('page', 1);
        $limit = (int)$this->request->get('limit', 20);

        $products = $this->brandsService->getAllByLimit($page, $limit);

        return Response::jsonSuccess(data: $products);
    }


    /**
     * Получение кол-во брендов
     *
     * @return Response
     */
    public function getCountAction(): Response {
        $count = $this->brandsService->getCount();

        return Response::jsonSuccess(data: $count);
    }

    /**
     * Удаление брендов
     *
     * @return Response
     * @throws ResponseException
     */
    public function deleteAction(): Response {
        $ids = $this->request->input('ids');

        if(!is_array($ids)) {
            throw new ResponseException(ResponseMessage::ERROR_DATA);
        }

        if(!$this->brandsService->delete($ids)) {
            throw new ResponseException(ResponseMessage::ERROR_DELETE);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_REMOVE_ITEMS);
    }

    /**
     * Безопасное удаление брендов
     *
     * @return Response
     * @throws ResponseException
     */
    public function safeDeleteAction(): Response {
        $ids = $this->request->input('ids');

        if(!is_array($ids)) {
            throw new ResponseException(ResponseMessage::ERROR_DATA);
        }

        if(!$this->brandsService->safeDelete($ids)) {
            throw new ResponseException(ResponseMessage::ERROR_DELETE);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_REMOVE_ITEMS);
    }
}