<?php

function testDate()
{
    $dateTime = new DateTime('2019-01-01 08:00:00');
    assert_same('2019-01-01', $dateTime->format('Y-m-d'));
}
