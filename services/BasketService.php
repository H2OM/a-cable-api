<?php

namespace app\services;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Session;
use app\repositories\ProductsRepository;

/** Сервис для управления корзиной покупок */
class BasketService {
    private array $basket;

    public function __construct(
        private readonly ProductsRepository $productsRepository,
        private readonly Session            $session,
    ) {
        $basket = $this->session->get('basket');

        if(!$basket) {
            $this->session->set('basket', []);

            $basket = [];
        }

        $this->basket = $basket;
    }

    /**
     * Получение
     *
     * @return array
     */
    public function get(): array {
        return $this->basket;
    }

    /**
     * Сохранение в сессию
     *
     * @return array
     */
    public function save(): array {
        $this->basket = array_values($this->basket);
        $this->session->set('basket', $this->basket);

        return $this->basket;
    }

    /**
     * Добавление
     *
     * @param int $id
     * @param int $count
     * @return array
     * @throws ResponseException
     */
    public function add(int $id, int $count = 1): array {
        $count = max(1, $count);
        $index = $this->findProductIndex(id: $id);

        if($index !== false) {
            $this->basket[$index]['count'] += $count;

            return $this->save();
        }

        $product = $this->productsRepository->getProductById($id);

        if(!$product || count($product) === 0) {
            throw new ResponseException(responseMessage: ResponseMessage::ERROR_PRODUCT_NOT_FOUND, code: 404);
        }

        $product['count'] = $count;

        $this->basket[] = $product;

        return $this->save();
    }

    /**
     * Установка количества конкретного товара
     *
     * @param int $id
     * @param int $count
     * @return array
     * @throws ResponseException
     */
    public function setCount(int $id, int $count): array {
        $count = max(1, $count);
        $index = $this->findProductIndex(id: $id);

        if($index === false) {
            throw new ResponseException(responseMessage: ResponseMessage::ERROR_PRODUCT_NOT_FOUND, code: 404);
        }

        $this->basket[$index]['count'] = $count;

        return $this->save();
    }

    /**
     * Уменьшить кол-во конкретного товара
     *
     * @param int $id
     * @return array
     * @throws ResponseException
     */
    public function decrement(int $id): array {
        $index = $this->findProductIndex(id: $id);

        if($index === false) {
            throw new ResponseException(responseMessage: ResponseMessage::ERROR_PRODUCT_NOT_FOUND, code: 404);
        }

        if($this->basket[$index]['count'] === 1) {
            unset($this->basket[$index]);

        } else {
            $this->basket[$index]['count'] -= 1;
        }

        return $this->save();
    }

    /**
     * Удалить товар
     *
     * @param int $id $id
     * @return array
     * @throws ResponseException
     */
    public function remove(int $id): array {
        $index = $this->findProductIndex(id: $id);

        if($index === false) {
            throw new ResponseException(responseMessage: ResponseMessage::ERROR_PRODUCT_NOT_FOUND, code: 404);
        }

        unset($this->basket[$index]);

        return $this->save();
    }

    /**
     * Отчистка
     *
     * @return array
     */
    public function clear(): array {
        $this->basket = [];

        return $this->save();
    }

    /**
     * Найти товар в корзине
     *
     * @param int $id
     * @return int|false
     */
    private function findProductIndex(int $id): int|false{
        foreach ($this->basket as $index => $product) {
            if ($product['id'] === $id) {
                return $index;
            }
        }

        return false;
    }
}