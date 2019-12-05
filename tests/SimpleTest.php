<?php

namespace fkooman\Put;

use DateTime;

class SimpleTest extends TestCase
{
    public function testDate()
    {
        $dateTime = new DateTime('2019-01-01 08:00:00');
        $this->assertSame('2019-01-01', $dateTime->format('Y-m-d'));
    }
}
