<?php

namespace app\services;

use app\repositories\MainRepository;

/** Сервис для управления главной информацией */
readonly class MainService {
    public function __construct(private MainRepository $mediaRepository) {}

    /**
     * Получение данных из таблицы 'новости'
     *
     * @return array
     */
    public function getNews(): array {
        return $this->mediaRepository->getNews();
    }
}