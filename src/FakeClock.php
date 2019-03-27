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

    public function advanceMs(int $ms): void
    {
        $this->timestamp += ($ms * 0.001);
    }

}