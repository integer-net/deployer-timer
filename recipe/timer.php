<?php
declare(strict_types=1);

namespace Deployer;

use IntegerNet\DeployerTimer\DecorateAllTasks;
use IntegerNet\DeployerTimer\ResultTaskFactory;
use IntegerNet\DeployerTimer\TimerTaskDecorator;

function timer(): ResultTaskFactory
{
    $deployer = Deployer::get();
    $decorateAllTasks = new DecorateAllTasks($deployer);
    $timer = new TimerTaskDecorator();
    $decorateAllTasks->with($timer);
    return new ResultTaskFactory($deployer, $timer);
}
