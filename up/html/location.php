<?php

session_start();
require '../vendor/autoload.php';
include './cms/inc/config.php';
include './cms/inc/lang.php';
include './cms/inc/functions.php';
$langSuffix = getLang();

$conn = getDbConnection($config);

$loader = new \Twig\Loader\FilesystemLoader($langSuffix == 'En' ? '.' : 'Es');
$twig = new \Twig\Environment($loader);

    $render = array();
    include './inc/uris.php';

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>