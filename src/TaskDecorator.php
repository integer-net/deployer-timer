<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

interface TaskDecorator
{
    public function name(): string;

    public function callbackBefore(string $taskName): callable;

    public function callbackAfter(string $taskName): callable;
}
