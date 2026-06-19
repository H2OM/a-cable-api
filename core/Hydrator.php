<?php

namespace app\core;

/** Класс для декодирования данных */
class Hydrator {
    /**
     * Декодирование JSON полей
     * >**Возвращает входящий массив**
     *
     * @param array $data
     * @param array $fields
     * @return array
     */
    public function decodeJson(array $data, array $fields): array {
        if (isset($data[0]) && is_array($data[0])) {
            foreach ($data as &$row) {
                $row = $this->decodeRow($row, $fields);
            }

            return $data;
        }

        return $this->decodeRow($data, $fields);
    }

    /**
     * Декодирование одной строки
     * >**Возвращает входящий массив**
     *
     * @param array $row
     * @param array $fields
     * @return array
     */
    private function decodeRow(array $row, array $fields): array {
        foreach ($fields as $field) {
            if (!isset($row[$field])) {
                continue;
            }

            $row[$field] = json_decode($row[$field], true) ?? [];
        }

        return $row;
    }
}
