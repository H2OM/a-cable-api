<?php

namespace app\services\admin;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Validator;
use app\repositories\UserRepository;

/** Сервис для управления админ-пользователями */
class AdminService {
    /** @var array $currentUser Текущий авторизированный пользователь */
    private array $current = [];

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Validator      $validator,

    ) {}

    /**
     * Получение текущего пользователя
     *
     * @return array
     */
    public function getCurrent(): array {
        return $this->current;
    }

    /**
     * Установка текущего пользователя
     *
     * @param array $userData
     * @return void
     */
    public function setCurrent(array $userData): void {
        $this->current = $userData;
    }

    /**
     * Получение пользователя по логину и паролю
     *
     * @param string $login
     * @param string $password
     * @return array
     * @throws ResponseException
     */
    public function get(string $login, string $password): array {
        $validateData = $this->validator->validate(
            data: [
                'login' => $login,
                'password' => $password,
            ],
            rules: [
                'login' => ['required', 'login'],
                'password' => ['required', 'password'],
            ]
        );

        if (!$validateData) {
            throw new ResponseException(ResponseMessage::ERROR_AUTH_LOGIN_DATA);
        }

        $user = $this->userRepository->getAdminByLogin($login);

        if(empty($user) || !password_verify($validateData['password'], $user['password'])) {
            throw new ResponseException(ResponseMessage::ERROR_AUTH_LOGIN_DATA);
        }

        unset($user['password']);
        unset($user['role_id']);

        return $user;
    }
}