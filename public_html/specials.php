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

    // specials
    $index = 1;
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}specials where status{$langSuffix} = '1' order by position");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);

            $items[$row[$tableId]]['index'] = $index++;
            $items[$row[$tableId]]['title'] = $row['title'.$langSuffix];
            $items[$row[$tableId]]['intro'] = $row['intro'.$langSuffix];
            $items[$row[$tableId]]['link'] = getLinkSpecial($config, $row['slug'.$langSuffix]);
            $items[$row[$tableId]]['image'] = $config['site_url'].'/files/specials/'.$row['image'];
        }

        $render['specials'] = $items;
    }

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>