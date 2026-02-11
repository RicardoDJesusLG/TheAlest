<?php

session_start();
require '../../vendor/autoload.php';
include './inc/config.php';
include './inc/lang.php';
include './inc/functions.php';

validateLogin('login.php');

$conn = getDbConnection($config);

if ($_SESSION['cms_type'] == 2) {
	header("location: admin-profile.php");
	exit;
}

// ------------------------------------------------- //
$tableName = "{$config['prefix']}admin";
$tableId = "idAdmin";
$tableOrderBy = "nameAdmin";
// ------------------------------------------------- //

if ($_POST['submitDelete']) {

	if (puedoBorrarAdmin($config, $_POST['idDelete'])) {

		$stmt = $conn->prepare("delete from $tableName where $tableId = :$tableId");
		unset($queryParams);
		$queryParams[$tableId] = $_POST['idDelete'];
		$stmt->execute($queryParams);

		$stmt = $conn->prepare("delete from {$config['prefix']}admin_secciones where $tableId = :$tableId");
		unset($queryParams);
		$queryParams[$tableId] = $_POST['idDelete'];
		$stmt->execute($queryParams);

		header("location: ?msgUpdated=1");
		exit;
	} else {
		header("location: ?");
		exit;
	}
}

$loader = new \Twig\Loader\FilesystemLoader('.');
$twig = new \Twig\Environment($loader);

	$render = array();
	$render['menuAdministradoresActive'] = ' open active';

	include 'inc/inc_container.php';

	unset($items);
	$query = $conn->prepare("select * from $tableName order by $tableOrderBy");
	$query->execute();
	$res = $query->fetchAll();

	if (count($res)) {
	    foreach ($res as $row) {
	    	$row = xmlFormat($row);

			if ($_SESSION['cms_logged'] != $row[$tableId]) {
				if ($_SESSION['cms_logged'] == $config['devAdminID']) {
					$items[$row[$tableId]]['nombre'] = $row['nameAdmin'];
					$items[$row[$tableId]]['email'] = $row['emailAdmin'];
					$items[$row[$tableId]]['id'] = $row[$tableId];
					$items[$row[$tableId]]['type'] = $row['statusAdmin'];
				} elseif ($_SESSION['cms_logged'] == $config['mainAdminID']) {
					if ($config['devAdminID'] != $row[$tableId]) {
						$items[$row[$tableId]]['nombre'] = $row['nameAdmin'];
						$items[$row[$tableId]]['email'] = $row['emailAdmin'];
						$items[$row[$tableId]]['id'] = $row[$tableId];
						$items[$row[$tableId]]['type'] = $row['statusAdmin'];
					}
				} elseif ($_SESSION['cms_type'] == 1) {
					if ($row['statusAdmin'] == 2) {
						$items[$row[$tableId]]['nombre'] = $row['nameAdmin'];
						$items[$row[$tableId]]['email'] = $row['emailAdmin'];
						$items[$row[$tableId]]['id'] = $row[$tableId];
						$items[$row[$tableId]]['type'] = $row['statusAdmin'];
					}
				}
			}
		}

		$render['items'] = $items;
	}

	if ($_SESSION['cms_logged'] == $config['mainAdminID'] || $_SESSION['cms_logged'] == $config['devAdminID']) {
		$render['showAddSectionAdministrator'] = true;
		$render['showAddFullAccessAdministrator'] = true;
	} elseif ($_SESSION['cms_type'] == 1) {
		$render['showAddSectionAdministrator'] = true;
	}

	$render['qs'] = $_SERVER['QUERY_STRING'];

	if ($_GET['msgUpdated']) {
		$render['msgUpdated'] = $language['translationMsgUpdated'][$config['lang']];
	}

	$render['adminsActive'] = 'active';

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>
