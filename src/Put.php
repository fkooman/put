<?php

namespace fkooman\Put;

use PHPUnit\Framework\TestCase;

class Put
{
    /**
     * @param array<string> $argv
     *
     * @return void
     */
    public function run(array $argv)
    {
        $projectConfig = self::parseCommandLine($argv);
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

        \pcov\start();

        $assertionCount = 0;
        $testCount = 0;
        $riskyCount = 0;
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
            $errorCount += $c->noOfErrors();
            $errorList = array_merge($errorList, $c->errorList());
        }

        \pcov\stop();
        Coverage::writeReport('cov_output.html', \pcov\collect());

        echo PHP_EOL;
        echo '#Tests      : '.$testCount.PHP_EOL;
        echo '#Assertions : '.$assertionCount.PHP_EOL;
        if (0 !== $riskyCount) {
            echo '#Risky Tests: '.$riskyCount.PHP_EOL;
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
     * @return array{projectAutoloader:string,testsSuffix:string,testsFolder:string}
     */
    private static function parseCommandLine(array $argv)
    {
        $testsFolder = 'tests';
        $testsSuffix = 'Test.php';
        $projectAutoloader = 'vendor/autoload.php';

        for ($i = 1; $i < count($argv); ++$i) {
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
            // if we have an argument that is not any of these, it must be the "tests"
            // folder...
            $testsFolder = $argv[$i];
        }

        return [
            'testsFolder' => $testsFolder,
            'testsSuffix' => $testsSuffix,
            'projectAutoloader' => $projectAutoloader,
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
