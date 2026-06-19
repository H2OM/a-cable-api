<?php

namespace app\middleware;

use app\core\enums\ResponseMessage;
use app\core\Env;
use app\core\exceptions\ResponseException;
use app\services\admin\AdminService;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AdminMiddleware implements MiddlewareInterface {
    /** Имя пространства защищенных классов */
    private const string NAMESPACE = "admin";

    public function __construct(
        private readonly AdminService $adminService,
        private readonly Env          $env
    ) {}

    /**
     * Получение имени пространства защищенных классов
     *
     * @return string
     */
    public function getNamespace(): string {
        return self::NAMESPACE;
    }

    /**
     * Проверка JWT-токена
     *
     * @param string|null $requiredPermission
     * @return bool
     * @throws ResponseException
     */
    public function handle(?string $requiredPermission = null): bool {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!str_starts_with($authHeader, 'Bearer ')) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_AUTH, 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $secretKey = $this->env->get('JWT_SECRET_KEY');
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            $adminData = (array)$decoded->data;
            $adminPermissions = array_column($adminData['permissions'] ?? [], 'code');

            if(
                $requiredPermission &&
                !in_array($requiredPermission, $adminPermissions) &&
                !in_array('*', $adminPermissions)
            ) {
                throw new ResponseException(ResponseMessage::ERROR_PERMISSIONS, 403);
            }

            $this->adminService->setCurrent(userData: $adminData);

            return true;
        } catch (ResponseException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ResponseException(ResponseMessage::ERROR_TOKEN, 401);
        }
    }
}