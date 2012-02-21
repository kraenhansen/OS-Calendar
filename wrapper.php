<?
require_once("merged_vcalendar.class.php");
$feeds = array(
	new vcalendar(array('unique_id' => 'SSLUG', 'url' => 'http://www.sslug.dk/adict/ical?organizer=SSLUG')),
	new vcalendar(array('unique_id' => 'feed-a', 'directory' => './testfiles', 'filename' => 'feed-a.ics')),
	new vcalendar(array('unique_id' => 'feed-b', 'directory' => './testfiles', 'filename' => 'feed-b.ics'))
);

$config = array();

// Get the tag whitelist from the users input
if(isset($_GET['feed_whitelist'])) {
	$config['feed_whitelist'] = explode(',', $_GET['feed_whitelist']);
}

// Get the tag blacklist from the users input
if(isset($_GET['feed_blacklist'])) {
	$config['feed_blacklist'] = explode(',', $_GET['feed_blacklist']);
}

// Get the tag whitelist from the users input
if(isset($_GET['tag_whitelist'])) {
	$config['tag_whitelist'] = explode(',', $_GET['tag_whitelist']);
}

// Get the tag blacklist from the users input
if(isset($_GET['tag_blacklist'])) {
	$config['tag_blacklist'] = explode(',', $_GET['tag_blacklist']);
}

header("Cache-Control: no-cache, must-revalidate");
$merger = new merged_vcalendar($feeds, $config);
$merger->parse();
//$merger->returnCalendar();
echo $merger->createCalendar();
?>