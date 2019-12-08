#!/usr/bin/php
<?php

require_once dirname(__DIR__).'/src/TestCase.php';
require_once dirname(__DIR__).'/src/TestException.php';

$projectAutoloader = realpath('vendor/autoload.php');
$testSuffix = 'Test.php';
$testFolder = 'tests';

for ($i = 1; $i < count($argv); ++$i) {
    if ('--bootstrap' === $argv[$i] || '-bootstrap' === $argv[$i]) {
        if ($i + 1 < count($argv)) {
            $projectAutoloader = realpath($argv[++$i]);
        }
        continue;
    }
    if ('--suffix' === $argv[$i] || '-suffix' === $argv[$i]) {
        if ($i + 1 < count($argv)) {
            $testSuffix = $argv[++$i];
        }
        continue;
    }
    // if we have an argument that is not any of these, it must be the "tests"
    // folder...
    $testFolder = $argv[$i];
}

require_once $projectAutoloader;

/**
 * @param string $startDir
 * @param string $testSuffix
 *
 * @return array<string>
 */
function findAllTestFiles($startDir, $testSuffix)
{
    if (false === $fileList = @glob(sprintf('%s/*', $startDir))) {
        return [];
    }
    $testList = [];
    foreach ($fileList as $fileEntry) {
        if (is_dir($fileEntry)) {
            $testList = array_merge($testList, findAllTestFiles(realpath($fileEntry), $testSuffix));
        }
        if ($testSuffix === substr($fileEntry, -strlen($testSuffix))) {
            $testList[] = $fileEntry;
        }
    }

    return $testList;
}

// find all *Test.php files in tests/ and subdirs
$testFileList = findAllTestFiles($testFolder, $testSuffix);

if (0 === count($testFileList)) {
    echo sprintf('ERROR: no testable files found in "%s/"', $testFolder).PHP_EOL;
    exit(1);
}

$classesToTest = [];
foreach ($testFileList as $testFile) {
    include $testFile;
    $declaredClasses = get_declared_classes();
    $className = basename($testFile, '.php');
    foreach ($declaredClasses as $declaredClass) {
        // make sure we only find the testable classes!
        // run get_declared_classes() first BEFORE starting to include files
        // and diff it
        if ('Test' === substr($declaredClass, -4)) {
            $classesToTest[] = $declaredClass;
        }
    }
}

$assertionCount = 0;
$testCount = 0;
$riskyCount = 0;
$errorCount = 0;
$errorList = [];
$alreadyTested = [];
foreach ($classesToTest as $classToTest) {
    if (in_array($classToTest, $alreadyTested)) {
        continue;
    }
    $alreadyTested[] = $classToTest;
    $c = new $classToTest();
    $c->run();
    $assertionCount += $c->getAssertionCount();
    $testCount += $c->getTestCount();
    $riskyCount += $c->getRiskyCount();
    $errorCount += $c->getErrorCount();
    $errorList = array_merge($errorList, $c->getErrorList());
}

echo PHP_EOL;
echo '#Tests      : '.$testCount.PHP_EOL;
echo '#Assertions : '.$assertionCount.PHP_EOL;
if (0 !== $riskyCount) {
    echo '#Risky Tests: '.$riskyCount.PHP_EOL;
}
if (0 !== $errorCount) {
    echo '#Errors     : '.$errorCount.PHP_EOL;
    foreach ($errorList as $error) {
        echo '**** ERROR ****'.PHP_EOL.$error->getMessage().PHP_EOL.$error->getTraceAsString().PHP_EOL.PHP_EOL;
    }
    exit(1);
}
