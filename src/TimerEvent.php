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
        $f = fopen('php://memory', 'rb+');
        if (fputcsv($f, $this->asArray()) === false) {
            throw new \RuntimeException('Cannot format TimerEvent as CSV');
        }
        rewind($f);
        $line = stream_get_contents($f);
        fclose($f);
        return rtrim($line);
    }
}