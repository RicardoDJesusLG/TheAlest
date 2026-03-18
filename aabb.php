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

    $render['link_aabb_the_restaurant'] = getLinkAABB($config, 1);
    $render['link_aabb_the_bar'] = getLinkAABB($config, 2);
    $render['link_aabb_the_terrace'] = getLinkAABB($config, 3);

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>