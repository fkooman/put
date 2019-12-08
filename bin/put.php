#!/usr/bin/php
<?php

require_once dirname(__DIR__).'/src/fkooman/Put/TestCase.php';
require_once dirname(__DIR__).'/src/PHPUnit/Framework/TestCase.php';

$projectAutoloader = realpath('vendor/autoload.php');
for ($i = 0; $i < count($argv); ++$i) {
    if ('--bootstrap' === $argv[$i] || '-bootstrap' === $argv[$i]) {
        if ($i + 1 < count($argv)) {
            $projectAutoloader = realpath($argv[++$i]);
        }
    }
}

require_once $projectAutoloader;

/**
 * @param string $startDir
 *
 * @return array<string>
 */
function findAllTestFiles($startDir)
{
    if (false === $fileList = @glob(sprintf('%s/*', $startDir))) {
        return [];
    }
    $testList = [];
    foreach ($fileList as $fileEntry) {
        if (is_dir($fileEntry)) {
            $testList = array_merge($testList, findAllTestFiles(realpath($fileEntry)));
        }
        if ('test.php' === strtolower(substr($fileEntry, -8))) {
            $testList[] = $fileEntry;
        }
    }

    return $testList;
}

// find all *Test.php files in tests/ and subdirs
$testFileList = findAllTestFiles('tests');

if (0 === count($testFileList)) {
    echo 'ERROR: no testable files found in "tests/"'.PHP_EOL;
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
}

echo PHP_EOL;
echo '#Tests      : '.$testCount.PHP_EOL;
echo '#Assertions : '.$assertionCount.PHP_EOL;
if (0 !== $riskyCount) {
    echo '#Risky Tests: '.$riskyCount.PHP_EOL;
}
