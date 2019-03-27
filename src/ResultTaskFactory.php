<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

use Deployer\Deployer;
use Deployer\Task\Task;

class ResultTaskFactory
{
    /**
     * @var TimerTaskDecorator
     */
    private $timer;
    /**
     * @var Deployer
     */
    private $deployer;

    public function __construct(Deployer $deployer, TimerTaskDecorator $timer)
    {
        $this->deployer = $deployer;
        $this->timer = $timer;
    }

    public function createCsvResultTask($fileName): string
    {
        $taskName = uniqid('timer_result-', true);
        $taskBody = function () use ($fileName) {
            file_put_contents($fileName, $this->timer->resultsAsCsv());
        };
        $this->deployer->tasks->set($taskName, new Task($taskName, $taskBody));

        return $taskName;
    }
}
