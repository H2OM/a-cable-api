<?php

namespace app\services\admin\parsers;

use app\core\Db;
use app\core\enums\ResponseMessage;
use app\core\Env;
use app\core\exceptions\ResponseException;
use app\repositories\BrandsRepository;
use app\repositories\CategoriesRepository;
use app\repositories\FiltersRepository;
use app\repositories\parsers\AnlanRepository;
use app\repositories\ProductsRepository;
use Exception;

/** Парсер для сайта АнЛан */
readonly class AnlanParserService extends ParserService {
    private const array FILTERS_CODE_MAP = [
    ];

    private const array FILTERS_VALUES_CODE_MAP = [
        'seryj' => 'gray',
        'chernyj' => 'black'
    ];

    public function __construct(
        private CategoriesRepository $categoriesRepository,
        private ProductsRepository   $productsRepository,
        private FiltersRepository    $filtersRepository,
        private BrandsRepository     $brandsRepository,
        private AnlanRepository      $anlanRepository,
        private Env                  $env,
        private Db                   $db
    ) {}

    /**
     * Взять товары из АнЛан
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function from(array $data): array {
        $products = $this->anlanRepository->getProducts($data['brand_id'], $data['categories_codes'], $data['limit']);

        file_put_contents(__DIR__ . '/data/products.json', json_encode(
            value: $products,
            flags: JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ));

        return $products;
    }

    /**
     * Загрузить в текущую базу товары из АнЛан
     *
     * @param array $parsedProducts
     * @param int $categoryTypeId
     * @return bool
     * @throws ResponseException
     * @throws Exception
     */
    public function to(array $parsedProducts, int $categoryTypeId): bool {
        $mainCategory = $this->categoriesRepository->getMainCategoryByTypeId($categoryTypeId);
        $parsedProducts = array_values($parsedProducts);
        $products = [];
        $brandsMap = [];

        foreach ($parsedProducts as $parseProduct) {
            $images = [];

            if(!empty($parseProduct['images_filename'])) {
                foreach ($parseProduct['images_filename'] as $key => $image) {
                    $imageUrl = $this->env->get('PARSER_ANLAN_IMAGES_URL') . "/" . $image['image'];
                    $imageLocal = $parseProduct['id'] . '_' . $key . '_' . $image['image'];

                    if($this->imageExists($imageLocal) || $this->getImage($imageUrl, $imageLocal)) {
                        $images[] = $imageLocal;
                    }
                }
            }

            if(empty($brandsMap[$parseProduct['brand_code']])) {
                $currentBrand = $this->brandsRepository->getByCode(strtolower($parseProduct['brand_code']));

                $brandsMap[$parseProduct['brand_code']] = $currentBrand['id'] ?? 4;
            }

            $parsedName = trim(preg_replace('/\s+/', ' ', preg_replace(
                pattern: "/{$parseProduct['sku']}|{$parseProduct['brand_name']}/i",
                replacement: '',
                subject: $parseProduct['name']
            )));

            $products[] = [
                'title' => $parsedName,
                'brand_id' => $brandsMap[$parseProduct['brand_code']],
                'category_type_id' => $categoryTypeId,
                'article' => $parseProduct['sku'],
                'price' => $parseProduct['price'],
                'price_old' => 0,
                'unit' => $parseProduct['units'] ?? 'шт.',
                'image' => $images[0] ?? '',
                'slider_images' => implode(',', $images),
                'description' =>  strip_tags($parseProduct['description']),
                'hit' => 0
            ];
        }

        try {
            $this->db->beginTransaction();

            if(!$this->productsRepository->insert($products)) {
                throw new ResponseException(ResponseMessage::ERROR_ADD_PRODUCT);
            }

            $productsIds = $this->productsRepository->getProductsIdsByArticle(array_map(function ($product) {
                return $product['article'];
            }, $products));

            $productsIds = array_column($productsIds, null, 'article');

            $productsStocks = [];
            $filters = [];
            $filtersValues = [];
            $filtersValuesProducts = [];

            foreach($parsedProducts as $parsedProduct) {
                $productId = $productsIds[$parsedProduct['sku']]['id'] ?? null;

                $productsStocks[] = [
                    'product_id' => $productId,
                    'count' => mt_rand(0, 200),
                ];

                foreach($parsedProduct['filters'] as $parsedFilter) {
                    $filterCode = self::FILTERS_CODE_MAP[$parsedFilter['filter_code']] ?? $parsedFilter['filter_code'];
                    $valueCode = self::FILTERS_VALUES_CODE_MAP[$parsedFilter['value_code']] ?? $parsedFilter['value_code'];

                    if(empty($filters[$filterCode])) {
                        $filters[$filterCode] = [
                            'filter' => $parsedFilter['filter_name'],
                            'code' => $filterCode,
                            'type' => 'multi'
                        ];
                    }

                    $filtersValues[$filterCode][$valueCode] = [
                        'value' => $parsedFilter['value'],
                        'code' => $valueCode,
                        'filter_code' => $filterCode
                    ];

                    $filtersValuesProducts[$filterCode][$valueCode][] = [
                        'product_id' => $productId
                    ];
                }
            }

            $this->productsRepository->insertStock($productsStocks);

            if(!$this->filtersRepository->insert($filters)) {
                throw new ResponseException(ResponseMessage::ERROR_ADD_FILTERS);
            }

            $filtersIds = $this->filtersRepository->getFiltersIdsByCode(array_map(function ($filter) {
                return $filter['code'];
            }, $filters));

            $filtersIds = array_column($filtersIds, null, 'code');

            $parsedFiltersValues = [];
            $categoriesFilters = [];

            foreach($filtersValues as $filterCode => $filterValues) {
                $filterId = $filtersIds[$filterCode]['id'] ?? null;

                foreach($filterValues as $filterValue) {
                    $parsedFiltersValues[] = [
                        ...$filterValue,
                        'filter_id' => $filterId,
                    ];
                }

                $categoriesFilters[] = [
                    'category_id' => $mainCategory['id'],
                    'filter_id' => $filterId
                ];
            }

            $this->categoriesRepository->addFiltersToCategories($categoriesFilters);

            $result = $this->filtersRepository->insertFiltersValues(array_map(function ($value) {
                return [
                    'value' => $value['value'],
                    'code' => $value['code'],
                    'filter_id' => $value['filter_id'],
                ];
            }, $parsedFiltersValues));

            if(!$result) {
                throw new ResponseException(ResponseMessage::ERROR_ADD_FILTERS);
            }

            $filtersValuesWithIds = $this->filtersRepository->getFiltersValuesByCode(array_map(function ($filterValue) {
                return $filterValue['code'];
            }, $parsedFiltersValues));

            $parsedFiltersValuesWithIds = [];

            foreach($filtersValuesWithIds as $filterValueWithId) {
                $parsedFiltersValuesWithIds[$filterValueWithId['filter_id']][$filterValueWithId['code']] = $filterValueWithId;
            }

            $parsedFiltersValuesProducts = [];

            foreach($parsedFiltersValues as $parsedFiltersValue) {
                $filterId = $parsedFiltersValue['filter_id'];
                $filterCode = $parsedFiltersValue['filter_code'];
                $valueCode = $parsedFiltersValue['code'];
                $filtersValueId = $parsedFiltersValuesWithIds[$filterId][$valueCode]['id'] ?? null;
                $fvp = $filtersValuesProducts[$filterCode][$valueCode];

                foreach($fvp as $filterValueProduct) {
                    $parsedFiltersValuesProducts[] = [
                        'product_id' => $filterValueProduct['product_id'],
                        'filter_value_id' => $filtersValueId
                    ];
                }
            }

            if(!$this->filtersRepository->insertValuesProducts($parsedFiltersValuesProducts)) {
                throw new ResponseException(ResponseMessage::ERROR_ADD_FILTERS);
            }

            $this->db->commit();

            return true;
        } catch (Exception $e) {
            $this->db->rollBack();

            throw $e;
        }
    }
}