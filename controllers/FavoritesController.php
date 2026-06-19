<?php

namespace app\controllers;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\FavoritesService;

/** Контроллер для управления избранным */
readonly class FavoritesController {
    public function __construct(
        private FavoritesService $favoritesService,
        private Request          $request
    ) {}

    /**
     * Получение
     *
     * @return Response
     */
    public function getAction(): Response {
        $favorites = $this->favoritesService->get();

        return Response::jsonSuccess(data: $favorites);
    }

    /**
     * Добавление
     *
     * @return Response
     * @throws ResponseException
     */
    public function addAction(): Response {
        $productId = (int)$this->request->get('product_id');

        if (empty($productId) || !is_numeric($productId)) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_ENOUGH_DATA, status: 403);
        }

        $favorites = $this->favoritesService->add(productId: $productId);

        return Response::jsonSuccess(data: $favorites, message: ResponseMessage::SUCCESS_ADD_FAVORITES);
    }

    /**
     * Удаление
     *
     * @return Response
     * @throws ResponseException
     */
    public function removeAction(): Response {
        $productId = (int)$this->request->get('product_id');

        if (empty($productId) || !is_numeric($productId)) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_ENOUGH_DATA, status: 403);
        }

        $favorites = $this->favoritesService->remove(productId: $productId);

        return Response::jsonSuccess(data: $favorites, message: ResponseMessage::SUCCESS_REMOVE_FAVORITES);
    }

    /**
     * Отчистка
     *
     * @return Response
     * @throws ResponseException
     */
    public function clearAction(): Response {
        $favorites = $this->favoritesService->clear();

        return Response::jsonSuccess(data: $favorites, message: ResponseMessage::SUCCESS_CLEAR_FAVORITES);
    }
}