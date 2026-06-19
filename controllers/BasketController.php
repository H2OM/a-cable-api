<?php

namespace app\controllers;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\AuthService;
use app\services\BasketService;

/** Управление корзиной */
readonly class BasketController {
    public function __construct(
        private BasketService $basketService,
        private Request       $session
    ) {}

    /**
     * Получение корзины покупок пользователя
     *
     * @return Response
     */
    public function getAction(): Response {
        return Response::jsonSuccess(data: $this->basketService->get());
    }

    /**
     * Добавление товара
     *
     * @return Response
     * @throws ResponseException
     */
    public function addAction(): Response {
        $id    = $this->session->input('id');
        $count = (int)$this->session->input('count');

        if(!$id) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_ENOUGH_DATA, status: 403);
        }

        if($count <= 0) {
            $count = 1;
        }

        $basket = $this->basketService->add(
            id: $id,
            count: $count
        );

        return Response::jsonSuccess(data: $basket, message: ResponseMessage::SUCCESS_ADD_BASKET);
    }

    /**
     * Уменьшение кол-во товара
     *
     * @return Response
     * @throws ResponseException
     */
    public function decrementAction(): Response {
        $id = $this->session->input('id');

        if(!$id) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_ENOUGH_DATA, status: 403);
        }

        $basket = $this->basketService->decrement(id: $id);

        return Response::jsonSuccess(data: $basket, message: ResponseMessage::SUCCESS_REMOVE_BASKET);
    }

    /**
     * Удаление товара
     *
     * @return Response
     * @throws ResponseException
     */
    public function removeAction(): Response {
        $id = $this->session->input('id');

        $basket = $this->basketService->remove(id: $id);

        return Response::jsonSuccess(data: $basket, message: ResponseMessage::SUCCESS_REMOVE_BASKET);
    }

    /**
     * Установка кол-во товара
     *
     * @return Response
     * @throws ResponseException
     */
    public function setCountAction(): Response {
        $id    = $this->session->input('id');
        $count = (int)$this->session->input('count');

        if(!$id || $count <= 0) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_ENOUGH_DATA, status: 403);
        }

        $basket = $this->basketService->setCount(id: $id, count: $count);

        return Response::jsonSuccess(data: $basket);
    }

    /**
     * Отчистка корзины
     *
     * @return Response
     */
    public function clearAction(): Response {
        return Response::jsonSuccess(data: $this->basketService->clear(), message: ResponseMessage::SUCCESS_CLEAR_BASKET);
    }
}