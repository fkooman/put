<?php

namespace fkooman\Put\Exception;

class AssertNullException extends TestException
{
    /**
     * @param mixed $condition
     */
    public function __construct($condition)
    {
        $message = '--- EXPECTED ---'.PHP_EOL.print_r(null, true).PHP_EOL.'--- ACTUAL ---'.PHP_EOL.print_r($condition, true).PHP_EOL.'--- END ---';
        parent::__construct($message);
    }
}
