<?php

namespace app\services;

use app\repositories\BaseRepository;

/** Сервис для получения данных о таблицах */
class ShemaService {
    public function __construct(private readonly BaseRepository $baseRepository) {}

    private array $cache = [];

    /**
     * Получение полей таблицы
     *
     * @param string $table
     * @return array
     */
    public function getTableColumns(string $table): array {
        if(!isset($this->cache[$table])) {
            $tableShema = $this->baseRepository->getTableShema($table);

            if(empty($tableShema)) {
                return [];
            }

            $this->cache[$table] = $tableShema;
        }

        return array_column($this->cache[$table], 'Field');
    }
}