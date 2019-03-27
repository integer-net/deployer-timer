<?php
declare(strict_types=1);

namespace IntegerNet\DeployerTimer;

use Deployer\Deployer;
use PHPUnit\Framework\TestCase;
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

    public function testTimer()
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

timerCsv('{$csvFile}');
PHP
        );
        exec('vendor/bin/dep --file=' . $this->deployFile . ' test', $output);
        $this->assertContains(
            'Test Output',
            $output,
            'Test task should be executed.' . "\n" .
            'Expected "Test Output"' . "\n" .
            'Actual Output: ' . print_r($output,true)
        );
        $this->assertRegExp(<<<'REGEX'
{BEGIN,test,[\d.]+,[\d.]+
END,test,[\d.]+,[\d.]+}
REGEX

            , file_get_contents($csvFile), 'CSV file should be written with timer results');
    }

    private function createTmpFile()
    {
        $fileName = tempnam(sys_get_temp_dir(), 'integer-net-deployer-timer');
        $this->tmpFiles[] = $fileName;
        return $fileName;
    }
}
