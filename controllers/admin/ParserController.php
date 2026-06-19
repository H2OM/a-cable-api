<?php

namespace app\controllers\admin;

use app\core\App;
use app\core\Db;
use app\core\enums\ResponseMessage;
use app\core\Env;
use app\core\exceptions\ResponseException;
use app\core\Request;
use app\core\Response;
use app\services\admin\parsers\AnlanParserService;
use Exception;

/** Контроллер для управления парсерами */
readonly class ParserController {
    public function __construct(
        private AnlanParserService $anlanParser,
        private Env                $env,
        private Request            $request
    ) {}

    /**
     * Получение товаров из АнЛан
     *
     * @return Response
     * @throws Exception
     */
    public function fromAnlanAction(): Response {
        $data = $this->request->input();

        if(
            empty($data['brand_id'])         ||
            empty($data['categories_codes']) ||
            empty($data['limit'])            ||
            empty($data['deploy_category_id'])
        ) {
            throw new ResponseException(ResponseMessage::ERROR_NOT_ENOUGH_DATA);
        }

        App::container()->set('AnlanDb',  new Db ([
            'dbname' => $this->env->get('PARSER_ANLAN_DB_NAME'),
            'host'   => $this->env->get('PARSER_ANLAN_DB_HOST'),
            'user'   => $this->env->get('PARSER_ANLAN_DB_USER'),
            'pass'   => $this->env->get('PARSER_ANLAN_DB_PASS'),
        ]));

        $result = $this->anlanParser->from($data);

        $this->anlanParser->to($result, $data['deploy_category_id']);

        return Response::jsonSuccess(message: ResponseMessage::SUCCESS_PARSED);
    }
}