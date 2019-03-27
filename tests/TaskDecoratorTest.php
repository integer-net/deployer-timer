<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

use Deployer\Console\Application;
use Deployer\Console\Output\OutputWatcher;
use Deployer\Deployer;
use Deployer\Host\Localhost;
use Deployer\Task\Context;
use Deployer\Task\Task;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use function Deployer\invoke;
use function Deployer\task;
use function Deployer\writeln;

class TaskDecoratorTest extends TestCase
{
    /**
     * @var Deployer
     */
    private $deployer;
    /**
     * @var InputInterface
     */
    private $input;
    /**
     * @var BufferedOutput
     */
    private $output;

    protected function setUp(): void
    {
        $this->input = new ArrayInput([]);
        $this->output = new BufferedOutput();
        $this->deployer = new Deployer(new Application());
        $this->deployer->init();
        $this->deployer['input'] = $this->input;
        $this->deployer['output'] = new OutputWatcher($this->output);
    }

    public function testWithSingleTask(): void
    {
        $this->givenTaskThatOutputsName('dummy');
        $this->givenDecoratorThatOutputsName('decorator');
        $this->whenTaskRuns('dummy');
        $this->thenOutputContainsLinesInOrder(
            [
                'dummy.decorator.before',
                'dummy',
                'dummy.decorator.after',
            ],
            $this->output->fetch()
        );
    }

    public function testWithNestedTasks(): void
    {
        $this->givenTaskThatOutputsName('inner_1');
        $this->givenTaskThatOutputsName('inner_2');
        $this->givenTaskGroup('outer', ['inner_1', 'inner_2']);
        $this->givenDecoratorThatOutputsName('decorator');
        $this->whenTaskRuns('outer');
        $this->thenOutputContainsLinesInOrder(
            [
                'outer.decorator.before',
                'inner_1.decorator.before',
                'inner_1',
                'inner_1.decorator.after',
                'inner_2.decorator.before',
                'inner_2',
                'inner_2.decorator.after',
                'outer.decorator.after',
            ],
            $this->output->fetch()
        );
    }

    private function thenOutputContainsLinesInOrder(array $expectedLines, string $actualOutput): void
    {
        $this->assertEquals($expectedLines, array_intersect($expectedLines, explode("\n", $actualOutput)));
    }

    private function whenTaskRuns(string $taskName): void
    {
        Context::push(new Context(new Localhost(), $this->input, $this->output));
        invoke($taskName);
    }

    private function givenTaskThatOutputsName(string $name): Task
    {
        return task(
            $name,
            function () use ($name) {
                writeln($name);
            }
        );
    }

    private function givenTaskGroup(string $name, array $tasks): void
    {
        task($name, $tasks);
    }

    private function givenDecoratorThatOutputsName(string $name)
    {
        $decorator = new class($name) implements TaskDecorator
        {
            private $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }

            public function name(): string
            {
                return $this->name;
            }

            public function callbackBefore(string $taskName): callable
            {
                return function () use ($taskName) {
                    writeln($taskName . '.' . $this->name . '.before');
                };
            }

            public function callbackAfter(string $taskName): callable
            {
                return function () use ($taskName) {
                    writeln($taskName . '.' . $this->name . '.after');
                };
            }

        };
        $decorateAllTasks = new DecorateAllTasks($this->deployer);
        $decorateAllTasks->with($decorator);
    }
}
