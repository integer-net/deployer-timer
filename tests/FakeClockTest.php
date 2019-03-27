<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

use PHPUnit\Framework\TestCase;

class FakeClockTest extends TestCase
{
    public function testAdvanceMs()
    {
        $clock = new FakeClock((float) strtotime('2000-01-01'));
        $this->assertEqualsWithDelta(946684800, $clock->microtime(), 0.001);
        $clock->advanceMs(500);
        $this->assertEqualsWithDelta(946684800.5, $clock->microtime(), 0.001);
        $clock->advanceMs(50);
        $this->assertEqualsWithDelta(946684800.55, $clock->microtime(), 0.001);
    }
}
