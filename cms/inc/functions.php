<?php

function getCurrentPage() {
	$tmp = pathinfo($_SERVER['PHP_SELF']);
	return $tmp['filename'];
}

function validateLogin($redirectTo) {
	if (!$_SESSION['cms_logged']) {
		header("location: $redirectTo");
		exit;
	}
}

function toXml($s) {
	if (is_array($s)) {
		foreach($s as $c => $v) {
			$s[$c] = htmlspecialchars(stripslashes($v));
		}
	} else $s = htmlspecialchars(stripslashes($c));

	return $s;
}

function xmlFormat($s) {
	if (is_array($s)) {
		foreach($s as $c => $v) {
			if (!is_array($v)) {
				$s[$c] = stripslashes($v);
			}
		}
	} else $s = stripslashes($s);

	return $s;
}

function xmlFormatSave($s) {
	if (is_array($s)) {
		foreach($s as $c => $v) {
			$s[$c] = utf8_encode($v);
		}
	} else $s = utf8_encode($s);

	return $s;
}

function xmlFormatJs($s) {
	if (is_array($s)) {
		foreach($s as $c => $v) {
			$s[$c] = stripslashes(htmlspecialchars($v, ENT_QUOTES));
		}
	} else $s = stripslashes(htmlspecialchars($s, ENT_QUOTES));

	return $s;
}

function sendEmail($config, $params, $phpmailer = false) {

	/*

	$params[subject] = xxxxxxxxx
	$params[fromMail][xxx@xxx.xx] = xxxxxxxxx
	$params[toMail][xxx@xxx.xx] = xxxxxxxxx
	$params[replyMail][xxx@xxx.xx] = xxxxxxxxx
	$params[content] = xxxxxxxxx

	*/

	if (!$phpmailer) {

		$sheader = "From:".key($params[fromMail])."\nReply-To:".key($params[replyMail])."\n";
		$sheader = $sheader."X-Mailer:PHP/".phpversion()."\n";
		$sheader = $sheader."Mime-Version: 1.0\n";
		$sheader = $sheader."Content-Type: text/html";

		$return = true;
		foreach ($params[toMail] as $c => $v) {
			if (!mail($c, $params[subject], $params[content], $sheader)) {
				$return = false;
			}
		}

		return $return;

	} else {
		return sendEmailPHPMailer($config, $params);
	}
}

function sendEmailPHPMailer($config, $params) {

	$mail = new PHPMailer();
	//$mail->IsSMTP();
	$mail->Host = $config['email_server'];

	if ($config['email_smtp_username'] && $config['email_smtp_password']) {
		$mail->IsSMTP();

		$mail->SMTPAuth = true; // turn on SMTP authentication
		$mail->Username = $config['email_smtp_username'];
		$mail->Password = $config['email_smtp_password'];
	}

	$mail->SetFrom(key($params[fromMail]), $params[fromMail][key($params[fromMail])]);

	$mail->ClearReplyTos();
	$mail->AddReplyTo(key($params[replyMail]), $params[replyMail][key($params[replyMail])]);

	$mail->Subject = $params[subject];

	$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
	$mail->MsgHTML($params[content]);

	foreach ($params[toMail] as $c => $v)
	$mail->AddAddress($c, $v);

	if(!$mail->Send()) {
		return false; // echo "Mailer Error: " . $mail->ErrorInfo; exit;
	} else {
		return true;
	}
}

function prepareFields($fields, $mode) {
	if ($mode == 'insert') {
		foreach ($fields as $c => $v) {
			$tmp[] = ":$c";
		}
	} elseif ($mode == 'update') {
		foreach ($fields as $c => $v) {
			$tmp[] = "$c = :$c";
		}
	}

	return implode(', ', $tmp);
}

function prepareFieldsArray($fields) {

	$return = array();

	if (is_array($fields)) {
		foreach ($fields as $key => $value) {
			$return[':'.$key] = $value;
		}
	}

	return $return;
}

function getArray($config, $id, $tableName, $value, $and='') {

	$conn = getDbConnection($config);

	$stmt = $conn->prepare("select * from $tableName where $id = :value $and");

	unset($queryParams);
	$queryParams['value'] = $value;

	$stmt->execute($queryParams);
	$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (count($row)) {
		return $row[0];
	}
}

function checkMail($email){
	$mail_correcto = 0;
	if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){
		if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) {
			if (substr_count($email,".")>= 1){
				$term_dom = substr(strrchr ($email, '.'),1);
				if (strlen($term_dom)>1 && strlen($term_dom)<9 && (!strstr($term_dom,"@")) ){
					$antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);
					$caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);
					if ($caracter_ult != "@" && $caracter_ult != "."){
						$mail_correcto = 1;
					}
				}
			}
		}
	}
	if ($mail_correcto)        return 1;
	else				       return 0;
}

function copyImage($idField, $ruta, $files, $checkImage = true) {

	if (is_uploaded_file($files[$idField]['tmp_name']))	{

		if ($checkImage) {
			if (!validImage(exif_imagetype($files[$idField]['tmp_name']))) {
				return false;
			}
		}

		$nomImagen = generateName($ruta, getPathInfo($files[$idField]['name']));
		$nomImagenPath = "$ruta/$nomImagen";

		if (file_exists($nomImagenPath)) {
			@chmod($nomImagenPath, 0777);
			@unlink($nomImagenPath);
		}

		copy($files[$idField]['tmp_name'], $nomImagenPath);
		@chmod($nomImagenPath, 0777);

		return $nomImagen;
	}
}

function validImage($i) {

	/*

	1 	IMAGETYPE_GIF
	2 	IMAGETYPE_JPEG
	3 	IMAGETYPE_PNG
	4 	IMAGETYPE_SWF
	5 	IMAGETYPE_PSD
	6 	IMAGETYPE_BMP
	7 	IMAGETYPE_TIFF_II (orden de byte intel)
	8 	IMAGETYPE_TIFF_MM (orden de byte motorola)
	9 	IMAGETYPE_JPC
	10 	IMAGETYPE_JP2
	11 	IMAGETYPE_JPX
	12 	IMAGETYPE_JB2
	13 	IMAGETYPE_SWC
	14 	IMAGETYPE_IFF
	15 	IMAGETYPE_WBMP
	16 	IMAGETYPE_XBM
	17 	IMAGETYPE_ICO

	*/

	$validFormats = array(1, 2, 3);
	if (in_array($i, $validFormats)) {
		return true;
	}
}

function generateName($ruta, $pathinfo, $prefix = '') {
	if (file_exists($ruta."/".$prefix.$pathinfo['filename'].'.'.$pathinfo['extension'])) {
		$noExiste = false;

		$counter = 1;

		while (!$noExiste) {
			$counter++;

			$tmp = $prefix.$pathinfo['filename'].'-'.$counter.'.'.strtolower($pathinfo['extension']);

			if (!file_exists($ruta."/".$tmp)) {
				$noExiste = true;
			}
		}

		return $prefix.$pathinfo['filename'].'-'.$counter;
	} else {
		return $prefix.$pathinfo['filename'];
	}
}

/**
* Function: sanitize
* Returns a sanitized string, typically for URLs.
*
* Parameters:
*     $string - The string to sanitize.
*     $force_lowercase - Force the string to lowercase?
*     $anal - If set to *true*, will remove all non-alphanumeric characters.
*/
function sanitize($string, $force_lowercase = true, $anal = false) {

	$strip = array("ñ", "Ñ", "~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
		"}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
		"â€”", "â€“", ",", "<", ".", ">", "/", "?");
		$clean = trim(str_replace($strip, "", strip_tags($string)));
		$clean = preg_replace('/\s+/', "-", $clean);
		$clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
		return ($force_lowercase) ?
		(function_exists('mb_strtolower')) ?
		mb_strtolower($clean, 'UTF-8') :
		strtolower($clean) :
		$clean;
	}

function getPathInfo($file) {
	$pathinfo = pathinfo($file);
	if (!isset($pathinfo['filename']))
	$pathinfo['filename'] = substr($pathinfo['basename'], 0, strpos($pathinfo['basename'], '.'));

	$pathinfo['filename'] = sanitize($pathinfo['filename']);
	return $pathinfo;
}

function formatPrice($number, $sign = true) {

	$number = abs(str_replace(',', '.', $number));
	$sign = $sign ? '$' : '';

	if ($number) {
		return $sign.number_format($number, 2, '.', '');
	} else {
		return $sign.' -';
	}
}

function getMoneda() {
	return 'U$S';
}

function getStrMonth($mesCurso, $lang = '') {

	if ($lang == 'Es') {

		$mes[1] = 'Enero';
		$mes[]  = 'Febrero';
		$mes[]  = 'Marzo';
		$mes[]  = 'Abril';
		$mes[]  = 'Mayo';
		$mes[]  = 'Junio';
		$mes[]  = 'Julio';
		$mes[]  = 'Agosto';
		$mes[]  = 'Septiembre';
		$mes[]  = 'Octubre';
		$mes[]  = 'Noviembre';
		$mes[]  = 'Diciembre';

	} else {

		$mes[1] = 'January';
		$mes[]  = 'February';
		$mes[]  = 'March';
		$mes[]  = 'April';
		$mes[]  = 'May';
		$mes[]  = 'June';
		$mes[]  = 'July';
		$mes[]  = 'August';
		$mes[]  = 'September';
		$mes[]  = 'October';
		$mes[]  = 'November';
		$mes[]  = 'December';
	}

	return $mes[$mesCurso];
}

function getStrMonthShort($mesCurso, $lang = '') {

	if ($lang == 'Es') {

		$mes[1] = 'Ene';
		$mes[]  = 'Feb';
		$mes[]  = 'Mar';
		$mes[]  = 'Abr';
		$mes[]  = 'May';
		$mes[]  = 'Jun';
		$mes[]  = 'Jul';
		$mes[]  = 'Ago';
		$mes[]  = 'Sep';
		$mes[]  = 'Oct';
		$mes[]  = 'Nov';
		$mes[]  = 'Dic';

	} else {

		$mes[1] = 'Jan';
		$mes[]  = 'Feb';
		$mes[]  = 'Mar';
		$mes[]  = 'Apr';
		$mes[]  = 'May';
		$mes[]  = 'Jun';
		$mes[]  = 'Jul';
		$mes[]  = 'Aug';
		$mes[]  = 'Sep';
		$mes[]  = 'Oct';
		$mes[]  = 'Nov';
		$mes[]  = 'Dec';
	}

	return $mes[$mesCurso];
}

function getStrDay($id) {
	$dia[1] = 'Monday';
	$dia[]  = 'Tuesday';
	$dia[]  = 'Wednesday';
	$dia[]  = 'Thursday';
	$dia[]  = 'Friday';
	$dia[]  = 'Saturday';
	$dia[]  = 'Sunday';

	return $dia[$id];
}

function getLang() {

	if (stristr($_SERVER['REQUEST_URI'], '/es/') !== FALSE) {
		return 'Es';
	} else {
		return 'En';
	}
}

function getVideoId($url, $type) {
	if ($type == 'vimeo')
		$result = preg_match('/(\d+)/', $url, $matches);
	elseif ($type == 'youtube')
		$result = preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);

	if ($result) {
		return $matches[0];
	}
}

/**
 * Gets a vimeo thumbnail url
 * @param mixed $id A vimeo id (ie. 1185346)
 * @return thumbnail's url
*/
function getVimeoThumb($id) {
    $data = file_get_contents("http://vimeo.com/api/v2/video/$id.json");
    $data = json_decode($data);

    // var_dump($data[0]);

    return $data[0]->thumbnail_large;
}

function puedoEditarAdmin($config, $idAdmin) {
	if (!$idAdmin) {
		return false;
	} else {
		$array = getArray($config, 'idAdmin', "{$config['prefix']}admin", $idAdmin);
		if (!$array['idAdmin']) {
			return false;
		} else {
			if ($_SESSION['cms_logged'] == $array['idAdmin']) {
				return true;
			} else {

				if ($_SESSION['cms_logged'] == $config['devAdminID']) {
					return true;
				} else {

					if ($array['statusAdmin'] == 1) {
						return false;
					} else {
						return true;
					}
				}
			}
		}
	}
}

function puedoBorrarAdmin($config, $idAdmin) {
	if (!$idAdmin) {
		return false;
	} else {
		$array = getArray($config, 'idAdmin', "{$config['prefix']}admin", $idAdmin);
		if (!$array['idAdmin']) {
			return false;
		} else {
			if ($_SESSION['cms_logged'] == $array['idAdmin']) {
				return false;
			} else {

				if ($_SESSION['cms_logged'] == $config['devAdminID']) {
					return true;
				} else {

					if ($array['idAdmin'] == $config['mainAdminID'] || $array['idAdmin'] == $config['devAdminID']) {
						return false;
					} else {
						if ($_SESSION['cms_logged'] == $config['mainAdminID']) {
							return true;
						} else {
							if ($array['statusAdmin'] == 1) {
								return false;
							} else {
								return true;
							}
						}
					}
				}
			}
		}
	}
}

function usernameAdminExists($config, $type, $emailAdmin, $idAdmin) {

	$conn = getDbConnection($config);

	if ($idAdmin)
	$and = " and idAdmin != :idAdmin ";

	$res = $conn->prepare("select count(*) as k from {$config['prefix']}admin where emailAdmin = :emailAdmin $and");

	unset($queryParams);
	$queryParams['emailAdmin'] = $emailAdmin;

	if ($idAdmin)
	$queryParams['idAdmin'] = $idAdmin;

	$res->execute($queryParams);

	if ($res->fetchColumn() > 0) {
		return true;
	} else {
		return false;
	}
}

function validateAdmin($config, $idAdmin) {

	$conn = getDbConnection($config);

	$res = $conn->prepare("select count(*) as k from {$config['prefix']}admin where idAdmin = :idAdmin");

	unset($queryParams);
	$queryParams['idAdmin'] = $idAdmin;

	$res->execute($queryParams);

	if (!$res->fetchColumn()) {
		header("location: index.php?logout=true");
		exit;
	}
}

function validateSeccion($config, $language) {

	$conn = getDbConnection($config);

	$tmp = pathinfo($_SERVER['PHP_SELF']);
	$pagina = $tmp['basename'];

	$stmt = $conn->prepare("select * from {$config['prefix']}admin where idAdmin = :idAdmin");

	unset($queryParams);
	$queryParams['idAdmin'] = $_SESSION['cms_logged'];

	$stmt->execute($queryParams);
	$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($row)) {

		$row = $row[0];

		if ($row['statusAdmin'] == 1) {
			return true;
		} else {

			$aSecciones = $conn->query("select * from {$config['prefix']}secciones");
			if (count($aSecciones)) {

				foreach($aSecciones as $rowSecciones) {

					$tmp = explode(",", $rowSecciones['paginaSeccion']);

					if (is_array($tmp)) {
						foreach ($tmp as $c => $v) {
							if (trim($v) == $pagina) {

								$stmtSeccion = $conn->prepare("select * from {$config['prefix']}admin_secciones where idAdmin = :idAdmin and idSeccion = :idSeccion");

								unset($queryParams);
								$queryParams['idAdmin'] = $_SESSION['cms_logged'];
								$queryParams['idSeccion'] = $rowSecciones['idSeccion'];

								$stmtSeccion->execute($queryParams);
								$rowSeccion = $stmtSeccion->fetchAll(PDO::FETCH_ASSOC);
								if (count($rowSeccion)) {
									return true;
								} else {

									$loader = new \Twig\Loader\FilesystemLoader('.');
									$twig = new \Twig\Environment($loader);

									$render = array();
									$render['noAccessTitle'] = xmlFormat($language['translationNoAccessTitle'][$config['lang']]);
									$render['no_nombreSeccion'] = xmlFormat($rowSecciones['nombreSeccion']);

									include 'inc/inc_container.php';

									echo $twig->render('template_no.html', $render);

									exit;
								}
							}
						}
					}

				}
			}
		}
	}

	return false;
}

function removeNl($string) {
	$string = str_replace("\n", '', $string);
	$string = str_replace("\r", ' ', $string);

	return $string;
}

/*
function getMysqlDate($date) {
	if (getLang() == 'En') {
		$tmp = explode('/', $date);

		$month = $tmp[0];
		$day = $tmp[1];
		$year = $tmp[2];

		return "$year-$month-$day";
	} else {
		$tmp = explode('/', $date);

		$month = $tmp[1];
		$day = $tmp[0];
		$year = $tmp[2];

		return "$year-$month-$day";
	}
}
*/

function getStrDate($date) {

	$tmp = explode('-', $date);

	$month = $tmp[1];
	$day = $tmp[2];
	$year = $tmp[0];

	if (getLang() == 'En') {
		return "$month/$day/$year";
	} else {
		return "$day/$month/$year";
	}
}

function getStrDateTime($date, $extra = '') {
	if (getLang() == 'En') {
		return date("m/d/Y h:iA", strtotime($date.$extra));
	} else {
		return date("d/m/Y H:i", strtotime($date.$extra)).'hs.';
	}
}

function getLatLon($address) {

	$get = getFileContent('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false');

	$tmp = json_decode($get);
	$tmp = $tmp->results;
	$tmp = $tmp[0];

	$return['lat'] = $tmp->geometry->location->lat;
	$return['lon'] = $tmp->geometry->location->lng;

	//print_r($return); exit;

	return $return;
}

function getFileContent($url) {
	if (function_exists('curl_init')) {
		$request = curl_init($url);
		curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
		// curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
		$content = curl_exec($request); // execute curl post and store results in $post_response
		curl_close ($request);
	} else {
		$content = file_get_contents($url);
	}

	return $content;
}

function clink($link) {
	if (substr($link, 0, strlen('http://')) != 'http://' && substr($link, 0, strlen('https://')) != 'https://')
		$link = 'http://'.$link;

		return $link;
}

function aTrim($s) {
	if (is_array($s)) {
		foreach($s as $c => $v) {
			if (!is_array($v))
			$s[$c] = trim($v);
		}
	} else $s = trim($s);

	return $s;
}

function printArray($array) {

	if (is_array($array)) {
		echo '<pre>'.print_r($array, true).'</pre>';
	}
}

function generatePassword($config, $password) {
	return sha1($config['step'].$password);
}

function formatHtmlToPlain($s) {
	return trim(strip_tags($s));
}

function text2url($string, $noSpaces = false) {

	return url_slug($string);

	/*
	$string = trim($string);

	$string = str_replace('á', 'a', $string);
	$string = str_replace('é', 'e', $string);
	$string = str_replace('í', 'i', $string);
	$string = str_replace('ó', 'o', $string);
	$string = str_replace('ú', 'u', $string);
	$string = str_replace('Á', 'a', $string);
	$string = str_replace('É', 'e', $string);
	$string = str_replace('Í', 'i', $string);
	$string = str_replace('Ó', 'o', $string);
	$string = str_replace('Ú', 'u', $string);
	$string = str_replace('ñ', 'n', $string);
	$string = str_replace('Ñ', 'n', $string);

	$spacer = "-";
	$string = trim($string);
	$string = strtolower($string);
	$string = trim(ereg_replace("[^ A-Za-z0-9_]", " ", $string));

	$string = ereg_replace("[ \t\n\r]+", "-", $string);
	$string = str_replace(" ", $spacer, $string);
	$string = ereg_replace("[ -]+", "-", $string);

	if ($noSpaces)
	$string = str_replace("-", "", $string);

	return $string;
	*/
}

/**
 * Create a web friendly URL slug from a string.
 *
 * Although supported, transliteration is discouraged because
 *     1) most web browsers support UTF-8 characters in URLs
 *     2) transliteration causes a loss of information
 *
 * @author Sean Murphy <sean@iamseanmurphy.com>
 * @copyright Copyright 2012 Sean Murphy. All rights reserved.
 * @license http://creativecommons.org/publicdomain/zero/1.0/
 *
 * @param string $str
 * @param array $options
 * @return string
 */
function url_slug($str, $options = array()) {

	$delimiter = '-';

	$str = str_replace('á', 'a', $str);
	$str = str_replace('é', 'e', $str);
	$str = str_replace('í', 'i', $str);
	$str = str_replace('ó', 'o', $str);
	$str = str_replace('ú', 'u', $str);
	$str = str_replace('Á', 'a', $str);
	$str = str_replace('É', 'e', $str);
	$str = str_replace('Í', 'i', $str);
	$str = str_replace('Ó', 'o', $str);
	$str = str_replace('Ú', 'u', $str);
	$str = str_replace('ñ', 'n', $str);
	$str = str_replace('Ñ', 'n', $str);
	
	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
	return $clean;

	/*

	// Make sure string is in UTF-8 and strip invalid UTF-8 characters
	$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

	$defaults = array(
		'delimiter' => '-',
		'limit' => null,
		'lowercase' => true,
		'replacements' => array(),
		'transliterate' => false,
	);

	// Merge options
	$options = array_merge($defaults, $options);

	$char_map = array(
		// Latin
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
		'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
		'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
		'ß' => 'ss',
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
		'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
		'ÿ' => 'y',

		// Latin symbols
		'©' => '(c)',

		// Greek
		'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
		'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
		'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
		'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
		'Ϋ' => 'Y',
		'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
		'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
		'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
		'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
		'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

		// Turkish
		'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
		'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',

		// Russian
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
		'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
		'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
		'я' => 'ya',

		// Ukrainian
		'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
		'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

		// Czech
		'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
		'Ž' => 'Z',
		'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
		'ž' => 'z',

		// Polish
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
		'Ż' => 'Z',
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
		'ż' => 'z',

		// Latvian
		'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
		'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
		'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
		'š' => 's', 'ū' => 'u', 'ž' => 'z'
	);

	// Make custom replacements
	$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

	// Transliterate characters to ASCII
	if ($options['transliterate']) {
		$str = str_replace(array_keys($char_map), $char_map, $str);
	}

	// Replace non-alphanumeric characters with our delimiter
	$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

	// Remove duplicate delimiters
	$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

	// Truncate slug to max. characters
	$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

	// Remove delimiter from ends
	$str = trim($str, $options['delimiter']);

	return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
	*/
}

# Spherical Law of Cosines
function distance_slc($lat1, $lon1, $lat2, $lon2) {

	$earth_radius = 3960.00; # in miles

	$delta_lat = $lat2 - $lat1 ;
	$delta_lon = $lon2 - $lon1 ;

	$distance  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($delta_lon)) ;
	$distance  = acos($distance);
	$distance  = rad2deg($distance);
	$distance  = $distance * 60 * 1.1515;
	$distance  = round($distance, 4);

	return $distance;
}

function ss($array) {
	if (is_array($array)) {
		foreach($array as $c => $v) {
			$array[$c] = stripslashes($v);
		}
	} else $array = stripslashes($array);

	return $array;
}

function defaultString($string, $substr = null) {
	return trim($string) != '' ? ($substr ? (strlen($string) > $substr ? substr($string, 0, $substr).' ...' : $string) : $string) : '-';
}

function getIp() {

	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

// Generates a strong password of N length containing at least one lower case letter,
// one uppercase letter, one digit, and one special character. The remaining characters
// in the password are chosen at random from those four sets.
//
// The available characters in each set are user friendly - there are no ambiguous
// characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
// makes it much easier for users to manually type or speak their passwords.
//
// Note: the $add_dashes option will increase the length of the password by
// floor(sqrt(N)) characters.

function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
{
	$sets = array();
	if(strpos($available_sets, 'l') !== false)
	$sets[] = 'abcdefghjkmnpqrstuvwxyz';
	if(strpos($available_sets, 'u') !== false)
	$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	if(strpos($available_sets, 'd') !== false)
	$sets[] = '23456789';
	if(strpos($available_sets, 's') !== false)
	$sets[] = '!@#$%&*?';

	$all = '';
	$password = '';
	foreach($sets as $set)
	{
		$password .= $set[array_rand(str_split($set))];
		$all .= $set;
	}

	$all = str_split($all);
	for($i = 0; $i < $length - count($sets); $i++)
	$password .= $all[array_rand($all)];

	$password = str_shuffle($password);

	if(!$add_dashes)
	return $password;

	$dash_len = floor(sqrt($length));
	$dash_str = '';
	while(strlen($password) > $dash_len)
	{
		$dash_str .= substr($password, 0, $dash_len) . '-';
		$password = substr($password, $dash_len);
	}
	$dash_str .= $password;
	return $dash_str;
}

function formatFecha($fecha) {
	$tmp = strtotime($fecha);
	return date("d", $tmp).'.'.date("m", $tmp).'.'.date("Y", $tmp);
}

/*
function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
*/

/******************************************************************************/

function UxmlFormat($s) {
	if (is_array($s)) {
		foreach($s as $c => $v) {
			$s[$c] = utf8_encode($v);
		}
	} else $s = utf8_encode($s);

	return $s;
}

function formatFechaHora($fecha) {
	$tmp = strtotime($fecha);
	return date("d", $tmp).'.'.date("m", $tmp).'.'.date("Y", $tmp).' '.date("H", $tmp).':'.date("i", $tmp).'hs.';
}

function getStrDia($i) {
	$lang = getLang();

	$aDias = array();
	if ($lang == 'Es') {

		$aDias[1] = 'Lunes';
		$aDias[] = 'Martes';
		$aDias[] = 'Miercoles';
		$aDias[] = 'Jueves';
		$aDias[] = 'Viernes';
		$aDias[] = 'Sabado';
		$aDias[] = 'Domingo';

	} elseif ($lang == 'En') {

		$aDias[1] = 'Lunes';
		$aDias[] = 'Martes';
		$aDias[] = 'Miercoles';
		$aDias[] = 'Jueves';
		$aDias[] = 'Viernes';
		$aDias[] = 'Sabado';
		$aDias[] = 'Domingo';

	} elseif ($lang == 'Po') {

		$aDias[1] = 'Lunes';
		$aDias[] = 'Martes';
		$aDias[] = 'Miercoles';
		$aDias[] = 'Jueves';
		$aDias[] = 'Viernes';
		$aDias[] = 'Sabado';
		$aDias[] = 'Domingo';
	}

	return $aDias[$i];
}

function getWeatherCss($icono) {

	if ($icono >= 200 && $icono <= 299) {
		return 'wi-thunderstorm';
	} elseif ($icono >= 300 && $icono <= 399) { // drizzle
		return 'wi-rain';
	} elseif ($icono >= 500 && $icono <= 599) {
		return 'wi-rain';
	} elseif ($icono >= 600 && $icono <= 699) {
		return 'wi-day-snow';
	} elseif ($icono == 701) { // mist
		return 'wi-fog';
	} elseif ($icono == 711) { // smoke
		return 'wi-fog';
	} elseif ($icono == 721) { // haze
		return 'wi-fog';
	} elseif ($icono == 731) { // sand, dust whirls
		return 'wi-fog';
	} elseif ($icono == 741) { // fog
		return 'wi-fog';
	} elseif ($icono == 751) { // sand
		return 'wi-fog';
	} elseif ($icono == 761) { // dust
		return 'wi-strong-wind';
	} elseif ($icono == 762) { // volcanic ash
		return 'wi-fog';
	} elseif ($icono == 771) { // squalls
		return 'wi-fog';
	} elseif ($icono == 781) { // tornado
		return 'wi-tornado';
	} elseif ($icono == 800) { // clear sky
		return 'wi-day-sunny';
	} elseif ($icono >= 801 && $icono <= 899) {
		return 'wi-cloudy';
	} elseif ($icono == 900) { // (extreme) tornado
		return 'wi-tornado';
	} elseif ($icono == 901) { // (extreme)  tropical storm
		return 'wi-day-storm-showers';
	} elseif ($icono == 902) { // (extreme)  hurricane
		return 'wi-tornado';
	} elseif ($icono == 903) { // (extreme)  cold
		return 'wi-down';
	} elseif ($icono == 904) { // (extreme)  hot
		return 'wi-day-sunny';
	} elseif ($icono == 905) { // (extreme)  windy
		return 'wi-windy';
	} elseif ($icono == 906) { // (extreme)  hail
		return 'wi-hail';
	} elseif ($icono == 951) { // (extreme)  calm
		return 'wi-cloudy';
	} elseif ($icono == 952) { // (extreme)  light breeze
		return 'wi-windy';
	} elseif ($icono == 953) { // (extreme)  gentle breeze
		return 'wi-windy';
	} elseif ($icono == 954) { // (extreme)  moderate breeze
		return 'wi-windy';
	} elseif ($icono == 955) { // (extreme)  fresh breeze
		return 'wi-windy';
	} elseif ($icono == 956) { // (extreme)  strong breeze
		return 'wi-windy';
	} elseif ($icono == 957) { // (extreme)  high wind, near gale
		return 'wi-windy';
	} elseif ($icono == 958) { // (extreme)  gale
		return 'wi-windy';
	} elseif ($icono == 959) { // (extreme)  severe gale
		return 'wi-windy';
	} elseif ($icono == 960) { // (extreme)  storm
		return 'wi-day-storm-showers';
	} elseif ($icono == 961) { // (extreme)  violent storm
		return 'wi-day-storm-showers';
	} elseif ($icono == 962) { // (extreme)  hurricane
		return 'wi-tornado';
	} else {
		return 'wi-cloudy';
	}
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function getMysqlDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d->format('Y-m-d');
}

/**
 *
 * @param Array $list
 * @param int $p
 * @return multitype:multitype:
 * @link http://www.php.net/manual/en/function.array-chunk.php#75022
 */
function partition(Array $list, $p) {
    $listlen = count($list);
    $partlen = floor($listlen / $p);
    $partrem = $listlen % $p;
    $partition = array();
    $mark = 0;
    for($px = 0; $px < $p; $px ++) {
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
        $partition[$px] = array_slice($list, $mark, $incr);
        $mark += $incr;
    }
    return $partition;
}

function nl2p($string)
{
    $paragraphs = '';

    foreach (explode("\n", $string) as $line) {
        if (trim($line)) {
            $paragraphs .= '<p>' . $line . '</p>';
        }
    }

    return $paragraphs;
}

// Original PHP code by Chirp Internet: www.chirp.com.au
// Please acknowledge use of this code by including this header.

function cleanData(&$str)
{
if($str == 't') $str = 'TRUE';
if($str == 'f') $str = 'FALSE';
if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
  $str = "'$str";
}
if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

function generateUri($config, $tableName, $id) {

	if ($tableName == "{$config['prefix']}articles") {

		$slugName = 'slug';
		$titleName = 'title';
		$idName = 'id';

		$conn = getDbConnection($config);

		$stmt = $conn->prepare("select * from $tableName where $idName = :value");

		unset($queryParams);
		$queryParams['value'] = $id;

		$stmt->execute($queryParams);
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($row)) {

			$row = $row[0];

			foreach ($config['siteLangs'] as $lang) {

				if ($row[$titleName.$lang] != '') {
					$url = $tmp_url = text2url($row[$titleName.$lang]);

					$counter = 1;
					$flag = false;

					while (!$flag) {
						$counter++;

						$stmt = $conn->prepare("select * from $tableName where $idName != :id and {$slugName}{$lang} = :uri");

						unset($queryParams);
						$queryParams['id'] = $id;
						$queryParams['uri'] = $tmp_url;

						$stmt->execute($queryParams);
						$row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

						if (count($row2)) {
							$tmp_url = $url.'-'.$counter;
						} else {
							$flag = true;
						}
					}

					unset($fields);
					$fields[$slugName.$lang] = $tmp_url;

					$stmt = $conn->prepare("update $tableName set ".prepareFields($fields, 'update')." where $idName = :id");

					$fields["id"] = $id;
					$stmt->execute(prepareFieldsArray($fields));
				}
			}
		}
		
		/*
		$slugName = 'slug';
		$titleName = 'title';
		$idName = 'id';

		$conn = getDbConnection($config);

		$stmt = $conn->prepare("select * from $tableName where $idName = :value");

		unset($queryParams);
		$queryParams['value'] = $id;

		$stmt->execute($queryParams);
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($row)) {

			$row = $row[0];

			$url = $tmp_url = text2url($row[$titleName]);

			$counter = 1;
			$flag = false;

			while (!$flag) {
				$counter++;

				$stmt = $conn->prepare("select * from $tableName where $idName != :id and $slugName = :uri");

				unset($queryParams);
				$queryParams['id'] = $id;
				$queryParams['uri'] = $tmp_url;

				$stmt->execute($queryParams);
				$row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if (count($row2)) {
					$tmp_url = $url.'-'.$counter;
				} else {
					$flag = true;
				}
			}

			unset($fields);
			$fields[$slugName] = $tmp_url;

			$stmt = $conn->prepare("update $tableName set ".prepareFields($fields, 'update')." where $idName = :id");

			$fields["id"] = $id;
			$stmt->execute(prepareFieldsArray($fields));
		}
		*/

	} elseif ($tableName == "{$config['prefix']}specials") {

		$slugName = 'slug';
		$titleName = 'title';
		$idName = 'id';

		$conn = getDbConnection($config);

		$stmt = $conn->prepare("select * from $tableName where $idName = :value");

		unset($queryParams);
		$queryParams['value'] = $id;

		$stmt->execute($queryParams);
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($row)) {

			$row = $row[0];

			foreach ($config['siteLangs'] as $lang) {

				if ($row[$titleName.$lang] != '') {
					$url = $tmp_url = text2url($row[$titleName.$lang]);

					$counter = 1;
					$flag = false;

					while (!$flag) {
						$counter++;

						$stmt = $conn->prepare("select * from $tableName where $idName != :id and {$slugName}{$lang} = :uri");

						unset($queryParams);
						$queryParams['id'] = $id;
						$queryParams['uri'] = $tmp_url;

						$stmt->execute($queryParams);
						$row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

						if (count($row2)) {
							$tmp_url = $url.'-'.$counter;
						} else {
							$flag = true;
						}
					}

					unset($fields);
					$fields[$slugName.$lang] = $tmp_url;

					$stmt = $conn->prepare("update $tableName set ".prepareFields($fields, 'update')." where $idName = :id");

					$fields["id"] = $id;
					$stmt->execute(prepareFieldsArray($fields));
				}
			}
		}

	} elseif ($tableName == "{$config['prefix']}rooms") {

		$slugName = 'slug';
		$titleName = 'title';
		$idName = 'id';

		$conn = getDbConnection($config);

		$stmt = $conn->prepare("select * from $tableName where $idName = :value");

		unset($queryParams);
		$queryParams['value'] = $id;

		$stmt->execute($queryParams);
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($row)) {

			$row = $row[0];

			foreach ($config['siteLangs'] as $lang) {

				if ($row[$titleName.$lang] != '') {
					$url = $tmp_url = text2url($row[$titleName.$lang]);

					$counter = 1;
					$flag = false;

					while (!$flag) {
						$counter++;

						$stmt = $conn->prepare("select * from $tableName where $idName != :id and {$slugName}{$lang} = :uri");

						unset($queryParams);
						$queryParams['id'] = $id;
						$queryParams['uri'] = $tmp_url;

						$stmt->execute($queryParams);
						$row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

						if (count($row2)) {
							$tmp_url = $url.'-'.$counter;
						} else {
							$flag = true;
						}
					}

					unset($fields);
					$fields[$slugName.$lang] = $tmp_url;

					$stmt = $conn->prepare("update $tableName set ".prepareFields($fields, 'update')." where $idName = :id");

					$fields["id"] = $id;
					$stmt->execute(prepareFieldsArray($fields));
				}
			}
		}

	} elseif ($tableName == "{$config['prefix']}gallery_categories") {

		$slugName = 'slug';
		$titleName = 'title';
		$idName = 'id';

		$conn = getDbConnection($config);

		$stmt = $conn->prepare("select * from $tableName where $idName = :value");

		unset($queryParams);
		$queryParams['value'] = $id;

		$stmt->execute($queryParams);
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($row)) {

			$row = $row[0];

			foreach ($config['siteLangs'] as $lang) {

				if ($row[$titleName.$lang] != '') {
					$url = $tmp_url = text2url($row[$titleName.$lang]);

					$counter = 1;
					$flag = false;

					while (!$flag) {
						$counter++;

						$stmt = $conn->prepare("select * from $tableName where $idName != :id and {$slugName}{$lang} = :uri");

						unset($queryParams);
						$queryParams['id'] = $id;
						$queryParams['uri'] = $tmp_url;

						$stmt->execute($queryParams);
						$row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

						if (count($row2)) {
							$tmp_url = $url.'-'.$counter;
						} else {
							$flag = true;
						}
					}

					unset($fields);
					$fields[$slugName.$lang] = $tmp_url;

					$stmt = $conn->prepare("update $tableName set ".prepareFields($fields, 'update')." where $idName = :id");

					$fields["id"] = $id;
					$stmt->execute(prepareFieldsArray($fields));
				}
			}
		}
	}
}

function getArticleLink($config, $slug) {
	$langSuffix = getLang();

	if ($langSuffix == 'Es')
		$site_root = '/es';
	else
		$site_root = '';

	return $config['site_url'].$site_root.'/'.$config['uris'][strtolower($langSuffix)]['link_news'].'/'.$slug.'/';	
}

function getLinkSpecial($config, $slug) {

	$langSuffix = getLang();

	if ($langSuffix == 'Es')
		$site_root = '/es';
	else
		$site_root = '';

	return $config['site_url'].$site_root.'/'.$config['uris'][strtolower($langSuffix)]['link_specials'].'/'.$slug.'/';	
}

function getLinkAABB($config, $id) {
	$tmp = getArray($config, 'id', "{$config['prefix']}aabb", $id);

	$langSuffix = getLang();

	if ($langSuffix == 'Es')
		$site_root = '/es';
	else
		$site_root = '';

	return $config['site_url'].$site_root.'/'.$config['uris'][strtolower($langSuffix)]['link_aabb'].'/'.$tmp['slug'].'/';	
}

function getLinkEvents($config, $id) {
	$tmp = getArray($config, 'id', "{$config['prefix']}events", $id);

	$langSuffix = getLang();

	if ($langSuffix == 'Es')
		$site_root = '/es';
	else
		$site_root = '';

	return $config['site_url'].$site_root.'/'.$config['uris'][strtolower($langSuffix)]['link_events'].'/'.$tmp['slug'].'/';	
}

function getLinkGallery($config, $slug) {

	$langSuffix = getLang();

	if ($langSuffix == 'Es')
		$site_root = '/es';
	else
		$site_root = '';

	return $config['site_url'].$site_root.'/'.$config['uris'][strtolower($langSuffix)]['link_gallery'].'/'.$slug.'/';	
}

function getLinkRoom($config, $slug) {

	$langSuffix = getLang();

	if ($langSuffix == 'Es')
		$site_root = '/es';
	else
		$site_root = '';

	return $config['site_url'].$site_root.'/'.$config['uris'][strtolower($langSuffix)]['link_rooms'].'/'.$slug.'/';	
}

?>