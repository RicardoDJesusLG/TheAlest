<?php

session_start();
require '../../vendor/autoload.php';
include './inc/config.php';
include './inc/lang.php';
include './inc/functions.php';

validateLogin('login.php');

if ($_SESSION['cms_logged'] != $config['mainAdminID'] && $_SESSION['cms_logged'] != $config['devAdminID']) {
	header("location: admin.php");
	exit;
}

$conn = getDbConnection($config);

if ($_POST['submitUpdate']) {

	unset($validate);
	$validate[] = 'nameAdmin';
	$validate[] = 'emailAdmin';

	$validatePasswords = false;
	if ($_GET['id']) {

		if ($_POST['passwordAdmin'] != '' || $_POST['passwordAdmin2'] != '') {
			$validatePasswords = true;
		}

	} else {
		$validatePasswords = true;
	}

	if ($validatePasswords) {
		$validate[] = 'passwordAdmin';
		$validate[] = 'passwordAdmin2';
	}

	$errorMsg = '';
	foreach ($validate as $c => $v) {
		if (trim($_POST[$v]) == '') {
			$errorMsg = $language['translationCompleteFields'][$config['lang']];
			break;
		}
	}

	$validate[] = 'passwordAdmin';
	$validate[] = 'passwordAdmin2';

	if (!$errorMsg) {

		if (!checkMail($_POST['emailAdmin'])) {
			$errorMsg = $language['translationCompleteEmail'][$config['lang']];
		} elseif (usernameAdminExists($config, 'admin', $_POST['emailAdmin'], $_GET['id'])) {
			$errorMsg = $language['translationCompleteAnotherEmail'][$config['lang']];
		} else {

        	if ($validatePasswords) {
        		if ($_POST['passwordAdmin'] != $_POST['passwordAdmin2']) {
            		$errorMsg = $language['translationPassDoNotMatch'][$config['lang']];
            	}
        	}

        	if (!$errorMsg) {

				unset($fields);
				foreach ($validate as $c => $v) {
					$fields[$v] = $_POST[$v];
				}

				unset($fields['passwordAdmin']);
				unset($fields['passwordAdmin2']);

				if ($validatePasswords) {
					$fields['passwordAdmin'] = sha1($config['step'].$_POST['passwordAdmin']);
				}

				if ($_GET['id']) {
					$array = xmlFormat(getArray($config, 'idAdmin', "{$config['prefix']}admin", $_GET['id']));
					if ($array['idAdmin']) {

						$stmt = $conn->prepare("update {$config['prefix']}admin set ".prepareFields($fields, 'update')." where idAdmin = :idAdmin");
						$fields['idAdmin'] = $_GET['id'];
						$stmt->execute(prepareFieldsArray($fields));

					} else {

						$fields['statusAdmin'] = 1;

						$stmt = $conn->prepare("insert into {$config['prefix']}admin (".implode(', ', array_keys($fields)).") values (".prepareFields($fields, 'insert').")");
						$stmt->execute(prepareFieldsArray($fields));
					}
				} else {

					$fields['statusAdmin'] = 1;

					$stmt = $conn->prepare("insert into {$config['prefix']}admin (".implode(', ', array_keys($fields)).") values (".prepareFields($fields, 'insert').")");
					$stmt->execute(prepareFieldsArray($fields));
				}

				header("location: admin.php?msgUpdated=1");
				exit;
			}
		}
	}
}

$loader = new \Twig\Loader\FilesystemLoader('.');
$twig = new \Twig\Environment($loader);

	$render = array();
	include 'inc/inc_container.php';

	if (count($_POST)) {
		$array = xmlFormat($_POST);

	} elseif ($_GET['id']) {
		$array = xmlFormat(getArray($config, 'idAdmin', "{$config['prefix']}admin", $_GET['id']));
		unset($array['passwordAdmin']);
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
	}

	$completePasswords = false;
	if ($_GET['id']) {

		if ($array['passwordAdmin'] != '' || $array['passwordAdmin2'] != '') {
			$completePasswords = true;
		}

	} else {
		$completePasswords = true;
	}

	$render['completePasswords'] = $completePasswords;

	$render['adminsActive'] = 'active';

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>
