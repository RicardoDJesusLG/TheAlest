<?php

session_start();
require '../vendor/autoload.php';
include './cms/inc/config.php';
include './cms/inc/lang.php';
include './cms/inc/functions.php';
$langSuffix = getLang();

$conn = getDbConnection($config);

$loader = new \Twig\Loader\FilesystemLoader($langSuffix == 'En' ? '.' : 'es');
$twig = new \Twig\Environment($loader);

    $render = array();
    include './inc/uris.php';

    // formulario
    unset($frmError);
    unset($validate);

    if ($_POST['frmSubmitContact']) {

        $captchaResponse = json_decode(getFileContent('https://www.google.com/recaptcha/api/siteverify?secret='.$config['recaptchaSecret'].'&response='.$_POST['g-recaptcha-response'].'&remoteip='.getIp()), true);

        $validate[] = 'frmNombre';
        $validate[] = 'frmApellido';
        $validate[] = 'frmEmail';
        $validate[] = 'frmPais';
        $validate[] = 'frmCiudad';
        $validate[] = 'frmTelefono';

        foreach ($validate as $v) {
            if (trim($_POST[$v]) == '') {
                $frmError = $language['translationCompleteFields'][strtolower($langSuffix)];
            }
        }

        $validate[] = 'frmAsunto';
        $validate[] = 'frmComentarios';

        if (!$frmError) {

            if (!checkMail($_POST['frmEmail'])) {
                $frmError = $language['translationCompleteEmail'][strtolower($langSuffix)];
            } elseif ($captchaResponse['success'] != 1) {
                 $frmError = $language['translationIncorrectCaptcha'][strtolower($langSuffix)];
            } else {

                unset($fields);
                $fields['fecha'] = date("Y-m-d H:i:s");
                $fields['ip'] = getIp();
                $fields['lang'] = $langSuffix;
                $fields['frmNombre'] = $_POST['frmNombre'];
                $fields['frmApellido'] = $_POST['frmApellido'];
                $fields['frmEmail'] = $_POST['frmEmail'];
                $fields['frmTelefono'] = $_POST['frmTelefono'];
                $fields['frmPais'] = $_POST['frmPais'];
                $fields['frmCiudad'] = $_POST['frmCiudad'];
                $fields['frmAsunto'] = $_POST['frmAsunto'];
                $fields['frmComentarios'] = $_POST['frmComentarios'];

                foreach ($_POST as $c => $v) {
                    if (!is_array($v))
                        $_POST[$c] = stripslashes(htmlspecialchars($v, ENT_QUOTES));
                }

                $_POST['frmComentarios'] = nl2br($_POST['frmComentarios']);

                $content = <<<EOD
                <b>Name:</b> {$_POST['frmNombre']} {$_POST['frmApellido']} <br />
                <b>Email:</b> {$_POST['frmEmail']} <br />
                <b>Country:</b> {$_POST['frmPais']} <br />
                <b>City:</b> {$_POST['frmCiudad']} <br />
                <b>Phone:</b> {$_POST['frmTelefono']} <br />
                <b>Subject:</b> {$_POST['frmAsunto']} <br />
                <b>Comments:</b> {$_POST['frmComentarios']} <br />
EOD;

                $fields['html'] = $content;

                $stmt = $conn->prepare("insert into {$config['prefix']}contacto (".implode(', ', array_keys($fields)).") values (".prepareFields($fields, 'insert').")");
                $stmt->execute(prepareFieldsArray($fields));

                unset($params);
                $params['fromMail'][$config['email_address']] = $config['siteName'];
                $params['subject'] = $config['siteName'].' - Contact';
                $params['toMail'][$config['email_address']] = $config['siteName'];
                $params['replyMail'][$fields['frmEmail']] = stripslashes($fields['frmNombre'].' '.$fields['frmApellido']);
                $params['content'] = utf8_decode($content);

                if (sendEmail($config, $params)) {
                    header("location: ".$render['link_contact_thanks']);
                    exit;
                } else {
                    $frmError = $language['translationErrorSendingEmail'][strtolower($langSuffix)];
                }
            }
        }
    }

    if ($frmError) {
        $render['frmMessage'] = $frmError;
    } elseif ($_GET['msg']) {
        $render['frmMessageThanks'] = true;
    }

    if (is_array($validate)) {
        foreach ($validate as $c => $v) {
            $render[$v] = $_POST[$v];
        }
    }

    $render['recaptchaPublic'] = $config['recaptchaPublic'];

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>