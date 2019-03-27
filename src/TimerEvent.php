<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

class TimerEvent
{
    public const TYPE_BEGIN = 'BEGIN';
    public const TYPE_END = 'END';
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $taskName;
    /**
     * @var float
     */
    private $timestamp;
    /**
     * @var float
     */
    private $duration;

    public function __construct(string $type, string $taskName, float $timestamp, float $duration)
    {
        $this->type = $type;
        $this->taskName = $taskName;
        $this->timestamp = $timestamp;
        $this->duration = $duration;
    }

    private function asArray()
    {
        return [$this->type, $this->taskName, round($this->timestamp, 3), round($this->duration, 3)];
    }

    public function asCsvLine(): string
    {
        $csvStream = fopen('php://memory', 'rb+');
        if ($csvStream === false) {
            throw new \RuntimeException('Could not open temporary stream');
        }
        try {
            if (fputcsv($csvStream, $this->asArray()) === false) {
                throw new \RuntimeException('Could not format TimerEvent as CSV');
            }
            rewind($csvStream);
            $line = stream_get_contents($csvStream);
            if ($line === false) {
                throw new \RuntimeException('Could not read from temporary CSV stream');
            }
        } finally {
            fclose($csvStream);
        }
        return rtrim($line);
    }
}
