<?php

namespace App\Log;

use DateTime;

class Log
{
    private int $responseCode;

    private float $responseTime;

    private DateTime $logTime;

    private bool $failure = false;

    /**
     * @param int $responseCode
     * @param float $responseTime
     * @param DateTime $logTime
     */
    public function __construct(int $responseCode, float $responseTime, DateTime $logTime)
    {
        $this->responseCode = $responseCode;
        $this->responseTime = $responseTime;
        $this->logTime      = $logTime;
    }

    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    /**
     * @return float
     */
    public function getResponseTime(): float
    {
        return $this->responseTime;
    }

    /**
     * @return DateTime
     */
    public function getLogTime(): DateTime
    {
        return $this->logTime;
    }

    /**
     * @param bool $failure
     */
    public function setFailure(bool $failure): void
    {
        $this->failure = $failure;
    }

    /**
     * @return bool
     */
    public function isFailure(): bool
    {
        return $this->failure;
    }
}