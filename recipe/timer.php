<?php
declare(strict_types=1);

namespace Deployer;

use IntegerNet\DeployerTimer\DecorateAllTasks;
use IntegerNet\DeployerTimer\ResultTaskFactory;
use IntegerNet\DeployerTimer\TimerTaskDecorator;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
if (!function_exists(__NAMESPACE__ . '\timer')) {
    function timer(): ResultTaskFactory
    {
        $deployer = Deployer::get();
        $decorateAllTasks = new DecorateAllTasks($deployer);
        $timer = new TimerTaskDecorator();
        $decorateAllTasks->with($timer);
        return new ResultTaskFactory($deployer, $timer);
    }
}
