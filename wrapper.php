<?
require_once("merged_vcalendar.class.php");
$feeds = array(
	new ical_feed(array('url' => 'http://www.sslug.dk/adict/ical?organizer=SSLUG'))
);
$merger = new merged_vcalendar($feeds);
$merger->parseFeeds();
$merger->returnCalendar();
?>