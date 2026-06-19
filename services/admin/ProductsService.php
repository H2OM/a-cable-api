<?php

namespace app\services\admin;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\core\Hydrator;
use app\core\Uploader;
use app\repositories\FiltersRepository;
use app\repositories\ProductsRepository;
use app\services\ShemaService;

/** Сервис для управления товарами */
readonly class ProductsService {
    public function __construct(
        private ProductsRepository $productsRepository,
        private FiltersRepository  $filtersRepository,
        private ShemaService       $shemaService,
        private Uploader           $uploader,
        private Hydrator           $hydrator
    ) {}


    /**
     * Добавление нового товара
     *
     * @param array $data
     * @return string
     * @throws ResponseException
     */
    public function add(array $data): string {
        $stock = $data['stock'] ?? 0;

        $data = $this->prepareProduct($data);
        $messages = $this->uploader->errors;

        $id = (int)$this->productsRepository->insertId($data);

        if(!$id) {
            throw new ResponseException(ResponseMessage::ERROR_DUPLICATE);
        }

        if(!$this->productsRepository->insertStock(['product_id' => $id, 'count' => $stock])) {
            $messages[] = 'Не удалось обновить остаток товара!';
        }

        $messages[] = $this->pairActions($data, $id);

        return implode(PHP_EOL, $messages);
    }

    /**
     * Обновление товара
     *
     * @param array $data
     * @return string
     * @throws ResponseException
     */
    public function update(array $data): string {
        if(empty($data['id'])) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        $id    = $data['id'];
        $stock = $data['stock'] ?? 0;

        $data = $this->prepareProduct($data);
        $messages = $this->uploader->errors;

        if(!$this->productsRepository->update($data)) {
            throw new ResponseException(ResponseMessage::ERROR_UPDATE);
        }

        if(!$this->productsRepository->updateStock($id, $stock)) {
            $messages[] = 'Не удалось обновить остаток товара!';
        }

        $messages[] = $this->pairActions($data, $id);

        return implode(PHP_EOL, $messages);
    }

    /**
     * Добавление наличия товаров
     *
     * @param array $data
     * @return bool
     */
    public function insertStock(array $data): bool {
        return $this->productsRepository->insertStock($data);
    }

    /**
     * Удаление товаров по Id
     *
     * @param array $ids
     * @return bool
     */
    public function deleteByIds(array $ids): bool {
        return $this->productsRepository->deleteByIds($ids);
    }

    /**
     * Привязка вариаций к товару
     *
     * @param array $ids
     * @param array $variationsIds
     * @param bool $oneWay
     * @return bool
     */
    public function pairVariations(array $ids, array $variationsIds, bool $oneWay = false): bool {
        $data = [];

        foreach ($ids as $id) {
            foreach($variationsIds as $variationId) {
                if($variationId === $id) continue;

                $data[] = ['product_id' => $id, 'variation_id' => $variationId];

                if(!$oneWay) {
                    $data[] = ['product_id' => $variationId, 'variation_id' => $id];
                }
            }
        }

        return $this->productsRepository->pairVariations($data);
    }

    /**
     * Привязка связанных товаров
     *
     * @param array $ids
     * @param array $relatedIds
     * @param bool $oneWay
     * @return bool
     */
    public function pairRelated(array $ids, array $relatedIds, bool $oneWay = false): bool {
        $data = [];

        foreach ($ids as $id) {
            foreach($relatedIds as $relatedId) {
                if($relatedId === $id) continue;

                $data[] = ['product_id' => $id, 'related_id' => $relatedId];

                if(!$oneWay) {
                    $data[] = ['product_id' => $relatedId, 'related_id' => $id];
                }
            }
        }

        return $this->productsRepository->pairRelated($data);
    }

    public function pairActions(array $data, int $id): string {
        $parsedData = $this->hydrator->decodeJson($data, [
            'related_products',
            'local_filters',
            'variations',
        ]);

        $related_products = $parsedData['related_products'] ?? null;
        $localFilters     = $parsedData['local_filters'] ?? null;
        $variations       = $parsedData['variations'] ?? null;
        $messages         = [];

        if(!empty($variations)) {
            $variations[] = $id;

            if(!$this->pairVariations($variations, $variations)) {
                $messages[] = 'Не удалось привязать вариации!';
            }
        }

        if(!empty($related_products)) {
            $related_products[] = $id;

            if(!$this->pairRelated($related_products, $related_products)) {
                $messages[] = 'Не удалось добавить связанные товары!';
            }
        }

        if(!empty($localFilters)) {
            $formattedFilters = [];

            foreach ($localFilters as $value) {
                $formattedFilters[] = [
                    'filter_value_id' => $value['value_id'],
                    'product_id' => $id,
                ];
            }

            if(!$this->filtersRepository->insertValuesProducts($formattedFilters)) {
                $messages[] = 'Не удалось добавить характеристики товара!';
            }
        }

        return implode(PHP_EOL, $messages);
    }

    /**
     * Присваивание товарам статуса хит продаж
     *
     * @param array $ids
     * @param bool $status
     * @return bool
     */
    public function changeHit(array $ids, bool $status): bool {
        return $this->productsRepository->changeHit($ids, $status);
    }

    /**
     * Подготовка данных товара для вставки в главную таблицу
     *
     * @param array $data
     * @return array
     */
    private function prepareProduct(array $data): array {
        $images = $data['images'] ?? [];

        $this->uploader->uploadFromFiles(fieldName: 'new_images', prefix: $data['article'] ?? null);

        if(!is_array($images)) {
            $images = explode(',', $images);
        }

        $images = [...$images, ...$this->uploader->savedFileNames];
        $data['image'] = $images[0] ?? '';
        $data['slider_images'] = implode(',', $images);

        $columns = $this->shemaService->getTableColumns('products');

        foreach ($data as $key => $value) {
            if(!in_array($key, $columns)) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}