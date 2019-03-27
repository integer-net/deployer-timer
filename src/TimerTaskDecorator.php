<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

class TimerTaskDecorator implements TaskDecorator
{
    /**
     * @var TimerEvents
     */
    private $events;

    /**
     * @var float[]
     */
    private $startTimeStack = [];

    /**
     * @var Clock
     */
    private $clock;

    public function __construct(?Clock $clock = null)
    {
        $this->events = new TimerEvents();
        $this->clock = $clock ?? new SystemClock();
    }

    public function name(): string
    {
        return 'timer';
    }

    public function callbackBefore(string $taskName): callable
    {
        return function() use ($taskName) {
            \array_push($this->startTimeStack, $this->clock->microtime());
            $this->events->append(new TimerEvent(TimerEvent::TYPE_BEGIN, $taskName, $this->clock->microtime(), 0));
        };
    }

    public function callbackAfter(string $taskName): callable
    {
        return function() use ($taskName) {
            $startTime = \array_pop($this->startTimeStack);
            $duration = $this->clock->microtime() - $startTime;
            $this->events->append(new TimerEvent(TimerEvent::TYPE_END, $taskName, $this->clock->microtime(), $duration));
        };
    }

    public function resultsAsCsv(): string
    {
        return $this->events->asCsv();
    }
}