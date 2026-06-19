<?php

namespace app\repositories;

use app\core\Db;

/** Репозиторий для общих запросов */
readonly class BaseRepository {
    public function __construct(private Db $db) {}

    /**
     * Получить информацию о структуре таблицы
     *
     * @param string $table
     * @return array|null
     */
    public function getTableShema(string $table): ?array {
        return $this->db->fetchAll("SHOW COLUMNS FROM {$table}");
    }
}