<?php

namespace app\services\admin;

use app\core\Env;
use Firebase\JWT\JWT;

/** Сервис для управления jwt токенами */
class JWTService {
    private const int HOURS_TO_EXPIRE = 2;

    public function __construct(private readonly Env $env) {}

    /**
     * Генерация токена
     *
     * @param array|null $data
     * @return string
     */
    public function generateToken(?array $data): string {
        $issuedAt = time();
        $expireAt = $issuedAt + self::HOURS_TO_EXPIRE * 3600;

        $payload = [
            'iss' => $this->env->get('JWT_ISSUER'), // Кто выпустил токен (Issuer)
            'aud' => $this->env->get('JWT_AUDIENCE'), // Для кого выпущен (Audience)
            'iat' => $issuedAt, // Время выпуска
            'exp' => $expireAt, // Время окончания
            'data' => $data
        ];

        return JWT::encode($payload, $this->env->get('JWT_SECRET_KEY'), 'HS256');
    }
}