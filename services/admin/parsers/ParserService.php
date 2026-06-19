<?php

namespace app\services\admin\parsers;

/** Абстрактный сервис для управления парсерами */
readonly abstract class ParserService {
    protected const string IMAGES_PATH = __DIR__ . "/../../../../public/img/";

    /**
     * Выгрузка из
     *
     * @param array $data
     * @return array
     */
    abstract protected function from(array $data): array;

    /**
     * Загрузка в текущую БД
     *
     * @param array $parsedProducts
     * @param int $categoryTypeId
     * @return bool
     */
    abstract protected function to(array $parsedProducts, int $categoryTypeId): bool;

    /**
     * Получение и сохранение изображения
     *
     * @param string $imageUrl
     * @param string $imageLocalName
     * @return bool
     */
    protected function getImage(string $imageUrl, string $imageLocalName): bool {
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $imageData !== false && !$this->imageExists($imageLocalName)) {
            return file_put_contents(static::IMAGES_PATH . $imageLocalName, $imageData);

        } else {
            return false;
        }
    }

    /**
     * Проверка на существование изображения
     *
     * @param string $imageName
     * @return bool
     */
    protected function imageExists(string $imageName): bool {
        return file_exists(static::IMAGES_PATH . $imageName);
    }
}