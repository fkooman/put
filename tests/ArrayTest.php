<?php

namespace PHPUnit\Framework;

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

        $this->assertSame($firstArray, $secondArray);
    }
}
