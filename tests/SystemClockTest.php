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
            0.0005, // test only for ms accuracy
            'System clock should return current time in µs'
        );
    }
}
