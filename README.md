# PHP Unit Testing for Minimalists

So, after attending a presentation on PHP and mocking I started evaluating
whether or not I actually need PHPUnit. It seems the only assertion I use all
the time is `assertSame()`. That seems hardly enough justification for having 
PHPUnit as a (development) dependency. Are we simple yet? :-)

That is when I decided to see how difficult it would be to create my own 
PHPUnit compatible unit tester. Turns out, not *that* difficult for the very 
basic functionality.

The goal is obviously *NOT* to have full feature compatibility with PHPUnit. 
Only stuff that is useful, and easy to implement, and that has no other obvious
way to achieve is implemented. 

So, no object mocking, no data providers, no PHPUnit annotations and just a 
bunch of assertions and exception testing. That's all.

After testing some software in the wild, it turns out many projects can be 
tested with put!

## Using

This project requires PHP >= 5.4. It has no other dependencies.

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

Writing tests is exactly the same as for PHPUnit. Put them in the `tests/` 
directory of your project. A simple example:

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

This makes it easy to run both PHPUnit and put to make sure put is not screwing 
up :)

### Assertions

As of now, we have the following assertions implemented:

* `assertSame()`
* `assertNotSame()`
* `assertGreaterThanOrEqual()`
* `assertInternalType()`
* `assertEquals()`
* `assertNotEmpty()`
* `assertTrue()`
* `assertFalse()`
* `assertNull()`
* `assertNotNull()`
* `assertInstanceOf()`
* `fail()`
* `expectException()`

### Exceptions

There are two ways to test exceptions. The first is by using 
`expectException()` at the start of the your test method, e.g.:

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
    [fkooman\Put\Exception\AssertSameException]
    --- EXPECTED ---
    '2019-01-02'
    --- ACTUAL ---
    '2019-01-01'
    --- END ---
    #0 /home/fkooman/Projects/put/tests/SimpleTest.php(16): PHPUnit\Framework\TestCase->assertSame('2019-01-02', '2019-01-01')
    #1 /home/fkooman/Projects/put/src/TestCase.php(274): fkooman\Put\SimpleTest->testDate()
    #2 /home/fkooman/Projects/put/src/Put.php(61): PHPUnit\Framework\TestCase->run()
    #3 /home/fkooman/Projects/put/bin/put(22): fkooman\Put\Put->run(Array)
    #4 {main}

    $ echo $?
    1

From the trace your can determine where it went wrong. The output is not as 
sophisticated as the PHPUnit output, but hey!
