<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

class TimerEvents
{
    /**
     * @var TimerEvent[]
     */
    private $items = [];

    public function append(TimerEvent $event)
    {
        $this->items[] = $event;
    }

    public function asCsv(): string
    {
        $lines = [];
        foreach ($this->items as $item) {
            $lines[] = $item->asCsvLine();
        }
        return implode("\n", $lines);
    }
}