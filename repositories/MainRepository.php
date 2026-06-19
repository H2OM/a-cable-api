<?php

namespace app\repositories;

use app\core\Db;

/** Репозиторий для управления главной информацией */
readonly class MainRepository {
    public function __construct(private Db $db) {}

    /**
     * Получение данных из таблицы 'новости'
     *
     * @return array
     */
    public function getNews(): array {
        return $this->db->query()->table('news')->orderBy('position')->get();
    }
}