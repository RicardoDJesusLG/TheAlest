<?php

session_start();
require '../../vendor/autoload.php';
include './inc/config.php';
include './inc/lang.php';
include './inc/functions.php';

$conn = getDbConnection($config);

if ($_SESSION['cms_logged']) {
	header("location: ./");
	exit;
}

if ($_GET['code']) {

	$validResetLink = false;

	// admin_password_recovery
	$stmt = $conn->prepare("select * from {$config['prefix']}admin_password_recovery where status = '0' and :now <= DATE_ADD(date, INTERVAL + 1 DAY)");
	$stmt->execute(array('now' => date("Y-m-d H:i:s")));

	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($res)) {
		foreach ($res as $row) {
			$row = xmlFormat($row);

			if ($_GET['code'] == sha1($config['step'].$row['id'])) {
				$validResetLink = true;

				$arrayTmp = xmlFormat(getArray($config, 'idAdmin', "{$config['prefix']}admin", $row['idAdmin']));

				if ($_POST['submitPasswordRecoveryNew'] && $arrayTmp['idAdmin']) {

					unset($validate);
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

		        		if ($_POST['passwordAdmin'] != $_POST['passwordAdmin2']) {
		            		$errorMsg = $language['translationPassDoNotMatch'][$config['lang']];
		            	} else {

							unset($fields);
							$fields['passwordAdmin'] = sha1($config['step'].$_POST['passwordAdmin']);
							$stmt = $conn->prepare("update {$config['prefix']}admin set ".prepareFields($fields, 'update')." where idAdmin = :idAdmin");
							$fields['idAdmin'] = $row['idAdmin'];
							$stmt->execute(prepareFieldsArray($fields));

							unset($fields);
							$fields['status'] = 1;
							$fields['dateModified'] = date("Y-m-d H:i:s");
							$stmt = $conn->prepare("update {$config['prefix']}admin_password_recovery set ".prepareFields($fields, 'update')." where id = :id");
							$fields['id'] = $row['id'];
							$stmt->execute(prepareFieldsArray($fields));

							// mail
							unset($params);
							$params['subject'] = $config['siteName'].' - '.$language['translationRecoverInfoSubject'][$config['lang']];
							$params['fromMail'][$config['email_address']] = $config['siteName'];
							$params['toMail'][$arrayTmp['emailAdmin']] = $arrayTmp['nameAdmin'];
							$params['replyMail'][$config['email_address']] = $config['siteName'];

							if ($config['lang'] == 'en') {
								$params['content'] = "Hello {$arrayTmp['nameAdmin']}, your password has been updated.";
							} else {
								$params['content'] = "Hola {$arrayTmp['nameAdmin']}, su password ha sido actualizada.";
							}

							sendEmail($config, $params, true);

							header("location: login.php?msgPasswordResetUpdated=1");
							exit;
						}
					}
				}

				break;
			}
		}
	}
}

if ($_POST['submitPasswordRecovery']) {

	// admins
	$stmt = $conn->prepare("select * from {$config['prefix']}admin where emailAdmin = :emailAdmin");

	unset($queryParams);
	$queryParams['emailAdmin'] = $_POST['emailAdmin'];

	$stmt->execute($queryParams);

	$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($row)) {

		$row = xmlFormat($row[0]);

		unset($fields);
		$fields['idAdmin'] = $row['idAdmin'];
		$fields['date'] = date("Y-m-d H:i:s");
		$fields['status'] = 0;

		$stmt = $conn->prepare("insert into {$config['prefix']}admin_password_recovery (".implode(', ', array_keys($fields)).") values (".prepareFields($fields, 'insert').")");
		$stmt->execute(prepareFieldsArray($fields));

		$id = $conn->lastInsertId();

		if ($id) {

			unset($params);
			$params['subject'] = $config['siteName'].' - '.$language['translationRecoverInfoSubject'][$config['lang']];
			$params['fromMail'][$config['email_address']] = $config['siteName'];
			$params['toMail'][$row['emailAdmin']] = $row['nameAdmin'];
			$params['replyMail'][$config['email_address']] = $config['siteName'];

			$linkRecover = $config['site_url'].'/cms/recover.php?code='.sha1($config['step'].$id);

			if ($config['lang'] == 'en') {
				$params['content'] = "Hello {$row['nameAdmin']}, please <a href='$linkRecover'>click here</a> in order to change your password.";
			} else {
				$params['content'] = "Hola {$row['nameAdmin']}, haga <a href='$linkRecover'>click aqui</a> para modificar su password.";
			}

			if (sendEmail($config, $params, true)) {
				header("location: ?msgRecover=1");
				exit;
			} else {
				$errorMsg = $language['translationErrorSendingEmail'][$config['lang']];
			}
		}

	} else {
	    $errorMsg = $language['translationErrorRecover'][$config['lang']];
	}
}

$loader = new \Twig\Loader\FilesystemLoader('.');
$twig = new \Twig\Environment($loader);

	$render = array();

	include 'inc/inc_container.php';

	if ($errorMsg) {
		$render['errorMsg'] = $errorMsg;
	} elseif ($_GET['msgRecover']) {
		$render['msgRecover'] = true;
	}

	if (count($_POST)) {
		foreach ($_POST as $c => $v) {
			$render[$c] = $v;
		}
	}


	$render['siteName'] = $config['siteName'];

	if ($_GET['code']) {
		if ($validResetLink) {
			$render['passwordResetForm'] = true;
		} else {
			$render['notValidResetLink'] = true;
		}
	}

	$render['qs'] = $_SERVER['QUERY_STRING'];

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>
