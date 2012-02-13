<?
require_once("merged_vcalendar.class.php");

/**
 * This class is used for automated tests.
 * @property merger merged_vcalendar
 */
class iCalMergerTester extends Tester {
	
	public $merger;
	
	function setup() {
		//$this->merger = new iCalMerger();
	}
	
	function teardown() {
		// Freeing memory ...
	}
	
	function trivial_test() {
		//assert('$this->false_method()');
		//assert('$this->error_method()');
	}
	
	function false_method() {
		return false;
	}
	
	function error_method() {
		$i = 1/0; // Division by zero!
		return true;
	}
	
	function simple_test() {
		// Instantiate an array of iCal feeds.
		$feeds = array(
			new ical_feed(array('directory'=>'testfiles', 'filename'=>'feed-a.ics')),
			new ical_feed(array('directory'=>'testfiles', 'filename'=>'feed-b.ics'))
		);
		$this->merger = new merged_vcalendar($feeds);
		// Check that the merger is instantiated and has the right type.
		assert('$this->merger instanceof vcalendar');
		assert('$this->merger instanceof merged_vcalendar');
		
		$this->merger->parseFeeds();
		$this->merger->saveCalendar();
	}
}

$GLOBALS['tester'] = new iCalMergerTester();

class Tester {
	public $passed = true;
	
	function __construct() {
		assert_options(ASSERT_ACTIVE,   true);
		assert_options(ASSERT_BAIL,     false);
		assert_options(ASSERT_WARNING,  false);
	}
	
	function run() {
		$test_methods = array();
		foreach(get_class_methods($this) as $method) {
			if(str_ends_with(strtolower($method), 'test')) {
				$test_methods[] = $method;
			}
		}
		$passed_tests = array();
		$failed_tests = array();
		foreach ($test_methods as $test_number => $method) {
			$this->passed = true;
			try {
				printf("Test %d of %d: '%s'\n", $test_number+1, count($test_methods), $method);
				if(method_exists($this, "setup")) {
					$this->setup();
				}
				call_user_func(array($this, $method), null);
				if(method_exists($this, "teardown")) {
					$this->teardown();
				}
				if($this->passed === true) {
					$passed_tests[] = $method;
				} elseif($this->passed === false) {
					print("Test failed!\n");
					$failed_tests[] = $method;
				}
			} catch (Exception $e) {
				printf("[!] Exception thrown: %s\n", $e);
				$this->passed = false;
			}
		}
		printf("----------\n%d/%d test cases complete. %d passes and %d fails.\n", count($passed_tests)+count($failed_tests), count($test_methods), count($passed_tests), count($failed_tests));
	}
	
	function assert_handler($file, $line, $code) {
		if(strlen($code) > 0) {
			printf("[!] Assertion failed: '%s'\n", $code);
		} else {
			printf("[!] Assertion failed!\n");
		}
		$this->passed = false;
	}
	
	function error_handler($errno, $errstr) {
		if(strlen($errstr) > 0) {
			printf("[!] Error: '%s'\n", $errstr);
		} else {
			printf("[!] Error!\n");
		}
		$this->passed = null;
	}
}

function str_ends_with($haystack, $needle) {
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

// This has to be here ...
function error_handler($errno, $errstr) {
	$GLOBALS['tester']->error_handler($errno, $errstr);
}
set_error_handler('error_handler');
function assert_handler($file, $line, $code) {
	$GLOBALS['tester']->assert_handler($file, $line, $code);
}
assert_options(ASSERT_CALLBACK, 'assert_handler');

// Run all testcases ..
$GLOBALS['tester']->run();
?>
