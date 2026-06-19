<?php

namespace app\controllers;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\AuthService;
use app\services\BasketService;
use app\services\NotificationService;
use app\services\OrdersService;
use app\services\UserService;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/** Контролер для получения заказов и частичного управления ими */
readonly class OrdersController {
    public function __construct(
        private NotificationService $notificationService,
        private OrdersService       $ordersService,
        private BasketService       $basketService,
        private UserService         $userService,
        private AuthService         $authService,
        private Request             $request,
    ) {
    }

    /**
     * Создание нового заказа
     *
     * @return Response
     * @throws ResponseException
     * @throws Exception
     */
    public function newAction(): Response {
        $data = $this->request->input();
        $userId = $this->authService->id();

        if (!$userId && !$data['user']) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        if (!$userId) {
            $data['user']['password'] = $data['user']['password_confirmed'] = 'TEMP_' . md5(uniqid());
            $user = $this->userService->insert($data['user']);

            if (!$user) {
                return Response::jsonError(message: ResponseMessage::ERROR_AUTH);
            }

            $this->authService->login($user);

            $userId = $user['id'];
        }

        $orderId = $this->ordersService->new([
            ...$data,
            'user_id' => $userId,
            'products' => $this->basketService->get(),
        ]);

        $order = $this->ordersService->get($orderId);
        $user = $this->authService->user();

        if (!empty($data['user']['password'])) {
            $user['password'] = $data['user']['password'];
        }

        try {
            $this->notificationService->sendOrderInfo(order: $order, user: $user);
        } catch (TransportExceptionInterface $e) {
        }

        return Response::jsonSuccess(data: [
            'order_id' => $orderId,
            'user' => $this->authService->user(),
        ], message: ResponseMessage::SUCCESS_CREATE_ORDER);
    }

    /**
     * Получение одного заказа
     *
     * @return Response
     */
    public function getAction(): Response {
        $id = (int)$this->request->get('id');

        if (empty($id) || !is_numeric($id)) {
            return Response::jsonError(message: ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        $order = $this->ordersService->get($id);

        if (empty($order)) {
            return Response::jsonError(message: ResponseMessage::ERROR_ORDER_NOT_FOUND);
        }

        return Response::jsonSuccess(data: $order);
    }

    /**
     * Получение всех способов доставки
     *
     * @return Response
     */
    public function getDeliveryTypesAction(): Response {
        $deliveryTypes = $this->ordersService->getDeliveryTypes();

        return Response::jsonSuccess(data: $deliveryTypes);
    }

    /**
     * Получение всех способов оплаты
     *
     * @return Response
     */
    public function getPaymentTypesAction(): Response {
        $paymentTypes = $this->ordersService->getPaymentTypes();

        return Response::jsonSuccess(data: $paymentTypes);
    }
}