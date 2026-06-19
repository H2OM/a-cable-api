<?php

namespace app\controllers\admin;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\admin\ProductsService;

/** Контроллер для управления товарами */
readonly class ProductsController {
    public function __construct(
        private ProductsService $productsService,
        private Request         $request
    ) {}

    /**
     * Добавление нового товара
     *
     * @throws ResponseException
     */
    public function addAction(): Response {
        $messages = $this->productsService->add($this->request->post(default: []));

        if(!empty($messages)) {
            return Response::json(data: [
                'success' => true,
                'message' => ResponseMessage::SUCCESS_UPDATE_DATA->value . PHP_EOL . $messages
            ]);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_UPDATE_DATA);
    }

    /**
     * Редактирование товара
     *
     * @return Response
     * @throws ResponseException
     */
    public function updateAction(): Response {
        $messages = $this->productsService->update($this->request->post(default: []));

        if(!empty($messages)) {
            return Response::json(data: [
                'success' => true,
                'message' => ResponseMessage::SUCCESS_UPDATE_DATA->value . PHP_EOL . $messages
            ]);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_UPDATE_DATA);
    }

    /**
     * Привязка вариаций к товару
     *
     * @return Response
     * @throws ResponseException
     */
    public function pairVariationsAction(): Response {
        $ids           = $this->request->input('ids');
        $variationsIds = $this->request->input('variations_ids');
        $oneWay        = (bool)$this->request->input('one_way');

        if (!is_array($ids) || !is_array($variationsIds)) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        if(!$this->productsService->pairVariations($ids, $variationsIds, $oneWay)) {
            throw new ResponseException(ResponseMessage::ERROR_ADD);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_ADD);
    }

    /**
     * Привязка связанных товаров
     *
     * @return Response
     * @throws ResponseException
     */
    public function pairRelatedAction(): Response {
        $ids        = $this->request->input('ids');
        $relatedIds = $this->request->input('related_ids');
        $oneWay     = (bool)$this->request->input('one_way');

        if (!is_array($ids) || !is_array($relatedIds)) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        if(!$this->productsService->pairRelated($ids, $relatedIds, $oneWay)) {
            throw new ResponseException(ResponseMessage::ERROR_ADD);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_ADD);
    }

    /**
     * Удаление товаров по id
     *
     * @return Response
     * @throws ResponseException
     */
    public function deleteAction(): Response {
        $ids = $this->request->input('ids');

        if(empty($ids)) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        $result = $this->productsService->deleteByIds($ids);

        if(!$result) {
            throw new ResponseException(ResponseMessage::ERROR_DELETE);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_REMOVE_ITEMS);
    }

    /**
     * Присваивание товарам статуса - хит продаж
     *
     * @return Response
     * @throws ResponseException
     */
    public function makeHitAction(): Response {
        return $this->changeHit(status: true);

    }

    /**
     * Удаление у товаров статуса - хит продаж
     *
     * @return Response
     * @throws ResponseException
     */
    public function excludeHitAction(): Response {
        return $this->changeHit(status: false);
    }

    /**
     * Смена статуса хит продаж у товара
     *
     * @param bool $status
     * @return Response
     * @throws ResponseException
     */
    private function changeHit(bool $status): Response  {
        $ids = $this->request->input('ids');

        if(empty($ids) || !is_array($ids)) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        $result = $this->productsService->changeHit(ids: $ids, status: $status);

        if(!$result) {
            throw new ResponseException(ResponseMessage::ERROR_UPDATE);
        }

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_UPDATE_DATA);
    }
}