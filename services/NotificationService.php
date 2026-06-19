<?php

namespace app\services;

use app\core\Env;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/** Сервис для отправки уведомлений */
readonly class NotificationService {
    public function __construct(
        private OrdersService  $ordersService,
        private LayoutsService $layoutsService,
        private MailService    $mailService,
        private Env            $env
    ) {
    }

    /**
     * Отправка информации о заказе на почту
     *
     * @param array $order
     * @param array $user
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendOrderInfo(array $order, array $user): void {
        $siteUrl = $this->env->get('SYSTEM_MAIN');

        $layoutData = [
            'main_info' => $this->ordersService->formatOrderFields($order),
            'order_url' => $siteUrl . '/order/' . $order['id'],
            'site_url'  => $siteUrl,
            'products'  => $order['products'],
            'user'      => [
                'first_name' => $user['first_name'] ?? '',
                'second_name' => $user['second_name'] ?? '',
                'personal_url' => $user['id'] ? $siteUrl . '/personal' : ''
            ],
        ];



        $this->mailService->send(
            to: $user['email'],
            subject: "Ваш заказ №{$order['number']} принят в обработку",
            html: $this->layoutsService->getMailLayout(template: 'order', data: $layoutData),
        );
    }
}