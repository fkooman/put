<?php

namespace fkooman\Put;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class Put
{
    /**
     * @param array<string> $argv
     * @param string        $currentWorkingDir
     *
     * @return void
     */
    public function run(array $argv, $currentWorkingDir)
    {
        if (null === $projectConfig = self::parseCommandLine($argv)) {
            // show help
            $helpText = 'USAGE'.PHP_EOL;
            $helpText .= '    '.$argv[0].' [OPTION]... TEST_DIRECTORY'.PHP_EOL;
            $helpText .= 'OPTION'.PHP_EOL;
            $helpText .= '    --help                            Show this message'.PHP_EOL;
            $helpText .= '    --bootstrap [vendor/autoload.php] Specify path to PHP script containing the autoloader'.PHP_EOL;
            $helpText .= '    --suffix [Test.php]               Specify the test suffix for files to tests in'.PHP_EOL;
            $helpText .= '    --coverage [report.html]          Enable test code coverage'.PHP_EOL;
            $helpText .= 'EXAMPLE'.PHP_EOL;
            $helpText .= '    '.$argv[0].' tests/'.PHP_EOL;
            echo $helpText;
            exit(0);
        }

        if (file_exists($projectConfig['projectAutoloader'])) {
            require_once $projectConfig['projectAutoloader'];
        }
        $testFileList = self::findTestFiles($projectConfig['testsFolder'], $projectConfig['testsSuffix']);
        if (0 === count($testFileList)) {
            echo sprintf('ERROR: no testable files found in "%s/"', $projectConfig['testsFolder']).PHP_EOL;
            exit(1);
        }

        $classesToTest = [];
        foreach ($testFileList as $testFile) {
            if (file_exists($testFile)) {
                include_once $testFile;
            }
            $declaredClasses = get_declared_classes();
            foreach ($declaredClasses as $declaredClass) {
                // make sure we only find the testable classes!
                // run get_declared_classes() first BEFORE starting to include files
                // and diff it
                if ('Test' === substr($declaredClass, -4)) {
                    $classesToTest[] = $declaredClass;
                }
            }
        }

        $coverageExtensionLoaded = extension_loaded('pcov');
        if (null !== $projectConfig['coverageOutputFile']) {
            if (!$coverageExtensionLoaded) {
                throw new RuntimeException('"ext-pcov" not available, unable to perform code coverage');
            }
            \pcov\start();
        }

        $assertionCount = 0;
        $testCount = 0;
        $riskyCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $errorList = [];
        $alreadyTested = [];
        foreach ($classesToTest as $classToTest) {
            if (in_array($classToTest, $alreadyTested, true)) {
                continue;
            }
            $alreadyTested[] = $classToTest;
            if (false === class_exists($classToTest, false)) {
                continue;
            }
            $c = new $classToTest();
            if (!($c instanceof TestCase)) {
                continue;
            }

            $c->run();
            $assertionCount += $c->noOfAssertions();
            $testCount += $c->noOfTests();
            $riskyCount += $c->noOfRiskyTests();
            $skippedCount += $c->noOfSkippedTests();
            $errorCount += $c->noOfErrors();
            $errorList = array_merge($errorList, $c->errorList());
        }

        if (null !== $projectConfig['coverageOutputFile']) {
            if ($coverageExtensionLoaded) {
                \pcov\stop();
                Coverage::writeReport($projectConfig['coverageOutputFile'], $currentWorkingDir, \pcov\collect());
            }
        }

        echo PHP_EOL;
        echo '#Tests        : '.$testCount.PHP_EOL;
        echo '#Assertions   : '.$assertionCount.PHP_EOL;
        if (0 !== $riskyCount) {
            echo '#Risky Tests  : '.$riskyCount.PHP_EOL;
        }
        if (0 !== $skippedCount) {
            echo '#Skipped Tests: '.$skippedCount.PHP_EOL;
        }
        if (0 !== $errorCount) {
            echo '#Errors     : '.$errorCount.PHP_EOL;
            foreach ($errorList as $error) {
                echo '**** ERROR ****'.PHP_EOL.'['.get_class($error).']'.PHP_EOL.$error->getMessage().PHP_EOL.$error->getTraceAsString().PHP_EOL.PHP_EOL;
            }
            exit(1);
        }
    }

    /**
     * @param array<string> $argv
     *
     * @return array{coverageOutputFile:string|null,projectAutoloader:string,testsSuffix:string,testsFolder:string}|null
     */
    private static function parseCommandLine(array $argv)
    {
        $coverageOutputFile = null;
        $testsFolder = 'tests';
        $testsSuffix = 'Test.php';
        $projectAutoloader = 'vendor/autoload.php';

        for ($i = 1; $i < count($argv); ++$i) {
            if ('--help' === $argv[$i] || '-help' === $argv[$i]) {
                return null;
            }
            if ('--bootstrap' === $argv[$i] || '-bootstrap' === $argv[$i]) {
                if ($i + 1 < count($argv)) {
                    $projectAutoloader = $argv[++$i];
                }
                continue;
            }
            if ('--suffix' === $argv[$i] || '-suffix' === $argv[$i]) {
                if ($i + 1 < count($argv)) {
                    $testsSuffix = $argv[++$i];
                }
                continue;
            }
            if ('--coverage' === $argv[$i] || '-coverage' === $argv[$i]) {
                $coverageOutputFile = 'report.html';
                if ($i + 1 < count($argv)) {
                    $coverageOutputFile = $argv[++$i];
                }
                continue;
            }
            // if we have an argument that is not any of these, it must be the "tests"
            // folder...
            $testsFolder = $argv[$i];
        }

        return [
            'testsFolder' => $testsFolder,
            'testsSuffix' => $testsSuffix,
            'projectAutoloader' => $projectAutoloader,
            'coverageOutputFile' => $coverageOutputFile,
        ];
    }

    /**
     * @param string $testsFolder
     * @param string $testsSuffix
     *
     * @return array<string>
     */
    private static function findTestFiles($testsFolder, $testsSuffix)
    {
        if (false === $fileList = @glob(sprintf('%s/*', $testsFolder))) {
            return [];
        }
        $testList = [];
        foreach ($fileList as $fileEntry) {
            if (is_dir($fileEntry)) {
                $testList = array_merge($testList, self::findTestFiles($fileEntry, $testsSuffix));
            }
            if ($testsSuffix === substr($fileEntry, -strlen($testsSuffix))) {
                $testList[] = $fileEntry;
            }
        }

        return $testList;
    }
}
