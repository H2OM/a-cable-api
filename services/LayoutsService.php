<?php

namespace app\services;

/** Сервис для работы с шаблонами */
readonly class LayoutsService {
    private const string LAYOUTS_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR;

    /**
     * Получение шаблона для письма
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public function getMailLayout(string $template, array $data): string {
        return $this->get(template: "mail" . DIRECTORY_SEPARATOR . $template, data: $data);
    }

    /**
     * Получение шаблона
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public function get(string $template, array $data): string {
        extract($data);
        ob_start();

        require self::LAYOUTS_PATH . "$template.php";

        return ob_get_clean();
    }
}