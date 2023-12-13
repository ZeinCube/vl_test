<?php

namespace App\Interval;

use App\Exception\HasNoOpenedIntervalException;
use App\Log\Log;
use Countable;
use Iterator;

class IntervalContainer implements Iterator, Countable
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

    public function sortIntervals(): void
    {
        usort($this->intervals, static function(Interval $a, Interval $b) {

            if ($a->getStartTime() === $b->getStartTime()) {
                return 0;
            }

            return $a < $b ? -1 : 1;
        });
    }

    public function count(): int
    {
        return count($this->intervals);
    }
}