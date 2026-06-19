<?php

namespace app\controllers\admin;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\admin\AdminService;
use app\services\admin\JWTService;

/** Контроллер для управления авторизацией в админ-панели */
readonly class AuthController {
    public function __construct(
        private AdminService $adminService,
        private JWTService   $JWTService,
        private Request      $request
    ) {}

    /**
     * Логирование
     *
     * @return Response
     * @throws ResponseException
     */
    public function loginAction(): Response {
        $login = $this->request->input('login');
        $password = $this->request->input('password');

        if(empty($login) || empty($password)) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        $user = $this->adminService->get(login: $login, password: $password);
        $token = $this->JWTService->generateToken($user);

        return Response::jsonSuccess(data: [
            'user' => $user,
            'token' => $token
        ], message: ResponseMessage::SUCCESS_AUTH);
    }

    /**
     * Проверка авторизации (попадание в этот экшен = авторизирован)
     *
     * @return Response
     */
    public function checkAction(): Response {
        return Response::jsonSuccess(data: $this->adminService->getCurrent(), message: ResponseMessage::USER_AUTH);
    }
}