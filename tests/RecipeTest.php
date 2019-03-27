<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

use PHPUnit\Framework\TestCase;

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

    public function testTimerWithCsvResult()
    {
        $recipeFile = __DIR__ . '/../recipe/timer.php';
        $csvFile = $this->createTmpFile();
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
        exec('vendor/bin/dep --file=' . $this->deployFile . ' test', $output);
        $this->assertContains(
            'Test Output',
            $output,
            'Test task should be executed.' . "\n" .
            'Expected "Test Output"' . "\n" .
            'Actual Output: ' . print_r($output, true)
        );
        $this->assertRegExp(<<<'REGEX'
{BEGIN,test,[\d.]+,[\d.]+
END,test,[\d.]+,[\d.]+}
REGEX

            , (string)file_get_contents($csvFile), 'CSV file should be written with timer results');
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
}
