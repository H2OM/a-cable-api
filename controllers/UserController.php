<?php

namespace app\controllers;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\AuthService;
use app\services\UserService;
use Exception;

/** Контролер для управления пользователями */
readonly class UserController {
    public function __construct(
        private UserService $userService,
        private AuthService $authService,
        private Request     $request
    ) {}

    /**
     * Проверка авторизации
     *
     * @return Response
     */
    public function isAuthAction(): Response {
        return Response::jsonSuccess(data: $this->authService->check());
    }

    /**
     * Авторизация
     *
     * @return Response
     * @throws ResponseException
     */
    public function signInAction(): Response {
        if($this->authService->check()) {
            return Response::jsonSuccess(data: $this->authService->user(), message: ResponseMessage::USER_ALREADY_AUTH);
        }

        $phone = $this->request->input('phone');
        $password = $this->request->input('password');

        if(empty($phone) || empty($password)) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        $user = $this->userService->signIn(
            password: $password,
            phone: $phone
        );

//      TODO При авторизации добавлять избранное из сессию в базу

        $this->authService->login(userData: $user);

        return Response::jsonSuccess(data: $this->authService->user(), message: ResponseMessage::SUCCESS_AUTH);
    }

    /**
     * Регистрация
     *
     * @return Response
     * @throws Exception
     */
    public function signUpAction(): Response {
        if($this->authService->check()) {
            return Response::jsonSuccess(data: $this->authService->user(), message: ResponseMessage::USER_ALREADY_AUTH);
        }

        $user = $this->userService->insert($this->request->input());

//      TODO При авторизации добавлять избранное из сессию в базу

        $this->authService->login(userData: $user);

        return Response::jsonSuccess(data: $this->authService->user(), message: ResponseMessage::SUCCESS_AUTH);
    }

    /**
     * Деавторизация
     *
     * @return Response
     */
    public function logOutAction(): Response {
        if(!$this->authService->check()) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_AUTH, status: 401);
        }

        $this->authService->logout();

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_LOGOUT);
    }

    /**
     * Получение
     *
     * @return Response
     */
    public function getAction(): Response {
        if(!$this->authService->check()) {
            return Response::jsonSuccess(message: ResponseMessage::ERROR_NOT_AUTH);
        }

        return Response::jsonSuccess(data: $this->authService->user());
    }

    /**
     * Редактирование
     *
     * @return Response
     * @throws ResponseException
     */
    public function editAction(): Response {
        if(!$this->authService->check()) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_AUTH, status: 401);
        }

        $updatedUser = $this->userService->edit(
            userData: $this->request->input(),
            userId: $this->authService->id()
        );

        $this->authService->login(userData: $updatedUser + $this->authService->user());

        return Response::jsonSuccess(data: $this->authService->user(), message: ResponseMessage::SUCCESS_EDIT);
    }

    /**
     * Получение всех заказов
     *
     * @return Response
     */
    public function getOrdersAction(): Response {
        if(!$this->authService->check()) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_AUTH, status: 401);
        }

        $orders = $this->userService->getOrders(userId: $this->authService->id());

        return Response::jsonSuccess(data: $orders);
    }
}
