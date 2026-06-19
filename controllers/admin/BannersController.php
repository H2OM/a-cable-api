<?php

namespace app\controllers\admin;

use app\core\Response;
use app\repositories\BannersRepository;

/** Контроллер для управления баннерами на сайте */
readonly class BannersController {
    public function __construct(private BannersRepository $bannersRepository) {}

    /**
     * Получение всех
     *
     * @return Response
     */
    public function getAllAction(): Response {
        $banners = $this->bannersRepository->getAll();

        return Response::jsonSuccess(data: $banners);
    }
}