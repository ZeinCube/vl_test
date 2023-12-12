<?php

namespace App\Input;

class Input
{
    private ?float $uptimePercent;

    private ?float $responseTimeLimit;

    /**
     * @param float|null $uptimePercent
     * @param float|null $responseTimeLimit
     */
    public function __construct(?float $uptimePercent, ?float $responseTimeLimit)
    {
        $this->uptimePercent = $uptimePercent;
        $this->responseTimeLimit = $responseTimeLimit;
    }

    /**
     * @return float
     */
    public function getUptimePercent(): float
    {
        return $this->uptimePercent;
    }

    /**
     * @return float
     */
    public function getResponseTimeLimit(): float
    {
        return $this->responseTimeLimit;
    }
}