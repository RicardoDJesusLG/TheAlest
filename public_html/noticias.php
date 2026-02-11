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
/*
    // instagram
    $aInstagram = array();
    $index = 0;
    $indexImg = 1;
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}articles_slider_instagram where status = '1' order by position");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);

            $items[$row[$tableId]]['image'] = $config['site_url'].'/files/slider/'.$row['image'];
            $index++;
        }

        $aInstagram = $items;
    }
*/
    // articles
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}articles where status{$langSuffix} = '1' order by pubDate desc");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);

            $items[$row[$tableId]]['id'] = $row[$tableId];
            $items[$row[$tableId]]['title'] = $row['title'.$langSuffix];
            $items[$row[$tableId]]['intro'] = $row['intro'.$langSuffix];
            $items[$row[$tableId]]['image'] = $config['site_url'].'/files/articles/'.$row['image'];
            $items[$row[$tableId]]['date'] = date("M d, Y", strtotime($row['pubDate']));
            $items[$row[$tableId]]['link'] = getArticleLink($config, $row['slug'.$langSuffix]);
        }
        //printArray($items); exit;
        $render['articles'] = $items;
    }

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>