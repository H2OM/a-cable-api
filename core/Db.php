<?php
    namespace app\core;

    use PDO;
    use PDOException;
    use PDOStatement;

    /** Работа с базой данных */
    class Db {
        private PDO $pdo;

        public function __construct(array $db_config) {
            try {
                $this->pdo = new PDO(
                    'mysql:host='.$db_config['host'].';dbname='.$db_config['dbname'].';charset=utf8',
                    $db_config['user'],
                    $db_config['pass'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_EMULATE_PREPARES => FALSE,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="TRADITIONAL"'
                    ]
                );
            } catch (PDOException $e) {
                throw new \PDOException(
                    "Database connection error: " . $e->getMessage(),
                    (int)$e->getCode()
                );
            }
        }

        /**
         * Получение сборщика запроса
         *
         * @return QueryBuilder
         */
        public function query(): QueryBuilder {
            return new QueryBuilder($this);
        }

        /**
         * Выполнение запроса и возвращение всех строк
         *
         * @param string $sql
         * @param array $params
         * @return array
         */
        public function fetchAll(string $sql, array $params = []): array {
            return $this->preparedExecute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Выполнение запроса и возвращение одной строки
         *
         * @param string $sql
         * @param array $params
         * @return array|null
         */
        public function fetchOne(string $sql, array $params = []): ?array {
            return $this->preparedExecute($sql, $params)->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        /**
         * Выполнение запроса и возвращение первой колонки первой строки
         *
         * @param string $sql
         * @param array $params
         * @return mixed
         */
        public function fetchColumn(string $sql, array $params = []): mixed {
            return $this->preparedExecute($sql, $params)->fetchColumn();
        }

        /**
         * Выполнение запроса и возвращения id строки
         *
         * @param string $sql
         * @param array $params
         * @return string|false
         */
        public function fetchInsertId(string $sql, array $params = []): string|false {
            $this->preparedExecute($sql, $params);

            return $this->pdo->lastInsertId();
        }

        /**
         * Выполнение запроса и получение затронутых строк
         *
         * @param string $sql
         * @param array $params
         * @return int
         */
        public function fetchAffectedRows(string $sql, array $params = []): int {
            return $this->preparedExecute($sql, $params)->rowCount();
        }

        /**
         * Выполнение запроса и получение его статуса
         *
         * @param string $sql
         * @param array $params
         * @return bool
         */
        public function execute(string $sql, array $params = []): bool {
            $state = $this->pdo->prepare($sql);

            return $state->execute($params);
        }

        /**
         * Выполнение запроса и возвращение состояния
         *
         * @param string $sql
         * @param array $params
         * @return PDOStatement
         */
        public function preparedExecute(string $sql, array $params = []): PDOStatement {
            $state = $this->pdo->prepare($sql);
            $state->execute($params);

            return $state;
        }

        /**
         * Начало транзакции
         *
         * @return bool
         */
        public function beginTransaction(): bool {
            return $this->pdo->beginTransaction();
        }

        /**
         * Подтверждение транзакции
         *
         * @return bool
         */
        public function commit(): bool {
            return $this->pdo->commit();
        }

        /**
         * Отмена транзакции
         *
         * @return bool
         */
        public function rollback(): bool {
            return $this->pdo->rollBack();
        }
    }
