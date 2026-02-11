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
$sectionTitle = "Events";
$sectionMenu = "events";
$tableName = "{$config['prefix']}events";
$tableId = "id";
$orderBy = "position";
// ------------------------------------------------- //

if ($_POST['submitDelete']) {

	$stmt = $conn->prepare("delete from $tableName where $tableId = :$tableId");
	unset($queryParams);
	$queryParams[$tableId] = $_POST['idDelete'];
	$stmt->execute($queryParams);

	header("location: ?msgUpdated=1");
	exit;
}

if ($_POST['submitUpdateOrder']) {

	if ($_POST['orderHidden'] != '') {
		$tmp = json_decode($_POST['orderHidden'], true);
		if (is_array($tmp[0])) {
			if (count($tmp[0])) {

				# Prepare the query ONCE
				$stmt = $conn->prepare("update $tableName set $orderBy = :orderByValue where $tableId = :tableIdValue");
				$stmt->bindParam(':orderByValue', $orderByValue);
				$stmt->bindParam(':tableIdValue', $tableIdValue);

				foreach ($tmp[0] as $key => $value) {
					$orderByValue = $key +1;
					$tableIdValue = $value['id'];
					$stmt->execute();
				}
			}
		}
	}

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

	if ($render['mode'] == 'list') {

		unset($items);

		$query = $conn->prepare("select * from $tableName order by $orderBy");
		$query->execute();
		$res = $query->fetchAll();
		if (count($res)) {

			$o = 0;
			foreach ($res as $row) {
				$row = xmlFormat($row);

				$items[$row[$tableId]]['position'] = str_pad($o++, 7, '0', STR_PAD_LEFT);
				$items[$row[$tableId]]['title'] = $row['title'];
				$items[$row[$tableId]]['id'] = $row[$tableId];
			}

			$render['items'] = $items;
		}

		if ($_GET['msgUpdated']) {
			$render['msgUpdated'] = $language['translationMsgUpdated'][$config['lang']];
		}

		$render['linkAdd'] = $render['currentPage'].'?mode=edit';

	} elseif ($render['mode'] == 'edit') {

		$images = array('image' => array('validate' => true, 'uploadDir' => '../files/aabb'), 'imageHero' => array('validate' => true, 'uploadDir' => '../files/aabb'));
		if ($_POST['submitUpdate']) {

			// printArray($_POST); exit;

			$_POST = aTrim($_POST);

			unset($validate);

			foreach ($config['siteLangs'] as $langSuffix) {
				$validate[] = 'pretitle'.$langSuffix;
				$validate[] = 'intro'.$langSuffix;
				$validate[] = 'text'.$langSuffix;
				$validate[] = 'caption'.$langSuffix;
			}

			if (is_array($images)) {
				foreach ($images as $fileInput => $value) {

					if (is_uploaded_file($_FILES[$fileInput]['tmp_name']))	{

						$storage = new \Upload\Storage\FileSystem($value['uploadDir']);
						$file = new \Upload\File($fileInput, $storage);

						$file->setName(generateName($value['uploadDir'], getPathInfo($file->getNameWithExtension())));

						try {
						    $file->upload();

							if (is_array($value['thumbs'])) {
								foreach ($value['thumbs'] as $options) {
									$thumb = new PHPThumb\GD($value['uploadDir'].'/'.$file->getNameWithExtension());
									$thumb->adaptiveResize($options['width'], $options['height'])->save($options['uploadDir'].'/'.$file->getNameWithExtension());
								}
							}

						    $_POST[$fileInput] = $file->getNameWithExtension();

						} catch (\Exception $e) {
						    $errorMsg = $language['invalidFile'][$config['lang']]; // implode(' / ', $file->getErrors());
						}
					}

					if ($value['validate'])
						$validate[] = $fileInput;
				}
			}

			$errorMsg = '';
			foreach ($validate as $c => $v) {
				if (trim($_POST[$v]) == '') {
					$errorMsg = $language['translationCompleteFields'][$config['lang']];
					break;
				}
			}

			foreach ($config['siteLangs'] as $langSuffix) {
				// $validate[] = 'reservations'.$langSuffix;
			}

			if (is_array($images)) {
				foreach ($images as $fileInput => $value) {
					if (!$value['validate'])
						$validate[] = $fileInput;
				}
			}

			if (!$errorMsg) {

				unset($fields);
				foreach ($validate as $c => $v)
					$fields[$v] = $_POST[$v];

				if ($_GET['id']) {

					$array = getArray($config, $tableId, $tableName, $_GET['id']);
					if ($array[$tableId]) {

						$stmt = $conn->prepare("update $tableName set ".prepareFields($fields, 'update')." where $tableId = :$tableId");
						$fields[$tableId] = $_GET['id'];
						$stmt->execute(prepareFieldsArray($fields));

						$id = $_GET['id'];
					} else {

						$stmt = $conn->prepare("insert into $tableName (".implode(', ', array_keys($fields)).") values (".prepareFields($fields, 'insert').")");
						$stmt->execute(prepareFieldsArray($fields));

						$id = $conn->lastInsertId();
					}

				} else {

					$stmt = $conn->prepare("insert into $tableName (".implode(', ', array_keys($fields)).") values (".prepareFields($fields, 'insert').")");
					$stmt->execute(prepareFieldsArray($fields));

					$id = $conn->lastInsertId();
				}

				if ($id) {

					// generateUri($config, $tableName, $id);

					if (!$_GET['id']) {
						// position
						$gid = $conn->prepare("select MAX(position) from $tableName");
						$gid->execute();
						$groupid = $gid->fetchColumn();

						unset($fields);
						$fields[$orderBy] = $groupid ? ($groupid + 1) : 1;

						$stmt = $conn->prepare("update $tableName set ".prepareFields($fields, 'update')." where $tableId = :$tableId");
						$fields[$tableId] = $id;
						$stmt->execute(prepareFieldsArray($fields));
					}
				}

				header("location: ?msgUpdated=1");
				exit;
			}
		}

		if (count($_POST)) {
			$array = xmlFormat($_POST);

		} elseif ($_GET['id']) {
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

		if (is_array($images)) {
			foreach ($images as $fileInput => $value) {

				if ($array[$fileInput]) {
					$render[$fileInput.'Preview'] = "<img src='".$value['uploadDir']."/".$array[$fileInput]."' class='img-responsive' />";
				}
			}
		}

		if ($errorMsg) {
			$render['errorMsg'] = $errorMsg;
		}

		$render['linkCancel'] = $render['currentPage'].'?';
	}

	$render['qs'] = $_SERVER['QUERY_STRING'];

	if ($_GET['mode'] == 'order') {
		$render['mode'] = $_GET['mode'];
	}

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>