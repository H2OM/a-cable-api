<?php

namespace app\services\admin;

use app\repositories\BannersRepository;

/** Сервис для управления баннерами */
readonly class BannersService {
    public function __construct(private BannersRepository $bannersRepository) {}

    /**
     * Получение всех
     *
     * @return array
     */
    public function getAll(): array {
        return $this->bannersRepository->getAll();
    }
}