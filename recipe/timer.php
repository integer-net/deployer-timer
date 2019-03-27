<?php
declare(strict_types=1);

namespace Deployer;

use Deployer\Task\Task;
use IntegerNet\DeployerTimer\DecorateAllTasks;
use IntegerNet\DeployerTimer\TimerTaskDecorator;

function timerCsv(string $fileName)
{
    $deployer = Deployer::get();
    $decorateAllTasks = new DecorateAllTasks($deployer);
    $timer = new TimerTaskDecorator();
    $decorateAllTasks->with($timer);
    $tasks = $deployer->tasks->toArray();
    /** @var Task $lastTask */
    $lastTask = array_pop($tasks);
    $collectResultTask = uniqid('timer_result-', true);
    task(
        $collectResultTask, function() use ($timer, $fileName) {
        file_put_contents($fileName, $timer->resultsAsCsv());
    });
    after($lastTask->getName(), $collectResultTask);
}