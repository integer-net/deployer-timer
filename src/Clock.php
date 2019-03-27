<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

interface Clock
{
    public function microtime(): float;
}
