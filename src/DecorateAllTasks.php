<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

use Deployer\Deployer;
use Deployer\Task\Task;

class DecorateAllTasks
{
    /**
     * @var Deployer
     */
    private $deployer;

    public function __construct(Deployer $deployer)
    {
        $this->deployer = $deployer;
    }

    public function with(TaskDecorator $decorator): void
    {
        foreach ($this->deployer->tasks as $taskName => $task) {
            $beforeName = $taskName . '.' . $decorator->name() . '.before';
            $afterName = $taskName . '.' . $decorator->name() . '.after';
            $this->deployer->tasks->set($beforeName, (new Task($beforeName, $decorator->callbackBefore($taskName)))->shallow());
            $this->deployer->tasks->set($afterName, (new Task($afterName, $decorator->callbackAfter($taskName)))->shallow());
            if (!in_array($task->getName(), [$beforeName, $afterName], true)) {
                $task->addBefore($beforeName);
                $task->addAfter($afterName);
            }
        }
    }
}
