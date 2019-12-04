<?php

function testException()
{
    try {
        throw new Exception('foo');
        fail();
    } catch (Exception $e) {
        assert_same('foo', $e->getMessage());
    }
}
