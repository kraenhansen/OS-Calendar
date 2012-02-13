<?
require_once("iCalcreator-2.10.23/iCalcreator.class.php");

$GLOBALS['debug'] = true;
// TODO: Remember to change this variable when in production.

/**
 * The main motor of the merger.
 */
class merged_vcalendar extends vcalendar {
	public $feeds = array();
	public $config;
	protected $DEFAULT_CONFIG = array(
		'unique_id' => 'opensource.dk',
		'filename' => 'output.ics'
	);
	/**
	 * @param array $feeds An array of feeds to use, each element of class iCalFeed.
	 */
	function __construct($feeds = array(), $config = null) {
		$this->feeds = $feeds;
		if(is_array($config)) {
			$this->config = $config;
		} else {
			$this->config = $this->DEFAULT_CONFIG;
		}
		parent::__construct($this->config);
		// Check that all elements in $feeds are indeed instances of iCalFeed.
		foreach($this->feeds as $feed) {
			if(!$feed instanceof ical_feed) {
				throw new InvalidArgumentException("One (or more) of the elements in the arguments is not an iCalFeed.");
			}
		}
	}
	
	function parseFeeds() {
		foreach($this->feeds as $feed) {
			$this->setConfig($feed->config);
			$this->parse();
		}
	}
	
	function saveCalendar() {
		$this->setConfig($this->config);
		parent::saveCalendar();
	}
	
	// TODO: Add methods for extracting pr. user configured filtering
	// based on white/black-list of tags.
}

/**
 * The representation of an external iCal feed.
 * TODO: Add the notion of categories
 * 
 * @property $url The external url address from which the feed can be downloaded.
 * @property $tag The default tag to associate with this feed.
 */
class ical_feed {
	public $config;
	function __construct($config) {
		if(!is_array($config)) {
			throw new InvalidArgumentException('Missing array argument $config');
		}
		/* // This check is done in the instantiation of the vcalendar.
		if(!isset($config['url']) && !isset($config['directory'])) {
			throw new InvalidArgumentException("Config has to define either a url for the feed, or a directory.");
		}
		*/
		$this->config = $config;
		
		if($GLOBALS['debug'] === true) {
			printf("Constructed iCalFeed\n");
		}
	}
}
?>