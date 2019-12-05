<?php

namespace fkooman\Put;

use Exception;

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
}
