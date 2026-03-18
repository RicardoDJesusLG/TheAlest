<?php

session_start();
require '../../vendor/autoload.php';
include './inc/config.php';
include './inc/lang.php';
include './inc/functions.php';

validateLogin('login.php');
validateSeccion($config, $language);

$conn = getDbConnection($config);

// ------------------------------------------------- //
$sectionTitle = "Contact";
$sectionMenu = "contacto";
$tableName = "{$config['prefix']}contacto";
$tableId = "id";
$orderBy = "fecha desc";
// ------------------------------------------------- //

if ($_POST['submitDelete']) {

	$stmt = $conn->prepare("delete from $tableName where $tableId = :$tableId");
	unset($queryParams);
	$queryParams[$tableId] = $_POST['idDelete'];
	$stmt->execute($queryParams);

	header("location: ?msgUpdated=1");
	exit;
}

$loader = new \Twig\Loader\FilesystemLoader('.');
$twig = new \Twig\Environment($loader);

	$render = array();
	$render[$sectionMenu.'Active'] = 'active';
	$render['sectionTitle'] = $sectionTitle;

	$render['mode'] = $_GET['mode'] == 'edit' ? 'edit' : 'list';

	include 'inc/inc_container.php';

	$render['currentPage'] = getCurrentPage().'.php';

	if ($render['mode'] == 'list' || $_GET['export']) {

		if ($_GET['export'] || $_GET['submitExportRange']) {

			$sql = $filenameDate = '';
			if ($_GET['start'] && $_GET['end']) {

				$startOk = $endOk = false;

				$dateExploded = explode("/", $_GET['start']);
				if (count($dateExploded) == 3){
					$day = $dateExploded[0];
					$month = $dateExploded[1];
					$year = $dateExploded[2];
					 
					if (checkdate($month, $day, $year)){
						$startOk = $year.'-'.$month.'-'.$day.' 00:00:00';
						$startOkF = $year.'-'.$month.'-'.$day;
					}
				}

				$dateExploded = explode("/", $_GET['end']);
				if (count($dateExploded) == 3){
					$day = $dateExploded[0];
					$month = $dateExploded[1];
					$year = $dateExploded[2];
					 
					if (checkdate($month, $day, $year)){
						$endOk = $year.'-'.$month.'-'.$day.' 23:59:59';
						$endOkF = $year.'-'.$month.'-'.$day;
					}
				}

				if ($startOk && $endOk) {
					$render['start'] = $_GET['start'];
					$render['end'] = $_GET['end'];
					$sql = " where fecha between '$startOk' and '$endOk'";

					$filenameDate = $startOkF.'_'.$endOkF.'-';
				}
			}

			$filename = "axenya-contact-" . $filenameDate . date('Ymd-His') . ".csv";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: text/csv");
			$out = fopen("php://output", 'w');

			unset($items);
			$query = $conn->prepare("select * from $tableName $sql order by $orderBy");
			$query->execute();
			$res = $query->fetchAll();

			if (count($res)) {

				foreach ($res as $row) {
					$row = xmlFormat($row);

					$items['Date'] = date("m/d/Y g:iA", strtotime($row['fecha']));
					$items['Name'] = $row['frmNombre'];
					$items['Last Name'] = $row['frmApellido'];
					$items['Phone'] = $row['frmTelefono'];
					$items['Country'] = $row['frmCountry'];
					$items['City'] = $row['frmCity'];
					$items['Email'] = $row['frmEmail'];
					$items['Subject'] = $row['frmSubject'];
					$items['Message'] = $row['frmComentarios'];
					$items['IP'] = $row['ip'];

					if(!$flag) {
						// display field/column names as first row
						fputcsv($out, array_keys($items), ',', '"');
						$flag = true;
					}

					array_walk($items, __NAMESPACE__ . '\cleanData');
					fputcsv($out, array_values($items), ',', '"');
				}
			}

			fclose($out);
			exit;

		} else {

			unset($items);
			$query = $conn->prepare("select * from $tableName order by $orderBy");
			$query->execute();
			$res = $query->fetchAll();

			if (count($res)) {

				$o = 0;
				foreach ($res as $row) {
					$row = xmlFormat($row);

					$items[$row[$tableId]]['position'] = str_pad($o++, 7, '0', STR_PAD_LEFT);
					$items[$row[$tableId]]['fromName'] = $row['frmNombre'].' '.$row['frmApellido'];
					$items[$row[$tableId]]['fromEmail'] = $row['frmEmail'];
					$items[$row[$tableId]]['status'] = $row['status'];
					$items[$row[$tableId]]['date'] = date("d/m/Y g:iA", strtotime($row['fecha']));
					$items[$row[$tableId]]['id'] = $row[$tableId];
				}

				$render['items'] = $items;
			}
		}

		if ($_GET['msgUpdated']) {
			$render['msgUpdated'] = $language['translationMsgUpdated'][$config['lang']];
		}

		// $render['linkAdd'] = $render['currentPage'].'?mode=edit';

	} elseif ($render['mode'] == 'edit') {

		if ($_POST['submitUpdate']) {

			$_POST = aTrim($_POST);

			unset($validate);
			$validate[] = 'status';

			unset($fields);
			foreach ($validate as $c => $v)
				$fields[$v] = $_POST[$v];

			$fields['status'] = abs($fields['status']);

			if ($_GET['id']) {

				$array = getArray($config, $tableId, $tableName, $_GET['id']);
				if ($array[$tableId]) {

					$stmt = $conn->prepare("update $tableName set ".prepareFields($fields, 'update')." where $tableId = :$tableId");
					$fields[$tableId] = $_GET['id'];
					$stmt->execute(prepareFieldsArray($fields));
				}
			}

			header("location: ?msgUpdated=1");
			exit;
		}

		if ($_GET['id']) {
			$array = xmlFormat(getArray($config, $tableId, $tableName, $_GET['id']));

			if (!$array[$tableId]) {
				header("location: {$config['site_url_cms']}");
				exit;
			}
		}

		if (is_array($array)) {
			foreach ($array as $c => $v) {
				$render[$c] = $v;
			}
		}

		$render['fecha'] = date("m/d/Y g:iA", strtotime($render['fecha']));

		if ($errorMsg) {
			$render['errorMsg'] = $errorMsg;
		}

		$render['linkCancel'] = $render['currentPage'].'?';
	}

	$render['qs'] = $_SERVER['QUERY_STRING'];

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>
