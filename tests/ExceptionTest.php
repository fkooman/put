<?php

namespace PHPUnit\Framework;

use Exception;
use RangeException;

class ExceptionTest extends TestCase
{
    public function testException()
    {
        try {
            throw new Exception('foo');
            $this->fail();
        } catch (Exception $e) {
            $this->assertSame('foo', $e->getMessage());
        }
    }

    public function testExpectedException()
    {
        $this->expectException('RangeException');
        throw new RangeException('foo');
    }

//    public function testUnexpectedException()
//    {
//        $this->assertSame('a', 'a');
//        throw new RangeException('foo');
//    }
}
