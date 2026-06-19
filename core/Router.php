<?php
    namespace app\core;

    use app\core\enums\ResponseMessage;
    use app\core\exceptions\ResponseException;
    use app\middleware\AdminMiddleware;
    use Exception;
    use PDOException;

    /** Роутер. Маршрутизация. Обработка ответа. */
    class Router {
       private const string PROTECTED_ROUTES_PATH = __DIR__ . '/../config/protectedRoutes.php';

        /**
         * Определение маршрутизации
         *
         * @return void
         */
        public static function dispatchInit(): void {
            try {
                $controller = App::request()->server('API_CONTROLLER');
                $action = App::request()->server('API_ACTION');

                if($controller && $action) {
                    Router::dispatch(
                        controller: $controller,
                        action: $action
                    );
                } else {
                    Router::dispatchURI(App::request()->server('REQUEST_URI'));
                }
            } catch (PDOException $e) {
                if($_ENV['PHP_DEBUG']) {
                    $message = $e->getMessage();

                } else {
                    $message = 'Внутренняя ошибка сервера!';
                }

                Response::json(['error' => true, 'message' => $message], 500)->send();
            } catch (ResponseException $e) {
                Response::jsonError(message: $e->getResponseMessage(), status: $e->getCode() ?: 400)->send();

            } catch (Exception $e) {
                Response::json(['error' => true, 'message' => $e->getMessage()], 400)->send();
            }
        }

        /**
         * Маршрутизация
         *
         * @param string $controller
         * @param string $action
         * @return void
         * @throws Exception
         * @throws ResponseException
         */
        public static function dispatch(string $controller = '', string $action = ''): void {
            $namespace = "app\\controllers\\";
            $protectedRoutes = require self::PROTECTED_ROUTES_PATH;

            foreach ($protectedRoutes as $route => $config) {
                if(strtolower($controller) === $route) {
                    $middleware = App::container()->get($config['class']);
                    $requiredPermission = $config[strtolower($action)] ?? $config['base_permission'];

                    if($requiredPermission === false || $middleware->handle($requiredPermission)) {
                        $namespace .= $middleware->getNamespace() . '\\';
                        break;
                    }

                    throw new ResponseException(ResponseMessage::ERROR_AUTH, 401);
                }
            }

            $controller = self::routeControllerParse($controller) . 'Controller';
            $action = self::routeParse($action) . 'Action';

            $class = $namespace . $controller;

            if (!class_exists($class))
                throw new \Exception("Class not found", 400);

            $controller = App::container()->get($class);

            if (!method_exists($controller, $action))
                throw new \Exception("Action not found", 400);

            $response = $controller->$action();

            if ($response instanceof Response) {
                $response->send();

            } else {
                echo $response;
            }
        }

        /**
         * Маршрутизация с определением URI
         *
         * @param string $uri
         * @return void
         * @throws ResponseException
         * @throws Exception
         */
        private static function dispatchURI(string $uri): void {
            $uri = trim(parse_url($uri, PHP_URL_PATH), '/');
            $segments = explode('/', $uri);

            self::dispatch($segments[1] ?? '', $segments[2] ?? '');
        }

        /**
         * Форматирование маршрута контроллера
         * >**admin-products => Products**
         *
         * @param string $route
         * @return string
         */
        private static function routeControllerParse(string $route): string {
            $controller = explode('-', $route);

            return ucfirst(self::routeParse(array_pop($controller)));
        }

        /**
         * Форматирование маршрута
         * >**get-products => getProducts**
         *
         * @param string $route
         * @return string
         */
        private static function routeParse(string $route): string {
            return lcfirst(
                str_replace(
                    ' ',
                    '',
                    ucwords(
                        str_replace('-', ' ', strtolower($route))
                    )
                )
            );
        }
    }
