<?php

namespace app\controllers\admin;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\admin\CallbacksService;

/** Контролер для управления запросами обратной связи */
readonly class CallbacksController {
    public function __construct(private Request $request, private CallbacksService $callbacksService) {}

    public function getAllAction(): Response {
        $page = (int)$this->request->get('page', 1);
        $limit = (int)$this->request->get('limit', 30);

        $callbacks = $this->callbacksService->getAllByLimit($page, $limit);

        return Response::jsonSuccess(data: $callbacks);
    }

    /**
     * Получения кол-ва запросов обратной связи
     *
     * @return Response
     */
    public function getCountAction(): Response {
        $userId = $this->request->get('user_id');

        $count = $this->callbacksService->getCount($userId);

        return Response::jsonSuccess(data: $count);
    }

    /**
     * Удаление
     *
     * @return Response
     * @throws ResponseException
     */
    public function deleteAction(): Response {
        $ids = $this->request->input('ids', []);

        $result = $this->callbacksService->deleteByIds($ids);

        if(!$result) {
            throw new ResponseException(ResponseMessage::ERROR_DELETE);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_REMOVE_ITEMS);
    }
}