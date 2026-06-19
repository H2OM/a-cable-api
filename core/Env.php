<?php

namespace app\core;

/** Управление окружением */
class Env {
    /**
     * Получение переменной окружения с автоматическим приведением типов
     *
     * @param string $key
     * @param $default
     * @return mixed
     */
    public function get(string $key, $default = null): mixed {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'empty':
                return '';
            case 'null':
                return null;
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        return $value;
    }
}