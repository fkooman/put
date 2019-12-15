<?php

namespace fkooman\Put\Exception;

class AssertInternalTypeException extends TestException
{
    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function __construct($expected, $actual)
    {
        $message = '--- EXPECTED ---'.PHP_EOL.print_r($expected, true).PHP_EOL.'--- ACTUAL ---'.PHP_EOL.print_r($actual, true).PHP_EOL.'--- END ---';
        parent::__construct($message);
    }
}
