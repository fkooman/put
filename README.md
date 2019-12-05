# PHP Unit Testing For the Insane

So, after attending a presentation on PHP and mocking I started evaluating
whether or not I actually need PHPUnit. It seems the only functions I use are
`assertSame()`, `fail()` and `ok()` for running my tests. That seems hardly 
enough justification for having PHPUnit as a (development) dependency.

That is when I decided to see how difficult it would be to create my own unit 
tester. Turns out, not *that* difficult for basic functionality.

We do NOT aim at full PHPUnit compatiblity, only the stuff that is really 
useful will be implemented. Write your own mock classes!

## Writing Tests

In the `tests/` folder of your project you can write your tests. The idea is 
to use `TestCase`, just like in PHPUnit, but with a different name space, i.e. 
`fkooman\Put\TestCase`. For example:

    <?php

    class SimpleTest extends \fkooman\Put\TestCase
    {
        public function testDate()
        {
            $dateTime = new \DateTime('2019-01-01 08:00:00');
            self::assertSame('2019-01-01', $dateTime->format('Y-m-d'));
        }
    }

Now you can simply run `put` in your project folder which contains the `tests/` 
folder and you are good to go:
	
	$ put
	.
	$ echo $?
	0

In case your test fails:

	$ put
	"2019-01-02" !== "2019-01-01" (function: testDate)
	$ echo $?
	1
	
### Comparison

We have the following comparison functions implemented as of now:

* `assertSame()`
* `assertTrue()`
* `assertFalse()`
* `assertNull()`
* `assertInstanceOf()`

### Exceptions

In order to test exceptions, we have the `ok()` and `fail()` functions.

As an example:

    <?php

    use Exception;

    class ExceptionTest extends \fkooman\Put\TestCase
    {
        public function testException()
        {
            try {
                throw new Exception('foo');
                self::fail();
            } catch (Exception $e) {
                self::assertSame('foo', $e->getMessage());
            }
        }
    }

If you don't care about the exception message you can use `ok()` instead of the
`assertSame()`.
