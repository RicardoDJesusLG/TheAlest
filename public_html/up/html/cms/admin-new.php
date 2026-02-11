<?php

session_start();
require '../../vendor/autoload.php';
include './inc/config.php';
include './inc/lang.php';
include './inc/functions.php';

validateLogin('login.php');

if ($_SESSION['cms_type'] == 2) {
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

						$idAdmin = $_GET['id'];
					} else {

						$fields['statusAdmin'] = 2;

						$stmt = $conn->prepare("insert into {$config['prefix']}admin (".implode(', ', array_keys($fields)).") values (".prepareFields($fields, 'insert').")");
						$stmt->execute(prepareFieldsArray($fields));

						$idAdmin = $conn->lastInsertId();
					}
				} else {

					$fields['statusAdmin'] = 2;

					$stmt = $conn->prepare("insert into {$config['prefix']}admin (".implode(', ', array_keys($fields)).") values (".prepareFields($fields, 'insert').")");
					$stmt->execute(prepareFieldsArray($fields));

					$idAdmin = $conn->lastInsertId();
				}

				if ($idAdmin && !$errorMsg) {

					$stmt = $conn->prepare("delete from {$config['prefix']}admin_secciones where idAdmin = :idAdmin");
					$stmt->bindParam(':idAdmin', $idAdmin);
					$stmt->execute();

					if (is_array($_POST['seccion'])) {
						foreach ($_POST['seccion'] as $v) {
							unset($fields);
							$fields['idAdmin'] = $idAdmin;
							$fields['idSeccion'] = $v;

							$stmt = $conn->prepare("insert into {$config['prefix']}admin_secciones (".implode(', ', array_keys($fields)).") values (".prepareFields($fields, 'insert').")");
							$stmt->execute(prepareFieldsArray($fields));
						}
					}
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
		$seccion = $_POST['seccion'];

	} elseif ($_GET['id']) {
		$array = xmlFormat(getArray($config, 'idAdmin', "{$config['prefix']}admin", $_GET['id']));

		unset($array['passwordAdmin']);

		unset($seccion);
		$query = $conn->prepare("select * from {$config['prefix']}admin_secciones where idAdmin = :idAdmin");
		$query->execute(array(':idAdmin' => $array['idAdmin']));
		$res = $query->fetchAll();

		if (count($res)) {
		    foreach ($res as $row) {
		    	$row = xmlFormat($row);
				$seccion[$row['idSeccion']] = $row['idSeccion'];
			}
		}
	}

	if (is_array($array)) {
		foreach ($array as $c => $v) {
			if (!is_array($v))
				$render[$c] = $v;
		}
	}

	unset($idSecciones);
	$query = $conn->prepare("select * from {$config['prefix']}secciones order by orderSeccion");
	$query->execute();
	$res = $query->fetchAll();

	if (count($res)) {
	    foreach ($res as $row) {
	    	$row = xmlFormat($row);

			$idSecciones[$row['idSeccion']]['id'] = $row['idSeccion'];
			$idSecciones[$row['idSeccion']]['text'] = $row['nombreSeccion'];

			if (is_array($seccion)) {
				$idSecciones[$row['idSeccion']]['selected'] = $seccion[$row['idSeccion']] ? 'checked' : '';
			}
		}

		$render['idSecciones'] = $idSecciones;
	}

	$render['qs'] = $_SERVER['QUERY_STRING'];

	if ($errorMsg) {
		$render['errorMsg'] = $errorMsg;
	} elseif ($_GET['msgUpdated']) {
		$render['msgUpdated'] = $language['translationMsgUpdated'][$config['lang']];
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
