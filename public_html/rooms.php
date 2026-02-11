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

    // rooms - suites
    $index = 0;
    $indexImg = 1;
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}rooms where status = '1' and idType = '1' order by position");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);

            $items[$index]['order'] = $index % 2 == 0;

            $items[$index]['id_'.$indexImg] = $row[$tableId];
            $items[$index]['title_'.$indexImg] = $row['title'.$langSuffix];
            $items[$index]['intro_'.$indexImg] = $row['intro'.$langSuffix];
            $items[$index]['link_'.$indexImg] = getLinkRoom($config, $row['slug'.$langSuffix]);
            $items[$index]['image_'.$indexImg] = $config['site_url'].'/files/rooms/'.$row['image'];

            $indexImg++;
            if ($indexImg > 3) {
                $indexImg = 1;
                $index++;
            }


        }

        $render['suites'] = $items;
    }

    // rooms - rooms
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}rooms where status = '1' and idType = '2' order by position");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);
            $items[$row[$tableId]]['title'] = $row['title'.$langSuffix];
            $items[$row[$tableId]]['intro'] = $row['intro'.$langSuffix];
            $items[$row[$tableId]]['image'] = $config['site_url'].'/files/rooms/'.$row['image'];
            $items[$row[$tableId]]['link'] = getLinkRoom($config, $row['slug'.$langSuffix]);
        }

        $render['rooms'] = $items;
    }

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

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>