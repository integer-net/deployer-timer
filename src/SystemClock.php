<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

class SystemClock implements Clock
{
    public function microtime(): float
    {
        return \microtime(true);
    }
}
