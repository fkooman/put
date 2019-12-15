<?php

namespace fkooman\Put\Exception;

class AssertNotSameException extends TestException
{
    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function __construct($expected, $actual)
    {
        $message = '--- EXPECTED ---'.PHP_EOL.var_export($expected, true).PHP_EOL.'--- ACTUAL ---'.PHP_EOL.var_export($actual, true).PHP_EOL.'--- END ---';
        parent::__construct($message);
    }
}
