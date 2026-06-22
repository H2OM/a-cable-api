<?php

namespace app\services\admin;

use app\repositories\UserRepository;

/** Сервис для управления пользователями */
readonly class UserService {
    public function __construct(private UserRepository $userRepository) {}

    /**
     * Получения кол-ва
     *
     * @return int
     */
    public function getCount(): int {
        return $this->userRepository->getCount();
    }
}