<?php

namespace app\repositories;

use app\core\Db;
use app\core\Hydrator;

/** Репозиторий для управления баннерами */
readonly class BannersRepository {
    public function __construct(private Db $db, private Hydrator $hydrator) {}

    /**
     * Получение всех
     *
     * @return array
     */
    public function getAll(): array {
        return $this->hydrator->decodeJson($this->db->query()
            ->table('banners')
            ->select([
                '*',
                "(
                    SELECT 
                        COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                            'id', news.id,
                            'title', news.title,
                            'text', news.text,
                            'image', news.image,
                            'position', news.position
                        )), JSON_ARRAY())
                    FROM news
                    WHERE banners.id = news.banner_id
                ) AS slides"
            ])
            ->get(),
            ['slides']
        );
    }
}