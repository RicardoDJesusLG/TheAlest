<?php

session_start();
require '../../vendor/autoload.php';
include './inc/config.php';
include './inc/lang.php';
include './inc/functions.php';

// echo generatePassword($config, 'admin'); exit;

$conn = getDbConnection($config);

if ($_SESSION['cms_logged']) {
	header("location: ./");
	exit;
}

if ($_POST['submitLogin']) {

	// admins
	$stmt = $conn->prepare("select * from {$config['prefix']}admin where emailAdmin = :emailAdmin and passwordAdmin = :passwordAdmin");

	unset($queryParams);
	$queryParams['emailAdmin'] = $_POST['emailAdmin'];
	$queryParams['passwordAdmin'] = sha1($config['step'].$_POST['passwordAdmin']);

	$stmt->execute($queryParams);

	$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($row)) {

		$row = xmlFormat($row[0]);

		// printArray($row); exit;

		$_SESSION['cms_name'] = $row['nameAdmin'];
		$_SESSION['cms_logged'] = $row['idAdmin'];
		$_SESSION['cms_type'] = $row['statusAdmin'];

		$_SESSION['cms_ckeditor_uploadURL'] = $config['ckeditor_uploadURL'];
		$_SESSION['cms_ckeditor_uploadDir'] = $config['ckeditor_uploadDir'];

		header("location: ./");
		exit;

	} else {
	    $errorMsg = $language['translationErrorLogin'][$config['lang']];
	}
}

$loader = new \Twig\Loader\FilesystemLoader('.');
$twig = new \Twig\Environment($loader);

	$render = array();

	include 'inc/inc_container.php';

	if ($errorMsg) {
		$render['errorMsg'] = $errorMsg;
	} elseif ($_GET['msgPasswordResetUpdated']) {
		$render['msgPasswordResetUpdated'] = true;
	}

	if (count($_POST)) {
		foreach ($_POST as $c => $v) {
			$render[$c] = $v;
		}
	}

	$render['siteName'] = $config['siteName'];

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>
