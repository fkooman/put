<?php

namespace fkooman\Put;

use DateTime;
use PHPUnit\Framework\TestCase;

class SkippedTest extends TestCase
{
    /**
     * @return void
     */
    public function testDate()
    {
        if (!extension_loaded('foo')) {
            $this->markTestSkipped();

            return;
        }

        $dateTime = new DateTime('2019-01-01 08:00:00');
        $this->assertSame('2019-01-01', $dateTime->format('Y-m-d'));
    }
}
