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

    $render['link_events_corporate'] = getLinkEvents($config, 1);
    $render['link_events_social'] = getLinkEvents($config, 2);

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>