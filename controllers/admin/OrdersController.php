<?php

namespace app\controllers\admin;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\admin\OrdersService;

/** Контроллер для управления заказами */
readonly class OrdersController {
    public function __construct(private Request $request, private OrdersService $ordersService) {}

    /**
     * Получение всех заказов
     *
     * @return Response
     */
    public function getAllAction(): Response {
        $page = (int)$this->request->get('page', 1);
        $limit = (int)$this->request->get('limit', 20);

        $orders = $this->ordersService->getAllByLimit($page, $limit);

        return Response::jsonSuccess(data: $orders);
    }

    /**
     * Получения кол-ва товаров
     *
     * @return Response
     */
    public function getCountAction(): Response {
        $userId = $this->request->get('user_id');

        $count = $this->ordersService->getCount($userId);

        return Response::jsonSuccess(data: $count);
    }

    /**
     * Обновление статуса у заказов
     *
     * @return Response
     * @throws ResponseException
     */
    public function updateStatusAction(): Response {
        $status = $this->request->input('status');
        $ids = $this->request->input('ids');

        if(!is_array($ids) || empty($status)) {
            throw new ResponseException(ResponseMessage::ERROR_DATA);
        }

        if(!$this->ordersService->updateStatus($ids, $status)) {
            throw new ResponseException(ResponseMessage::ERROR_UPDATE_STATUS);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_UPDATE_STATUS);
    }

    /**
     * Удаление
     *
     * @return Response
     * @throws ResponseException
     */
    public function deleteAction(): Response {
        $ids = $this->request->input('ids');

        if(empty($ids) || !is_array($ids)) {
            throw new ResponseException(ResponseMessage::ERROR_DATA);
        }

        if(!$this->ordersService->delete($ids)) {
            throw new ResponseException(ResponseMessage::ERROR_DELETE);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_REMOVE_ORDERS);
    }

}