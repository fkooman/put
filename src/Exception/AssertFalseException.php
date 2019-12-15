<?php

namespace fkooman\Put\Exception;

class AssertFalseException extends TestException
{
    /**
     * @param mixed $condition
     */
    public function __construct($condition)
    {
        $message = '--- EXPECTED ---'.PHP_EOL.var_export(false, true).PHP_EOL.'--- ACTUAL ---'.PHP_EOL.var_export($condition, true).PHP_EOL.'--- END ---';
        parent::__construct($message);
    }
}
