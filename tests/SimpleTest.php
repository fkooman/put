<?php

namespace PHPUnit\Framework;

use DateTime;

class SimpleTest extends TestCase
{
    public function testDate()
    {
        $dateTime = new DateTime('2019-01-01 08:00:00');
        $this->assertSame('2019-01-01', $dateTime->format('Y-m-d'));
    }

    public function testAssertEquals()
    {
        $this->assertEquals(5, '5');
    }

//    public function testAssertSame()
//    {
//        $this->assertSame(5, '5');
//    }
}
