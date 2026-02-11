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

    // tyc
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}tyc where $tableId = '1'");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);
            $render['title'] = $row['legalTitle'.$langSuffix];
            $render['text'] = $row['legalText'.$langSuffix];
        }
    }

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>