<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

class FakeClock implements Clock
{
    /**
     * @var float
     */
    private $timestamp;

    public function __construct(float $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function microtime(): float
    {
        return $this->timestamp;
    }

    public function advanceMillis(int $millis): void
    {
        $this->timestamp += ($millis * 0.001);
    }
}
