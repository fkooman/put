<?php

function testArrayCompare()
{
    $firstArray = [
        'a' => ['b' => 'c']
    ];
    $secondArray = [
        'a' => ['b' => 'c']
    ];

    assert_same($firstArray, $secondArray);
}
