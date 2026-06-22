<?php

namespace app\services\admin;

use DateInterval;
use DateMalformedPeriodStringException;
use DateMalformedStringException;
use DatePeriod;
use DateTime;

/** Сервис для получения статистики */
class StatisticService {
    private const string STATISTIC_PATH = __DIR__ . "/../../data";

    /**
     * Получение метрики за указанный период
     *
     * @param string $periodFrom формат:
     *
     *      d-m-Y
     * @param string $periodTo формат:
     *
     *      d-m-Y
     * @return array
     * @throws DateMalformedStringException
     * @throws DateMalformedPeriodStringException
     */
    public function getMetric(string $periodFrom, string $periodTo): array {
        $start = DateTime::createFromFormat('Y-m-d', $periodFrom);
        $end = DateTime::createFromFormat('Y-m-d', $periodTo);

        $start->setTime(0, 0, 0);
        $end->setTime(0, 0, 0);
        $end->modify('+1 day');

        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);

        $metrics = [];

        foreach ($period as $date) {
            $fileDate = $date->format('Y-m-d');
            $filePath = self::STATISTIC_PATH . "/ym_$fileDate.json";

            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $metrics[] = json_decode($content, true);

            }
        }

        return $metrics;
    }
}