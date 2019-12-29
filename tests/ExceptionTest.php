<?php

namespace fkooman\Put;

use Exception;
use PHPUnit\Framework\TestCase;
use RangeException;

class ExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testException()
    {
        try {
            throw new Exception('foo');
        } catch (Exception $e) {
            $this->assertSame('foo', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testExpectedExceptionString()
    {
        $this->expectException('RangeException');
        $this->expectExceptionMessage('foo');
        throw new RangeException('foo');
    }

    /**
     * @return void
     */
    public function testExpectedExceptionClass()
    {
        $this->expectException(RangeException::class);
        $this->expectExceptionMessage('foo');

        throw new RangeException('foo');
    }

//    public function testUnexpectedException()
//    {
//        $this->assertSame('a', 'a');
//        throw new RangeException('foo');
//    }
}
