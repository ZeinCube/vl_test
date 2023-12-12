<?php

namespace App\Util;

use App\Log\Log;
use App\Interval\Interval;

class IntervalHandler
{
    private float $availabilityMinimumLevel;

    private float $responseTimeLimit;

    /**
     * @param float $availabilityMinimumLevel
     * @param float $responseTimeLimit
     */
    public function __construct(float $availabilityMinimumLevel, float $responseTimeLimit)
    {
        $this->availabilityMinimumLevel = $availabilityMinimumLevel;
        $this->responseTimeLimit = $responseTimeLimit;
    }

    public function checkIsFailureLog(Log $log): bool
    {
        return $log->getResponseCode() > 500 || $log->getResponseTime() > $this->responseTimeLimit;
    }

    public function dryCheckIsIntervalFault(Interval $interval, Log $log): bool
    {
        $failureCount = $interval->getFaultLogsCount();
        $logsCount = $interval->getLogsCount() + 1;

        if ($log->isFailure()) {
            ++$failureCount;
        }

        return self::availabilityLevel($failureCount, $logsCount) < $this->availabilityMinimumLevel;
    }

    public static function availabilityLevel(int $failureLogsCount, int $logsCount): float
    {
        return 100 - (round($failureLogsCount / $logsCount, 2) * 100);
    }
}