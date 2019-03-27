<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

use PHPUnit\Framework\TestCase;

class SystemClockTest extends TestCase
{
    public function testMicrotime()
    {
        $clock = new SystemClock();
        $this->assertEqualsWithDelta(
            microtime(true),
            $clock->microtime(),
            0.000010,
            'System clock should return current time in µs'
        );
    }
}
