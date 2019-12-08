# PHP Unit Testing For the Insane

So, after attending a presentation on PHP and mocking I started evaluating
whether or not I actually need PHPUnit. It seems the only functions I use are
`assertSame()`, `fail()` and `ok()` for running my tests. That seems hardly 
enough justification for having PHPUnit as a (development) dependency.

That is when I decided to see how difficult it would be to create my own unit 
tester. Turns out, not *that* difficult for the very basic functionality.

We do NOT aim at full PHPUnit compatibility, only the stuff that is really 
useful will be implemented. Write your own mock classes!

## Dependency

In your project's `composer.json`:

    "repositories": [
        {
            "type": "vcs",
            "url": "https://git.tuxed.net/fkooman/put"
        }
    ],

    ...

    "require-dev": {
        "fkooman/put": "dev-master"
    },

Do not forget to run `composer update`.

## Writing Tests

Writing tests is the same as for PHPUnit. Put them in the `tests/` directory of 
your project. A simple example:

    <?php

    namespace my\app;

    use DateTime;
    use PHPUnit\Framework\TestCase;

    class SimpleTest extends TestCase
    {
        public function testDate()
        {
            $dateTime = new DateTime('2019-01-01 08:00:00');
            $this->assertSame('2019-01-01', $dateTime->format('Y-m-d'));
        }
    }

### Assertions

We have the following functions are implemented as of now:

* `assertSame()`
* `assertEquals()`
* `assertTrue()`
* `assertFalse()`
* `assertNull()`
* `assertInstanceOf()`
* `ok()`
* `fail()`

### Exceptions

There are two ways to test exceptions. The first is by using `expectException` 
at the start of the your test method, e.g.:

    $this->expectException('RangeException');

The second way is to catch the exceptions yourself. As an example:

    <?php

    namespace my\app;

    use Exception;
    use PHPUnit\Framework\TestCase;

    class ExceptionTest extends TestCase
    {
        public function testException()
        {
            try {
                throw new Exception('foo');
                $this->fail();
            } catch (Exception $e) {
                $this->assertSame('foo', $e->getMessage());
            }
        }
    }

If you don't care about the exception message you can use `ok()` instead of the
`assertSame()`.

## Running Tests

Assuming you added `put` to your `composer.json`, you can simply run it:

    $ vendor/bin/put
    .............
    #Tests      : 13
    #Assertions : 14
    $ echo $?
    0

There are 3 parameters to `put`. You can specify the project's autoloader with 
the `--bootstrap` flag, and the test file suffix with the `--suffix` flag. You 
can also specify the directory that contains the tests. As an example (using 
the defaults):

    $ vendor/bin/put --bootstrap vendor/autoload.php --suffix Test.php tests

This uses the default composer `vendor/autoload.php` as the autoloader and 
searches (recursively) in the `tests/` directory for PHP files where their 
name ends in `Test.php`.

### Test Failure

    $ vendor/bin/put
    ...E.
    #Tests      : 5
    #Assertions : 5
    #Errors     : 1
    **** ERROR ****
    assertSame
    #0 /home/fkooman/Projects/put/tests/SimpleTest.php(12): PHPUnit\Framework\TestCase->assertSame('2019-01-02', '2019-01-01')
    #1 /home/fkooman/Projects/put/src/TestCase.php(196): PHPUnit\Framework\SimpleTest->testDate()
    #2 /home/fkooman/Projects/put/bin/put(90): PHPUnit\Framework\TestCase->run()
    #3 {main}
    $ echo $?
    1

From the trace your can determine where it went wrong. The output is not as 
sophisticated as the PHPUnit output, but hey!
