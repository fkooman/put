<?php

namespace fkooman\Put;

class TestCase
{
    private $assertionCount = 0;

    /**
     * @param mixed $a
     *
     * @return void
     */
    protected function assertTrue($a)
    {
        ++$this->assertionCount;
        if (true === $a) {
            echo '.';

            return;
        }

        // figure out who called us
        $callerInfo = debug_backtrace()[1];
        $calledBy = $callerInfo['function'];
        echo sprintf('ERROR: true !== "%s" (function: %s)', $a, $calledBy).PHP_EOL;
        exit(1);
    }

    /**
     * @param mixed $a
     *
     * @return void
     */
    protected function assertFalse($a)
    {
        ++$this->assertionCount;
        if (false === $a) {
            echo '.';

            return;
        }

        // figure out who called us
        $callerInfo = debug_backtrace()[1];
        $calledBy = $callerInfo['function'];
        echo sprintf('ERROR: false !== "%s" (function: %s)', $a, $calledBy).PHP_EOL;
        exit(1);
    }

    /**
     * @param mixed $a
     * @param mixed $b
     *
     * @return void
     */
    protected function assertInstanceOf($a, $b)
    {
        ++$this->assertionCount;
        // $b must be instance of $a
        if ($b instanceof $a) {
            echo '.';

            return;
        }

        // figure out who called us
        $callerInfo = debug_backtrace()[1];
        $calledBy = $callerInfo['function'];
        echo sprintf('ERROR: "%s" NOT instanceof "%s" (function: %s)', $b, $a, $calledBy).PHP_EOL;
        exit(1);
    }

    /**
     * @param mixed $a
     *
     * @return void
     */
    protected function assertNull($a)
    {
        ++$this->assertionCount;
        if (null === $a) {
            echo '.';

            return;
        }

        // figure out who called us
        $callerInfo = debug_backtrace()[1];
        $calledBy = $callerInfo['function'];
        echo sprintf('ERROR: null !== "%s" (function: %s)', $a, $calledBy).PHP_EOL;
        exit(1);
    }

    /**
     * @param mixed $a
     * @param mixed $b
     *
     * @return void
     */
    protected function assertSame($a, $b)
    {
        ++$this->assertionCount;
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
            echo sprintf('ERROR: types "%s" !== "%s" (function: %s)', $typeA, $typeB, $calledBy).PHP_EOL;
            exit(1);
        }

        if (is_array($a)) {
            // be a bit more clever in helping the developer see what is wrong
            $serializedArrayA = var_export($a, true);
            $serializedArrayB = var_export($b, true);
            echo sprintf('ERROR: array is not the same (function: %s)', $calledBy).PHP_EOL;
            echo '---- FIRST ----'.PHP_EOL;
            echo $serializedArrayA.PHP_EOL;
            echo '---- SECOND ----'.PHP_EOL;
            echo $serializedArrayB.PHP_EOL;
            exit(1);
        }

        echo sprintf('ERROR: "%s" !== "%s" (function: %s)', $a, $b, $calledBy).PHP_EOL;
        exit(1);
    }

    /**
     * @return void
     */
    protected function ok()
    {
        ++$this->assertionCount;
        echo '.';
    }

    /**
     * @return void
     */
    protected function fail()
    {
        ++$this->assertionCount;
        // figure out who called us
        $callerInfo = debug_backtrace()[1];
        $calledBy = $callerInfo['function'];

        echo sprintf('ERROR: FAIL (function: %s)', $calledBy).PHP_EOL;
        exit(1);
    }

    /**
     * @return int
     */
    public function getAssertionCount()
    {
        return $this->assertionCount;
    }
}
