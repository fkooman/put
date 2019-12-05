<?php

namespace fkooman\Put;

use Exception;

class ExceptionTest extends TestCase
{
    public function testException()
    {
        try {
            throw new Exception('foo');
            self::fail();
        } catch (Exception $e) {
            self::assertSame('foo', $e->getMessage());
        }
    }
}
