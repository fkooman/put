<?php

namespace fkooman\Put;

use DateTime;
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    /**
     * @return void
     */
    public function testDate()
    {
        $dateTime = new DateTime('2019-01-01 08:00:00');
        $this->assertSame('2019-01-01', $dateTime->format('Y-m-d'));
    }

    /**
     * @return void
     */
    public function testAssertEquals()
    {
        $this->assertEquals(5, '5');
    }

//    public function testAssertSame()
//    {
//        $this->assertSame(5, '5');
//    }
}
