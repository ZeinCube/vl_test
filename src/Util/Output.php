<?php

namespace App\Util;

use App\Interval\Interval;
use Exception;

class Output
{
    private const DATE_PRINT_FORMAT = 'd:M:Y H:i:s';

    /**
     * @param Interval $interval
     *
     * @return void
     */
    public static function printInterval(Interval $interval): void
    {
        $startDate = $interval->getStartTime()->format(self::DATE_PRINT_FORMAT);
        $endDate = $interval->getEndTime()->format(self::DATE_PRINT_FORMAT);
        $availabilityLevel = IntervalHandler::availabilityLevel($interval->getFaultLogsCount(), $interval->getLogsCount());

        $string = sprintf("%s %s %s\n", $startDate, $endDate, $availabilityLevel);

        echo $string;
    }

    /**
     * @param string $message
     * @param Exception $exception
     *
     * @return void
     */
    public static function printException(string $message, Exception $exception): void
    {
        $string = sprintf("%s: %s", $message, $exception->getMessage());

        echo $string;
    }
}