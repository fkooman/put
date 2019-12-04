<?php

/**
 * @return void
 */
function fail()
{
    // figure out who called us
    $callerInfo = debug_backtrace()[1];
    $calledBy = $callerInfo['function'];

    echo sprintf('ERROR: FAIL (function: %s)', $calledBy).PHP_EOL;
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

    $typeA = gettype($a);
    $typeB = gettype($b);
    if ($typeA !== $typeB) {
        echo sprintf('ERROR: types "%s" !== "%s" (function: %s)', $typeA, $typeB, $calledBy) . PHP_EOL;
        exit(1);
    }

    if (is_array($a)) {
        // be a bit more clever in helping the developer see what is wrong
        $serializedArrayA = var_export($a, true);
        $serializedArrayB = var_export($b, true);
        echo sprintf('ERROR: array is not the same (function: %s)', $calledBy) . PHP_EOL;
        echo '---- FIRST ----' . PHP_EOL;
        echo $serializedArrayA . PHP_EOL;
        echo '---- SECOND ----' . PHP_EOL;
        echo $serializedArrayB . PHP_EOL;
        exit(1);
    }

    echo sprintf('ERROR: "%s" !== "%s" (function: %s)', $a, $b, $calledBy) . PHP_EOL;
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
