<?php

namespace fkooman\Put;

class TestCase
{
    /** @var int */
    private $testCount = 0;

    /** @var int */
    private $riskyCount = 0;

    /** @var int */
    private $assertionCount = 0;

    /** @var string|null */
    private $expectedException = null;

    /**
     * @param string $a
     *
     * @return void
     */
    protected function expectException($a)
    {
        ++$this->assertionCount;
        $this->expectedException = $a;
    }

    /**
     * @param mixed $a
     *
     * @return void
     */
    protected function assertTrue($a)
    {
        ++$this->assertionCount;
        if (true === $a) {
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
    protected function assertEquals($a, $b)
    {
        ++$this->assertionCount;
        if ($a == $b) {
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

        echo sprintf('ERROR: "%s" != "%s" (function: %s)', $a, $b, $calledBy).PHP_EOL;
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

    /**
     * @return int
     */
    public function getTestCount()
    {
        return $this->testCount;
    }

    /**
     * @return int
     */
    public function getRiskyCount()
    {
        return $this->riskyCount;
    }

    /**
     * @return void
     */
    public function run()
    {
        $classMethods = get_class_methods($this);
        // find all methods with a name that start with test and call them
        foreach ($classMethods as $classMethod) {
            // if setup is there, always run it before the test method!
            if (in_array('setUp', $classMethods)) {
                $this->setUp();
            }
            if (0 === strpos($classMethod, 'test')) {
                $preAssertionCount = $this->assertionCount;
                ++$this->testCount;
                $this->expectedException = null;
                try {
                    $this->$classMethod();
                    // did we expect an exception but didn't get one?
                    if (null !== $this->expectedException) {
                        die('WAAA, no exception thrown!');
                    }
                } catch (\Exception $e) {
                    // did we expect one?!
                    if (null === $this->expectedException) {
                        // we got one, but did not expect one!
                        die('WAAA, we got exception but did not expect one!');
                    }
                    if (get_class($e) !== $this->expectedException) {
                        die('WAA, wrong exception received');
                    }
                }
                $postAssertionCount = $this->assertionCount;
                if ($preAssertionCount === $postAssertionCount) {
                    echo 'R';
//                    echo ':('.$classMethod.')';
                    ++$this->riskyCount;
                } else {
                    echo '.';
                }
            }
        }
    }
}
