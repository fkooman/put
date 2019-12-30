# PHP Unit Testing for Minimalists

A simple PHPUnit replacement with support for PHP >= 5.4. It implements the
most common PHPUnit assertions. In addition, it supports PHP Code Coverage
using [pcov](https://github.com/krakjoe/pcov) on PHP >= 7.

We support a few common assertions, and the recommended way to test exceptions, 
nothing more. No mocks, no data providers and no PHPUnit annotations. Those 
should not be used anyway as that reduces the effectiveness of static code 
analysis.

## Assertions

The following assertions are implemented:

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

## Exceptions

For testing exceptions we implemented:

* `expectException()`
* `expectExceptionMessage()`

For example:

    $this->expectException('RangeException');

Or when using PHP >= 5.5:

    $this->expectException(RangeException::class);

## Using

This project requires PHP >= 5.4. It has no other dependencies, but optionally
`ext-pcov` for code coverage reporting.

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

This makes it easy to support both `phpunit` and `put` with the same test 
suite.

## Running Tests

Assuming you added `put` to your `composer.json`, you can simply run it:

    $ vendor/bin/put
    .............
    #Tests      : 13
    #Assertions : 14
    $ echo $?
    0

See `vendor/bin/put --help` for a description of the configuration options. 

As an example, with the defaults:

    $ vendor/bin/put --bootstrap vendor/autoload.php --suffix Test.php tests

To run code coverage reporting, using the defaults:

    $ vendor/bin/put --coverage report.html

You can view the `report.html` in your web browser.

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
