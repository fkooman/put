<?php

namespace fkooman\Put;

class ArrayTest extends TestCase
{
    public function testArrayCompare()
    {
        $firstArray = [
            'a' => ['b' => 'c'],
        ];
        $secondArray = [
            'a' => ['b' => 'c'],
        ];

        self::assertSame($firstArray, $secondArray);
    }
}
