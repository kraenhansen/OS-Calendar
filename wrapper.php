<?
require_once("merged_vcalendar.class.php");
$feeds = array(
	new vcalendar(array('url' => 'http://www.sslug.dk/adict/ical?organizer=SSLUG')),
	new vcalendar(array('unique_id' => 'feed-a', 'directory' => 'testfiles', 'filename' => 'feed-a.ics')),
	new vcalendar(array('unique_id' => 'feed-b', 'directory' => 'testfiles', 'filename' => 'feed-b.ics'))
);

// Get the tag whitelist from the users input
$whitelist = null;
if(isset($_GET['whitelist'])) {
	$whitelist = explode(',', $_GET['whitelist']);
}

// Get the tag blacklist from the users input
$blacklist = null;
if(isset($_GET['blacklist'])) {
	$blacklist = explode(',', $_GET['blacklist']);
}

$config = array('whitelist' => $whitelist, 'blacklist' => $blacklist);

$merger = new merged_vcalendar($feeds, $config);
$merger->parse_feeds();
$merger->returnCalendar();
?>