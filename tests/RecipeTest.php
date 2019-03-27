<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

use Deployer\Console\Application;
use Deployer\Deployer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\Process;

class RecipeTest extends TestCase
{
    /**
     * @var string
     */
    private $deployFile;

    /**
     * @var string[]
     */
    private $tmpFiles = [];

    protected function setUp(): void
    {
        $this->deployFile = $this->createTmpFile();
    }

    protected function tearDown(): void
    {
        foreach ($this->tmpFiles as $tmpFile) {
            unlink($tmpFile);
        }
    }

    /**
     * Stable integration test
     */
    public function testTimerWithCsvResultInSeparateProcess()
    {
        $csvFile = $this->createTmpFile();
        $this->givenDeployFileWithCsvResultTask($csvFile);
        $this->whenExecuted(['vendor/bin/dep', '--file=' . $this->deployFile, 'test'], $process);
        $this->thenTestTaskOutputShouldBeVisibleIn(explode("\n", $process->getOutput()));
        $this->andCsvFileShouldContainTestTaskResults($csvFile);
    }

    /**
     * Same test that runs deployer directly, without exiting, to measure code coverage
     */
    public function testTimerWithCsvResult()
    {
        $csvFile = $this->createTmpFile();
        $this->givenDeployFileWithCsvResultTask($csvFile);
        $this->whenDeployerRuns($output);
        $this->thenTestTaskOutputShouldBeVisibleIn(explode("\n", $output->fetch()));
        $this->andCsvFileShouldContainTestTaskResults($csvFile);
    }

    private function createTmpFile()
    {
        $fileName = tempnam(sys_get_temp_dir(), 'integer-net-deployer-timer');
        if (!$fileName) {
            throw new \RuntimeException('Could not create temporary file');
        }
        $this->tmpFiles[] = $fileName;
        return $fileName;
    }

    private function givenDeployFileWithCsvResultTask($csvFile): void
    {
        $recipeFile = __DIR__ . '/../recipe/timer.php';
        file_put_contents(
            $this->deployFile,
            <<<PHP
<?php
namespace Deployer;

require '{$recipeFile}';

task('test', function() { writeln('Test Output');});

after('test', timer()->createCsvResultTask('{$csvFile}'));
PHP
        );
    }

    private function thenTestTaskOutputShouldBeVisibleIn(array $outputLines): void
    {
        $this->assertContains(
            'Test Output',
            $outputLines,
            'Test task should be executed.' . "\n" . 'Expected "Test Output"' . "\n" . 'Actual Output: ' . print_r(
                $outputLines,
                true
            )
        );
    }

    private function andCsvFileShouldContainTestTaskResults($csvFile): void
    {
        $this->assertRegExp(
            <<<'REGEX'
{BEGIN,test,[\d.]+,[\d.]+
END,test,[\d.]+,[\d.]+}
REGEX

            ,
            (string)file_get_contents($csvFile),
            'CSV file should be written with timer results'
        );
    }

    private function whenExecuted(array $command, ?Process &$process): void
    {
        $process = new Process($command);
        $process->run();
    }

    private function whenDeployerRuns(?BufferedOutput &$output): void
    {
        $input = new ArrayInput(['test']);
        $output = new BufferedOutput();
        $console = new Application('Deployer', 'master');
        $console->setAutoExit(false);
        $deployer = new Deployer($console);
        require $this->deployFile;
        $deployer->init();
        $console->run($input, $output);
    }
}
