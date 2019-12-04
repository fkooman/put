<?php

/**
 * @return void
 */
function fail()
{
    // figure out who called us
    $callerInfo = debug_backtrace()[1];
    $calledBy = $callerInfo['function'];

    echo sprintf('FAIL (function: %s)', $calledBy).PHP_EOL;
    exit(1);
}

/**
 * @return void
 */
function ok()
{
    echo '.';
}

/**
 * @param mixed $a
 * @param mixed $b
 * @return void
 */
function assert_same($a, $b)
{
    if ($a === $b) {
        echo '.';

        return;
    }

    // figure out who called us
    $callerInfo = debug_backtrace()[1];
    $calledBy = $callerInfo['function'];

    echo sprintf('"%s" !== "%s" (function: %s)', $a, $b, $calledBy) . PHP_EOL;
    exit(1);
}

// find all test*.php files
$testFileList = @glob(sprintf('tests/test*.php'));
if (false === $testFileList || 0 === count($testFileList)) {
    echo 'ERROR: no testable files found in "tests/"' . PHP_EOL;
    exit(1);
}

foreach ($testFileList as $testFile) {
    // limitation: one can not redefine the same function in different files,
    // so EVERY test function MUST have a unique name
    include $testFile;
}

// find the user defined functions and call them!
$functionList = get_defined_functions();
$userFunctionList = $functionList['user'];
foreach ($userFunctionList as $userFunction) {
    // call all functions where the name starts with "test"
    if (0 === strpos($userFunction, 'test')) {
        call_user_func($userFunction);
    }
}
