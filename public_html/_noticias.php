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

            $items[$index]['image'] = $config['site_url'].'/files/slider/'.$row['image'];
            $index++;
        }

        $aInstagram = $items;
    }

    // articles
    $index = 0;
    $indexImg = 1;
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}articles where status{$langSuffix} = '1' order by pubDate desc");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);

            $items[$index]['id_'.$indexImg] = $row[$tableId];
            $items[$index]['title_'.$indexImg] = $row['title'.$langSuffix];
            $items[$index]['intro_'.$indexImg] = $row['intro'.$langSuffix];
            $items[$index]['image_'.$indexImg] = $config['site_url'].'/files/articles/'.$row['image'];
            $items[$index]['date_'.$indexImg] = date("M d, Y", strtotime($row['pubDate']));
            $items[$index]['link_'.$indexImg] = getArticleLink($config, $row['slug'.$langSuffix]);

            if ($indexImg == 5) {
                $items[$index]['image_instagram_'.$indexImg] = $aInstagram[$index]['image'];
            }

            $indexImg++;
            if ($indexImg > 5) {
                $indexImg = 1;
                $index++;
            }
        }
        //printArray($items); exit;
        $render['articles'] = $items;
    }

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>