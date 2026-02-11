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

    // gallery_categories
    $index = 0;
    $indexImg = 1;
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}gallery_categories where status = '1' and $tableId in (select idRelated from {$config['prefix']}gallery where status = '1') order by position");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        unset($items2);
        foreach ($res as $row) {
            $row = xmlFormat($row);

            $items2[$row[$tableId]]['id'] = $row[$tableId];
            $items2[$row[$tableId]]['title'] = $row['title'.$langSuffix];

            $queryTmp = $conn->prepare("select * from {$config['prefix']}gallery where status = '1' and idRelated = '{$row[$tableId]}' order by position");
            $queryTmp->execute();
            $resTmp = $queryTmp->fetchAll();
            if (count($resTmp)) {
                foreach ($resTmp as $rowTmp) {
                    $rowTmp = xmlFormat($rowTmp);
                    $items2[$row[$tableId]]['images'][$rowTmp[$tableId]]['title'] = $rowTmp['title'.$langSuffix];
                    $items2[$row[$tableId]]['images'][$rowTmp[$tableId]]['image'] = $config['site_url'].'/files/gallery/'.$rowTmp['image'];
                }
            }

            $items[$index]['order'] = $index % 2 == 0;

            $items[$index]['id_'.$indexImg] = $row[$tableId];
            $items[$index]['title_'.$indexImg] = $row['title'.$langSuffix];
            $items[$index]['image_'.$indexImg] = $config['site_url'].'/files/gallery/'.$row['image'];

            $indexImg++;
            if ($indexImg > 3) {
                $indexImg = 1;
                $index++;
            }


        }
        // printArray($items); exit;
        $render['gallery_categories'] = $items;
        $render['gallery_categories_2'] = $items2;
    }

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>