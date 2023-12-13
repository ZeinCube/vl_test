<?php

namespace App\Log;

use App\Input\Input;
use App\Interval\IntervalContainer;
use App\Util\IntervalHandler;

class LogProcessor
{
    private IntervalHandler $intervalHandler;
    private IntervalContainer $intervalContainer;

    /**
     * @param Input $input
     */
    public function __construct(Input $input)
    {
        $this->intervalHandler = new IntervalHandler($input->getUptimePercent(), $input->getResponseTimeLimit());
        $this->intervalContainer = new IntervalContainer();
    }

    public function processLog(Log $log): void
    {
        if ($this->intervalHandler->checkIsFailureLog($log)) {
            $log->setFailure(true);
        }

        $currentInterval = $this->intervalContainer->getCurrent();

        if ($currentInterval !== null && !$this->intervalHandler->dryCheckIsIntervalFault($currentInterval, $log)) {
            $this->intervalContainer->closeCurrent();
        }

        $this->intervalContainer->addLogToInterval($log);
    }

    public function getIntervalContainer(): IntervalContainer
    {
        return $this->intervalContainer;
    }
}