<?php

namespace app\controllers\admin;

use app\core\Request;
use app\core\Response;
use app\services\admin\BrandsService;
use app\services\admin\CategoriesService;
use app\services\admin\OrdersService;
use app\services\admin\StatisticService;
use app\services\admin\UserService;
use app\services\admin\CallbacksService;
use app\services\ProductsService;
use DateMalformedPeriodStringException;
use DateMalformedStringException;

/** Контроллер для получения статистики */
readonly class StatisticController {
    public function __construct(
        private CategoriesService $categoriesService,
        private StatisticService  $statisticService,
        private CallbacksService  $callbacksService,
        private ProductsService   $productsService,
        private BrandsService     $brandsService,
        private OrdersService     $ordersService,
        private UserService       $userService,
        private Request           $request
    ) {}

    /**
     * Получение всей статистики
     *
     * @return Response
     * @throws DateMalformedStringException
     * @throws DateMalformedPeriodStringException
     */
    public function getAction(): Response {
        $periodFrom = $this->request->get('from') ?? date('Y-m-d');
        $periodTo = $this->request->get('to') ?? date('Y-m-d');

        $metrics   = $this->statisticService->getMetric($periodFrom, $periodTo);
        $categories = $this->categoriesService->getTypesCount();
        $products   = $this->productsService->getCount();
        $brands     = $this->brandsService->getCount();
        $users      = $this->userService->getCount();
        $callbacks = $this->callbacksService->getStatusesCount();
        $orders    = $this->ordersService->getStatusesCount();

        return Response::jsonSuccess(data: [
            'metrics' => $metrics,
            'totals' => [
                'products' => $products,
                'users' => $users,
                'brands' => $brands,
                'orders' => $orders,
                'categories' => $categories
            ],
            'orders' => $orders,
            'callbacks' => $callbacks
        ]);
    }
}