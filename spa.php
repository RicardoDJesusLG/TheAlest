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

    if ($langSuffix == 'Es')
    	$site_root = '/es';
    else
    	$site_root = '';

    $render['link_massage'] = $config['site_url'].$site_root.'/'.$config['uris'][strtolower($langSuffix)]['link_spa'].'/'.'massage'.'/';
    $render['link_facial'] = $config['site_url'].$site_root.'/'.$config['uris'][strtolower($langSuffix)]['link_spa'].'/'.'facial'.'/';
    $render['link_exfoliation'] = $config['site_url'].$site_root.'/'.$config['uris'][strtolower($langSuffix)]['link_spa'].'/'.'exfoliation'.'/';

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>