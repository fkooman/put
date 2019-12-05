#!/usr/bin/php
<?php

require_once dirname(__DIR__).'/vendor/autoload.php';
// also autoload the project, make autoloader configurable!
require_once 'vendor/autoload.php';

// find all *Test.php files in tests/
// XXX make search recursive
$testFileList = @glob(sprintf('tests/*Test.php'));
if (false === $testFileList || 0 === count($testFileList)) {
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
