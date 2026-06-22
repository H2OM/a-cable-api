<?php
use app\core\Env;

require_once __DIR__ . '/../composer/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$env = new Env();

$date = date("Y-m-d");
$id = $env->get('YANDEX_ID');
$token = $env->get('YANDEX_TOKEN');

$url = "https://api-metrika.yandex.net/stat/v1/data?" . http_build_query([
    'id'     => $id,
    'metrics' => 'ym:s:visits,ym:s:pageviews,ym:s:users',
    'date1'   => 'today',
    'date2'   => 'today'
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: OAuth ' . $token]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    $data = json_decode($response, true);

    if (isset($data['totals'])) {
        file_put_contents(
            filename: __DIR__ . "/../data/ym_$date.json",
            data: json_encode([
                'visits' => $data['totals'][0],
                'unique_visits' => $data['totals'][2],
                'page_views' => $data['totals'][1],
                'date' => $date,
            ]),
            flags: LOCK_EX
        );
    }
}
