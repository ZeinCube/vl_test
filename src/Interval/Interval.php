<?php

namespace App\Interval;

use App\Log\Log;
use DateTime;

class Interval
{
    private DateTime $startTime;
    private DateTime $endTime;

    private int $logsCount = 0;
    private int $faultLogsCount = 0;

    /**
     * Flag to close interval for editing
     *
     * @var bool
     */
    private bool $closed = false;

    /**
     * @param Log $log
     */
    public function __construct(Log $log)
    {
        $this->startTime = $log->getLogTime();
        $this->endTime = $log->getLogTime();

        $this->incrementLogCount();

        if ($log->isFailure()) {
            $this->incrementFaultLogCount();
        }
    }

    /**
     * Closes interval for adding logs
     *
     * @return $this
     */
    public function close(): self
    {
        $this->closed = true;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    /**
     * @return DateTime
     */
    public function getEndTime(): DateTime
    {
        return $this->endTime;
    }

    /**
     * @param Log $log
     *
     * @return $this
     */
    public function addLog(Log $log): self
    {
        $this->incrementLogCount();

        if ($log->isFailure()) {
            $this->incrementFaultLogCount();
        }

        $this->endTime = $log->getLogTime();

        return $this;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * @return int
     */
    public function getLogsCount(): int
    {
        return $this->logsCount;
    }

    /**
     * @return int
     */
    public function getFaultLogsCount(): int
    {
        return $this->faultLogsCount;
    }

    /**
     * @return void
     */
    private function incrementLogCount(): void
    {
        ++$this->logsCount;
    }

    /**
     * @return void
     */
    private function incrementFaultLogCount(): void
    {
        ++$this->faultLogsCount;
    }
}