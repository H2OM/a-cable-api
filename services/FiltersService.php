<?php

namespace app\services;

use app\core\enums\ResponseMessage;
use app\core\exceptions\ResponseException;
use app\repositories\FiltersRepository;

/** Сервис для получения фильтров */
readonly class FiltersService {
    public function __construct(
        private FiltersRepository  $filtersRepository,
    ) {}

    /**
     * Получение всех фильтров
     *
     * @param string $categoryCode
     * @return array
     * @throws ResponseException
     */
    public function getFilters(string $categoryCode): array {
        $filters = $this->filtersRepository->getFilters($categoryCode);

        if(count($filters) === 0) {
            throw new ResponseException(ResponseMessage::ERROR_DATA, 403);
        }

        return $filters;
    }

    /**
     * Получение всех фильтров сгруппированных по коду
     *
     * @param string $categoryCode
     * @return array
     * @throws ResponseException
     */
    public function getFiltersGroupByCode(string $categoryCode): array {
        $filters = $this->getFilters($categoryCode);

        $groupedFilters = [];

        foreach($filters as $filter) {
            $code = $filter['filter_code'];

            if(!isset($groupedFilters[$code])) {
                $groupedFilters[$code] = [
                    'code' => $code,
                    'name' => $filter['filter_name'],
                    'type' => $filter['type'],
                    'position' => $filter['position'],
                    'values' => []
                ];
            }

            $groupedFilters[$code]['values'][] = [
                'id' => $filter['value_id'],
                'name' => $filter['value_name'],
                'code' => $filter['value_code'],
            ];
        }

        return array_values($groupedFilters);
    }

    /**
     * Получение всех фильтров сгруппированных по типу
     *
     * @param string $categoryCode
     * @return array
     * @throws ResponseException
     */
    public function getFiltersGroupByType(string $categoryCode): array {
        $filters = $this->getFilters($categoryCode);

        $groupedFilters = [];

        foreach($filters as $filter) {
            if($filter['name'] === "" && $filter['value_code'] === "") {
                continue;
            }

            $type = $filter['type'];

            if(!isset($groupedFilters[$type])) {
                $groupedFilters[$type] = [];
            }

            $index = "";

            foreach($groupedFilters[$type] as $key => $value) {
                if(isset($type['code']) && $value['code'] == $filter['code']) {
                    $index = $key;

                    break;
                }
            }

            if($index !== "") {
                $groupedFilters[$type][$index]["values"][] = [
                    "name" => $filter["name"],
                    "value_code" => $filter['value_code']
                ];
            } else {
                $groupedFilters[$type][] = [
                    "filter" => $filter['filter'],
                    "code" => $filter['code'],
                    "values" => [
                        [
                            "name" => $filter["name"],
                            "value_code" => $filter['value_code']
                        ]
                    ]
                ];
            }
        }

        return $groupedFilters;
    }
}