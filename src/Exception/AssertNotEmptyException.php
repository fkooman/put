<?php

namespace fkooman\Put\Exception;

class AssertNotEmptyException extends TestException
{
    /**
     * @param mixed $condition
     */
    public function __construct($condition)
    {
        $message = '--- EXPECTED ---'.PHP_EOL.'!empty'.PHP_EOL.'--- ACTUAL ---'.PHP_EOL.var_export($condition, true).PHP_EOL.'--- END ---';
        parent::__construct($message);
    }
}
