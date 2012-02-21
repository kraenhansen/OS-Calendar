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
		'unique_id' => 'opensource.dk'
	);
	var $feeds = array();
	var $feed_whitelist = null;
	var $feed_blacklist = null;
	var $tag_whitelist = null;
	var $tag_blacklist = null;
	
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
		if(array_key_exists('feed_whitelist', $config) && is_array($config['feed_whitelist'])) {
			$this->feed_whitelist = array();
			foreach($config['feed_whitelist'] as $feed) {
				$this->feed_whitelist[] = strtolower($feed);
			}
		}
		if(array_key_exists('feed_blacklist', $config) && is_array($config['feed_blacklist'])) {
			$this->feed_blacklist = array();
			foreach($config['feed_blacklist'] as $feed) {
				$this->feed_blacklist[] = strtolower($feed);
			}
		}
		if(array_key_exists('tag_whitelist', $config) && is_array($config['tag_whitelist'])) {
			$this->tag_whitelist = array();
			foreach($config['tag_whitelist'] as $tag) {
				$this->tag_whitelist[] = strtolower($tag);
			}
		}
		if(array_key_exists('tag_blacklist', $config) && is_array($config['tag_blacklist'])) {
			$this->tag_blacklist = array();
			foreach($config['tag_blacklist'] as $tag) {
				$this->tag_blacklist[] = strtolower($tag);
			}
		}
		// Check that all elements in $feeds are indeed instances of vcalendar.
		foreach($this->feeds as $feed) {
			if(!$feed instanceof vcalendar) {
				throw new InvalidArgumentException("One (or more) of the elements in the arguments is not a vcalendar.");
			}
		}
	}
	
	function parse() {
		foreach($this->feeds as $feed) {
			$feed->parse();
			// Add all relevant components to our feed.
			if (is_array($this->feed_blacklist) && in_array(strtolower($feed->unique_id), $this->feed_blacklist)) {
				continue;
			} elseif (is_array($this->feed_whitelist)) {
				if(!in_array(strtolower($feed->unique_id), $this->feed_whitelist)) {
					continue;
				}
			}
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
		
		if(is_array($this->tag_blacklist) && count($this->tag_blacklist) > 0) {
			// We are blacklisting
			// Can we find the tag in the blacklist?
			foreach($this->tag_blacklist as $tag) {
				foreach($component->categories as $category) {
					if(strtolower($category) == $tag) {
						return false;
					}
				}
			}
		}
		
		// If no elements in whitelist, we just skip it.
		if(is_array($this->tag_whitelist) && count($this->tag_whitelist) > 0) {
			// We are whitelisting
			// Can we find the tag in the whitelist?
			foreach($this->tag_whitelist as $tag) {
				foreach($component->categories as $category) {
					if(strtolower($category) == $tag) {
						return true;
					}
				}
			}
			return false;
		}
		
		// Not either white nor blacklisting.
		return true;
	}
}
?>