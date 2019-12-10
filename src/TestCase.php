<?php

namespace PHPUnit\Framework;

use Exception;
use fkooman\Put\TestException;

class TestCase
{
    /** @var int */
    private $testCount = 0;

    /** @var int */
    private $riskyCount = 0;

    /** @var int */
    private $assertionCount = 0;

    /** @var int */
    private $errorCount = 0;

    /** @var array<TestException> */
    private $errorList = [];

    /** @var string|null */
    private $expectedException = null;

    /**
     * @param string $expected
     *
     * @return void
     */
    protected function expectException($expected)
    {
        ++$this->assertionCount;
        $this->expectedException = $expected;
    }

    /**
     * @return void
     */
    protected function setUp()
    {
    }

    /**
     * @param bool $condition
     *
     * @return void
     */
    protected function assertTrue($condition)
    {
        ++$this->assertionCount;
        if (true !== $condition) {
            throw new TestException('assertTrue');
        }
    }

    /**
     * @param bool $condition
     *
     * @return void
     */
    protected function assertFalse($condition)
    {
        ++$this->assertionCount;
        if (false !== $condition) {
            throw new TestException('assertFalse');
        }
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     *
     * @return void
     */
    protected function assertInstanceOf($expected, $actual)
    {
        ++$this->assertionCount;
        if (!($actual instanceof $expected)) {
            throw new TestException('assertInstanceOf');
        }
    }

    /**
     * @param mixed $variable
     *
     * @return void
     */
    protected function assertNull($variable)
    {
        ++$this->assertionCount;
        if (null !== $variable) {
            throw new TestException('assertNull');
        }
    }

    /**
     * @param mixed $variable
     *
     * @return void
     */
    protected function assertNotNull($variable)
    {
        ++$this->assertionCount;
        if (null === $variable) {
            throw new TestException('assertNotNull');
        }
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     *
     * @return void
     */
    protected function assertEquals($expected, $actual)
    {
        ++$this->assertionCount;
        if ($expected != $actual) {
            throw new TestException('assertEquals');
        }
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     *
     * @return void
     */
    protected function assertSame($expected, $actual)
    {
        ++$this->assertionCount;
        if ($expected !== $actual) {
            throw new TestException('assertSame');
        }
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     *
     * @return void
     */
    protected function assertNotSame($expected, $actual)
    {
        ++$this->assertionCount;
        if ($expected === $actual) {
            throw new TestException('assertNotSame');
        }
    }

    /**
     * @param mixed $variable
     *
     * @return void
     */
    protected function assertNotEmpty($variable)
    {
        ++$this->assertionCount;
        if (empty($variable)) {
            throw new TestException('assertNotEmpty');
        }
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     *
     * @return void
     */
    protected function assertGreaterThanOrEqual($expected, $actual)
    {
        ++$this->assertionCount;
        if ($actual < $expected) {
            throw new TestException('assertGreaterThanOrEqual');
        }
    }

    /**
     * @param string $expected
     * @param mixed  $actual
     *
     * @return void
     */
    protected function assertInternalType($expected, $actual)
    {
        ++$this->assertionCount;
        if ($expected !== gettype($actual)) {
            throw new TestException('assertInternalType');
        }
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
        throw new TestException('fail');
    }

    /**
     * @return int
     */
    public function noOfAssertions()
    {
        return $this->assertionCount;
    }

    /**
     * @return int
     */
    public function noOfTests()
    {
        return $this->testCount;
    }

    /**
     * @return int
     */
    public function noOfRiskyTests()
    {
        return $this->riskyCount;
    }

    /**
     * @return int
     */
    public function noOfErrors()
    {
        return $this->errorCount;
    }

    /**
     * @return array<TestException>
     */
    public function errorList()
    {
        return $this->errorList;
    }

    /**
     * @return void
     */
    public function run()
    {
        $classMethods = get_class_methods($this);
        // find all methods with a name that start with test and call them
        foreach ($classMethods as $classMethod) {
            try {
                // if "setUp" method is there, always run it before the test method
                if (method_exists($this, 'setUp')) {
                    $this->setUp();
                }

                if (0 === strpos($classMethod, 'test')) {
                    $preAssertionCount = $this->assertionCount;
                    ++$this->testCount;
                    $expectedException = null;
                    try {
                        $this->$classMethod();
                        // did we expect an exception but didn't get one?
                        $expectedException = $this->expectedException;
                        if (null !== $expectedException) {
                            throw new TestException(sprintf('no exception "%s" thrown in "%s"', $expectedException, $classMethod));
                        }
                    } catch (Exception $e) {
                        // is this needed? FIXME
                        if ($e instanceof TestException) {
                            throw $e;
                        }
                        // did we expect one?!
                        $expectedException = $this->expectedException;
                        if (null === $expectedException) {
                            throw new TestException(sprintf('unexpected exception "%s" thrown in "%s"', get_class($e), $classMethod));
                        }
                        if (get_class($e) !== $expectedException) {
                            throw new TestException(sprintf('exception "%s" thrown, expected type "%s" in "%s"', $expectedException, get_class($e), $classMethod));
                        }
                    }
                    $this->expectedException = null;
                    $postAssertionCount = $this->assertionCount;
                    if ($preAssertionCount === $postAssertionCount) {
                        echo 'R';
                        ++$this->riskyCount;
                    } else {
                        echo '.';
                    }
                }
            } catch (TestException $e) {
                echo 'E';
                ++$this->errorCount;
                $this->errorList[] = $e;
            }
        }
    }
}
