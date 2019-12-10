<?php

namespace PHPUnit\Framework;

class ArrayTest extends TestCase
{
    /**
     * @return void
     */
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

//    public function testArrayCompareNotSame()
//    {
//        $firstArray = [
//            'a' => ['b' => 'c'],
//        ];
//        $secondArray = [
//            'a' => ['b' => 'd'],
//        ];

//        $this->assertSame($firstArray, $secondArray);
//    }
}
