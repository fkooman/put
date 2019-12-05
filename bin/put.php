#!/usr/bin/php
<?php

require_once dirname(__DIR__).'/vendor/autoload.php';
// also autoload the project, make autoloader configurable!
require_once 'vendor/autoload.php';


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
        if ('Test.php' === substr($fileEntry, -8)) {
            $testList[] = $fileEntry;
        }
    }

    return $testList;
}

// find all *Test.php files in tests/ and subdirs
$testFileList = findAllTestFiles(dirname(__DIR__).'/tests');

if (0 === count($testFileList)) {
    echo 'ERROR: no testable files found in "tests/"' . PHP_EOL;
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

foreach ($classesToTest as $classToTest) {
//    echo $classToTest . PHP_EOL;
    $c = new $classToTest();
    $classMethods = get_class_methods($c);
    // find all methods with a name that start with test and call them
    foreach ($classMethods as $classMethod) {
        // if setup is there, always run it before the test method!
        if (in_array('setUp', $classMethods)) {
            $c->setUp();
        }
        if (0 === strpos($classMethod, 'test')) {
            $c->$classMethod();
        }
    }
}

echo PHP_EOL;
