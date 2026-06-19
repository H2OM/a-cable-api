<?php

namespace app\services;

use app\core\Env;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

/** Сервис для отправки писем */
readonly class MailService {
    private Mailer $mailer;

    public function __construct(private Env $env) {
        $transport = Transport::fromDsn(
            sprintf(
                'smtp://%s:%s@%s:%s',
                $env->get('MAIL_USER'),
                $env->get('MAIL_PASSWORD'),
                $env->get('MAIL_HOST'),
                $env->get('MAIL_PORT')
            )
        );

        $this->mailer = new Mailer($transport);
    }

    /**
     * Отправка письма
     *
     * @param string $to
     * @param string $subject
     * @param string $html
     * @return void
     * @throws TransportExceptionInterface
     */
    public function send(
        string $to,
        string $subject,
        string $html
    ): void {
        $email = (new Email())
            ->from($this->env->get('MAIL_BASE_FROM'))
            ->to($to)
            ->subject($subject)
            ->html($html);

        $this->mailer->send($email);
    }
}