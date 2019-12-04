# PHP Unit Testing For the Insane

So, after attending a presenation on PHP and mocking I started evaluating
whether or not I actually need PHPUnit. It seems the only functions I use are
`assertSame()`, `fail()` and `ok()` for running my tests. That seems hardly 
enough justification for having PHPUnit as a (development) dependency.

That is when I decided to see how difficult it would be to create my own unit 
tester. Turns out, not *that* difficult for basic functionality.

## Writing Tests

In the `tests/` folder of your project you can write your tests. The idea is 
not to use `TestCase` but simply write functions that test your code, for 
example:

	<?php

	function testDate()
	{
		$dateTime = new DateTime('2019-01-01 08:00:00');
		assert_same('2019-01-01', $dateTime->format('Y-m-d'));
	}

Now you can simply run `put` in your project folder which contains the `tests/` 
folder and you are good to go:
	
	$ put
	.

In case your test fails:

	$ put
	"2019-01-02" !== "2019-01-01" (function: testDate)
	
### Comparison

For now only `test_same()` is implemented as we don't need anything else. It 
prints a `.` if the test succeeds or prints an error and terminates the test
runner with an exit code 1 if it doesn't.

### Exceptions

In order to test exceptions, we have the `ok()` and `fail()` functions.

As an example:

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

If you don't care about the exception message you can use `ok()` instead of the
`assert_same()`.