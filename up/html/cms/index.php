<?php

session_start();
require '../../vendor/autoload.php';
include './inc/config.php';
include './inc/lang.php';
include './inc/functions.php';

$conn = getDbConnection($config);

validateLogin('login.php');
validateSeccion($config, $language);

// ------------------------------------------------- //
$sectionTitle = "Articles";
$sectionMenu = "articles";
$tableName = "{$config['prefix']}articles";
$tableId = "id";
$orderBy = "pubDate desc, $tableId desc";
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
				$items[$row[$tableId]]['status'] = $row['status'];
				$items[$row[$tableId]]['id'] = $row[$tableId];
				$items[$row[$tableId]]['lang'] = $row['lang'];

				$tmp = explode("-", $row['pubDate']);
				$items[$row[$tableId]]['fecha'] = $config['lang'] == 'es' ? $tmp[2].'/'.$tmp[1].'/'.$tmp[0] : $tmp[1].'/'.$tmp[2].'/'.$tmp[0];
			}

			$render['items'] = $items;
		}

		if ($_GET['msgUpdated']) {
			$render['msgUpdated'] = $language['translationMsgUpdated'][$config['lang']];
		}

		$render['linkAdd'] = $render['currentPage'].'?mode=edit';

	} elseif ($render['mode'] == 'edit') {

		// $images = array('image' => array('validate' => true, 'uploadDir' => '../files/articles', 'thumbs' => array(0 => array('uploadDir' => '../files/articles/thumbs', 'width' => '386', 'height' => '213'))));
		$images = array('image' => array('validate' => true, 'uploadDir' => '../files/articles'));
		$files = null;

		if ($_POST['submitUpdate']) {

			$_POST = aTrim($_POST);

			unset($validate);
			$validate[] = 'title';
			$validate[] = 'intro';
			$validate[] = 'description';
			$validate[] = 'pubDate';
			$validate[] = 'lang';

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

			if (is_array($files)) {
				foreach ($files as $fileInput => $value) {

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

			$validate[] = 'status';

			if (is_array($images)) {
				foreach ($images as $fileInput => $value) {
					if (!$value['validate'])
						$validate[] = $fileInput;
				}
			}

			if (is_array($files)) {
				foreach ($files as $fileInput => $value) {
					if (!$value['validate'])
						$validate[] = $fileInput;
				}
			}

			if (!$errorMsg) {

				$tmp1 = explode('/', $_POST['pubDate']);

				if ($config['lang'] == 'es') {
					$checkdate_month = 1;
					$checkdate_day = 0;
					$checkdate_year = 2;
				} else {
					$checkdate_month = 0;
					$checkdate_day = 1;
					$checkdate_year = 2;
				}

				if (!checkdate($tmp1[$checkdate_month], $tmp1[$checkdate_day], $tmp1[$checkdate_year])) {
					$errorMsg = $language['translationCompleteDate'][$config['lang']];
				} else {

					unset($fields);
					foreach ($validate as $c => $v)
						$fields[$v] = $_POST[$v];

					$fields['status'] = abs($fields['status']);
					$fields['pubDate'] = "{$tmp1[$checkdate_year]}-{$tmp1[$checkdate_month]}-{$tmp1[$checkdate_day]}";

					if (is_array($files)) {
						foreach ($files as $fileInput => $value) {
							if ($_POST[$fileInput.'_delete'])
								$fields[$fileInput] = '';
						}
					}

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
						generateUri($config, $tableName, $id);
					}

					header("location: ?msgUpdated=1");
					exit;
				}
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

			if ($array['pubDate']) {
				$tmp = explode("-", $array['pubDate']);
				$array['pubDate'] = $config['lang'] == 'es' ? $tmp[2].'/'.$tmp[1].'/'.$tmp[0] : $tmp[1].'/'.$tmp[2].'/'.$tmp[0];
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

		if (is_array($files)) {
			foreach ($files as $fileInput => $value) {

				$deleteImage = $deleteImageChecked = '';
				if ($array[$fileInput]) {
					if (!$value['validate']) {
						$deleteImageChecked = $array[$fileInput.'_delete'] ? 'checked' : '';
						$deleteImage = "<div class='checkbox'><label><input type='checkbox' id='".$fileInput."_delete' name='".$fileInput."_delete' value='1' ".$deleteImageChecked." /> ".$language['translationDelete'][$config['lang']].'?</label></div>';
					}

					$render[$fileInput.'Preview'] = $array[$fileInput].$deleteImage;
				}
			}
		}
		
		// langs
		unset($items);
		foreach ($config['langs'] as $value) {
			$items[$value]['text'] = $value;
			$items[$value]['id'] = $value;
			$items[$value]['selected'] = $value == $array['lang'] ? 'selected' : '';
		}
		$render['langs'] = $items;

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
