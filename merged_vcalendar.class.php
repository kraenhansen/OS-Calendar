<?
require_once("iCalcreator-2.10.23/iCalcreator.class.php");

/**
 * The main motor of the merger.
 * @param array $DEFAULT_CONFIG The default configuration.
 * @param vcalendar[] $feeds An array of vcalenders to merge.
 * @param string[] $whitelist A list of tags of which at least one has to be as category or unique id for any event in the resulting output
 * @param string[] $blacklist A list of tags that cannot to be present as categories or unique id for any event in the resulting output
 */
class merged_vcalendar extends vcalendar {
	var $DEFAULT_CONFIG = array(
		'unique_id' => 'opensource.dk',
		'filename' => 'output.ics'
	);
	var $feeds = array();
	var $whitelist = NULL;
	var $blacklist = null;
	
	function __construct($feeds = array(), $config = null) {
		$this->feeds = $feeds;
		if(is_array($config)) {
			// Insert default values on keys that exists in default array
			// but is not present in the array given as argument.
			foreach($this->DEFAULT_CONFIG as $k => $v) {
				if(!array_key_exists($k, $config)) {
					$config[$k] = $v;
				}
			}
		} else {
			$config = $this->DEFAULT_CONFIG;
		}
		parent::__construct($config);
		if(array_key_exists('whitelist', $config) && is_array($config['whitelist'])) {
			$this->whitelist = $config['whitelist'];
		}
		if(array_key_exists('blacklist', $config) && is_array($config['blacklist'])) {
			$this->blacklist = $config['blacklist'];
		}
		// Check that all elements in $feeds are indeed instances of vcalendar.
		foreach($this->feeds as $feed) {
			if(!$feed instanceof vcalendar) {
				throw new InvalidArgumentException("One (or more) of the elements in the arguments is not a vcalendar.");
			}
		}
	}
	
	function parse_feeds() {
		foreach($this->feeds as $feed) {
			$feed->parse();
			// Add all relevant components to our feed.
			foreach($feed->components as &$component) {
				if($this->is_component_included($feed, $component)) {
					$this->components[] = $component;
				}
			}
		}
		$this->sort();
	}
	
	function is_component_included(&$feed, &$component) {
		// We only consider events for the moment.
		if(!$component instanceof vevent) {
			return false;
		}
		
		// If no elements in whitelist, we just skip it.
		if(is_array($this->whitelist) && count($this->whitelist) > 0) {
			$satisfied = false;
			// We are whitelisting
			foreach($this->whitelist as $tag) {
				if($tag === $feed->unique_id) {
					$satisfied = true;
					break;
				}
				// Can we find the tag in the whitelist?
				if(in_array($tag, $component->categories) !== false) {
					$satisfied = true;
					break;
				}
			}
			if(!$satisfied) {
				// No tag from whitelist was found as category or unique_id on the event.
				return false;
			}
		}
		
		if(is_array($this->blacklist) && count($this->blacklist) > 0) {
			// We are blacklisting
			foreach($this->blacklist as $tag) {
				if($tag === $feed->unique_id) {
					return false;
				}
				// Can we find the tag in the blacklist?
				if(in_array($tag, $component->categories) !== false) {
					return false;
				}
			}
		}
		
		// Not either white nor blacklisting.
		return true;
	}
}
?>