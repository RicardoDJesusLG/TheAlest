<?php

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED ^ E_USER_DEPRECATED);

unset($config);

// timezone
$config['timezone'] = "America/Argentina/Buenos_Aires";
date_default_timezone_set($config['timezone']);

// label site
$config['siteName'] = 'The Alest';

// lang site
$config['lang'] = 'en';

// db login
$config['db_hostname'] = "localhost";
$config['db_database'] = "web8755";
$config['db_username'] = "root";
$config['db_password'] = "woofandbarf";

// emails
$config['email_address'] = "hello@thealest.com"; // info@thealest.com
$config['email_server'] = "localhost";

$https = '';
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
	$https = 's';
}

$config['site_root'] = '';
$config['site_url'] = 'http'.$https.'://'.$_SERVER['HTTP_HOST'].$config['site_root'];
$config['site_url_cms'] = $config['site_url'].'/cms';

$config['ckeditor_uploadURL'] = $config['site_url'].'/files/uploads';
$config['ckeditor_uploadDir'] = '../../../../files/uploads';

// db prefix tables
$config['prefix'] = 'alest_';

// step
$config['step'] = 'b072654cf0d342d7696ea088dcda7321a091161b';

// admins
$config['mainAdminID'] = 1;
$config['devAdminID'] = 2;

// uris
	$config['uris']['en']['link_root'] = '';
	$config['uris']['en']['link_news'] = 'news';
	$config['uris']['en']['link_rooms'] = 'stay';
	$config['uris']['en']['link_specials'] = 'specials';
	$config['uris']['en']['link_specials'] = 'specials';
	$config['uris']['en']['link_aabb'] = 'eat-and-drink';
	$config['uris']['en']['link_events'] = 'events';
	$config['uris']['en']['link_gallery'] = 'gallery';
	$config['uris']['en']['link_location'] = 'location';
	$config['uris']['en']['link_hotel'] = 'the-hotel';
	$config['uris']['en']['link_contact'] = 'contact';
	$config['uris']['en']['link_contact_thanks'] = 'contact/thanks';
	$config['uris']['en']['link_safety'] = 'safety-protocols';
	$config['uris']['en']['link_privacy'] = 'privacy-policy';
	$config['uris']['en']['link_legal'] = 'legal-notice';

	$config['uris']['es']['link_root'] = '';
	$config['uris']['es']['link_news'] = 'news';
	$config['uris']['es']['link_rooms'] = 'stay';
	$config['uris']['es']['link_specials'] = 'specials';
	$config['uris']['es']['link_specials'] = 'specials';
	$config['uris']['es']['link_aabb'] = 'eat-and-drink';
	$config['uris']['es']['link_events'] = 'events';
	$config['uris']['es']['link_gallery'] = 'gallery';
	$config['uris']['es']['link_location'] = 'location';
	$config['uris']['es']['link_hotel'] = 'the-hotel';
	$config['uris']['es']['link_contact'] = 'contact';
	$config['uris']['es']['link_contact_thanks'] = 'contact/thanks';
	$config['uris']['es']['link_safety'] = 'safety-protocols';
	$config['uris']['es']['link_privacy'] = 'privacy-policy';
	$config['uris']['es']['link_legal'] = 'legal-notice';
	
// recaptcha
$config['recaptchaPublic'] = '6Lead0IcAAAAAKCfGk5HgF4JLw61k13fIEdigGcT';
$config['recaptchaSecret'] = '6Lead0IcAAAAAEA7s4q1n2aQ0Jo1zhjrxhSPB9nu';

// google maps api
$config['googleMapsApiKey'] = '';

// langs
$config['siteLangs'] = array('Es', 'En');
$config['siteLangsStr'] = array('Spanish', 'English');

$config['langs'] = array('En', 'Es');

$config['uri_en'] = '';
$config['uri_es'] = 'es/';

function getDbConnection($config) {
	try {
		$conn = new PDO('mysql:host='.$config['db_hostname'].';dbname='.$config['db_database'], $config['db_username'], $config['db_password']);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		//$conn->exec("SET time_zone = '{$config['timezone']}'");

	    return $conn;

	} catch(PDOException $e) {
	    echo 'ERROR: ' . $e->getMessage();
	    exit;
	}
}

if ($_GET['logout']) {
	foreach ($_SESSION as $c => $v) {
		if (substr($c, 0, strlen('cms_')) == 'cms_') {
			unset($_SESSION[$c]);
		}
	}

	header("location: ".$config['site_url'].'/cms');
	exit;
}

?>