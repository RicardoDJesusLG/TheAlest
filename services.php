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

    // testimonials
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}testimonials where status = '1' order by rand()");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);
            $items[$row[$tableId]]['author'] = $row['author'];
            $items[$row[$tableId]]['text'] = $row['text'];
            $items[$row[$tableId]]['location'] = $row['location'];
        }

        $render['testimonials'] = $items;
    }






    if ($langSuffix == 'Es')
    	$site_root = '/es';
    else
    	$site_root = '';

    $render['link_spa'] = $config['site_url'].$site_root.'/'.$config['uris'][strtolower($langSuffix)]['link_spa'].'/';

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>