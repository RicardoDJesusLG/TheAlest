<?php

session_start();
require '../../vendor/autoload.php';
include './inc/config.php';
include './inc/lang.php';
include './inc/functions.php';

validateAdmin($config, $_SESSION['cms_logged']);
validateLogin('login.php');

$conn = getDbConnection($config);

if ($_POST['submitUpdate']) {

	unset($validate);
	$validate[] = 'nameAdmin';
	$validate[] = 'emailAdmin';
	$validate[] = 'passwordAdmin';
	$validate[] = 'passwordAdmin2';

	$errorMsg = '';
	foreach ($validate as $c => $v) {
		if (trim($_POST[$v]) == '') {
			$errorMsg = $language['translationCompleteFields'][$config['lang']];
			break;
		}
	}

	if (!$errorMsg) {

		if (!checkMail($_POST['emailAdmin'])) {
			$errorMsg = $language['translationCompleteEmail'][$config['lang']];
		} elseif (usernameAdminExists($config, 'admin', $_POST['emailAdmin'], $_SESSION['cms_logged'])) {
			$errorMsg = $language['translationCompleteAnotherEmail'][$config['lang']];
		} elseif ($_POST['passwordAdmin'] != $_POST['passwordAdmin2']) {
			$errorMsg = $language['translationPassDoNotMatch'][$config['lang']];
		} else {
			unset($fields);
			foreach ($validate as $c => $v) {
				$fields[$v] = $_POST[$v];
			}

			unset($fields['passwordAdmin2']);
			$fields['passwordAdmin'] = sha1($config['step'].$_POST['passwordAdmin']);

			$array = xmlFormat(getArray($config, 'idAdmin', "{$config['prefix']}admin", $_SESSION['cms_logged']));
			if ($array['idAdmin']) {

				$stmt = $conn->prepare("update {$config['prefix']}admin set ".prepareFields($fields, 'update')." where idAdmin = :idAdmin");
				$fields['idAdmin'] = $_SESSION['cms_logged'];
				$stmt->execute(prepareFieldsArray($fields));

				$_SESSION['cms_name'] = xmlFormat($fields['nameAdmin']);
			}

			header("location: ?msgUpdated=1");
			exit;
		}
	}
}

$loader = new \Twig\Loader\FilesystemLoader('.');
$twig = new \Twig\Environment($loader);

	$render = array();
	include 'inc/inc_container.php';

	if (count($_POST)) {
		$array = xmlFormat($_POST);

	} else {
		$array = xmlFormat(getArray($config, 'idAdmin', "{$config['prefix']}admin", $_SESSION['cms_logged']));
		$array['passwordAdmin'] = '';
	}

	if (is_array($array)) {
		foreach ($array as $c => $v) {
			if (!is_array($v))
				$render[$c] = $v;
		}
	}

	$render['qs'] = $_SERVER['QUERY_STRING'];

	if ($errorMsg) {
		$render['errorMsg'] = $errorMsg;
	} elseif ($_GET['msgUpdated']) {
		$render['msgUpdated'] = $language['translationMsgUpdated'][$config['lang']];
	}

	$render['adminProfileActive'] = 'active';

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>
