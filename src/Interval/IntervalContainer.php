<?php

namespace App\Interval;

use App\Exception\HasNoOpenedIntervalException;
use App\Log\Log;
use Iterator;

class IntervalContainer implements Iterator
{
    /**
     * @var Interval[]
     */
    private array $intervals = [];

    private int $index = 0;

    /**
     * @return Interval|null
     */
    public function getCurrent(): ?Interval
    {
        if (count($this->intervals) === 0) {
            return null;
        }

        return end($this->intervals);
    }

    /**
     * @param Log $log
     *
     * @return $this
     */
    public function addLogToInterval(Log $log): self
    {
        $current = $this->getCurrent();

        if (($current === null || $current->isClosed()) && $log->isFailure()) {
            $this->intervals[] = new Interval($log);

            return $this;
        }

        $current?->addLog($log);

        return $this;
    }

    /**
     * Closes current interval for adding logs
     *
     * @return $this
     */
    public function closeCurrent(): self
    {
        $current = $this->getCurrent();

        if ($current === null) {
            throw new HasNoOpenedIntervalException("There is not opened interval to close");
        }

        $current->close();

        return $this;
    }

    public function current(): Interval
    {
        return $this->intervals[$this->index];
    }

    public function next(): void
    {
        $this->index++;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return isset($this->intervals[$this->index]);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }
}