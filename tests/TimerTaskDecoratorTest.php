<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

use PHPUnit\Framework\TestCase;

class TimerTaskDecoratorTest extends TestCase
{
    public function testInstantExecution()
    {
        $clock = new FakeClock(0);
        $timer = new TimerTaskDecorator($clock);
        $timer->callbackBefore('outer')();
        $timer->callbackBefore('inner')();
        $timer->callbackAfter('inner')();
        $timer->callbackAfter('outer')();
        $this->assertEquals(
            <<<CSV
BEGIN,outer,0,0
BEGIN,inner,0,0
END,inner,0,0
END,outer,0,0
CSV
            ,
            $timer->resultsAsCsv()
        );
    }
    public function testTimer()
    {
        $clock = new FakeClock(1000000000);
        $timer = new TimerTaskDecorator($clock);
        $timer->callbackBefore('outer')();
        $clock->advanceMillis(1);
        $timer->callbackBefore('inner')();
        $clock->advanceMillis(2);
        $timer->callbackAfter('inner')();
        $clock->advanceMillis(4);
        $timer->callbackAfter('outer')();
        $this->assertEquals(
            <<<CSV
BEGIN,outer,1000000000,0
BEGIN,inner,1000000000.001,0
END,inner,1000000000.003,0.002
END,outer,1000000000.007,0.007
CSV
            ,
            $timer->resultsAsCsv()
        );
    }
}
