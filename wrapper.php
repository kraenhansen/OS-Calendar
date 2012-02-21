<?
require_once("merged_vcalendar.class.php");
$feeds = array(
	new vcalendar(array('url' => 'http://www.sslug.dk/adict/ical?organizer=SSLUG')),
	new vcalendar(array('unique_id' => 'feed-a', 'directory' => 'testfiles', 'filename' => 'feed-a.ics')),
	new vcalendar(array('unique_id' => 'feed-b', 'directory' => 'testfiles', 'filename' => 'feed-b.ics'))
);

$config = array();

// Get the tag whitelist from the users input
if(isset($_GET['whitelist'])) {
	$config['whitelist'] = explode(',', $_GET['whitelist']);
}

// Get the tag blacklist from the users input
if(isset($_GET['blacklist'])) {
	$config['blacklist'] = explode(',', $_GET['blacklist']);
}

$merger = new merged_vcalendar($feeds, $config);
$merger->parse_feeds();
$merger->returnCalendar();
?>