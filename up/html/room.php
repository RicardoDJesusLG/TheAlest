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

    // rooms
    $items = array();
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}rooms where slug{$langSuffix} = :slug and status = '1'");
    $query->execute(array('slug' => $_GET['slug']));
    $res = $query->fetchAll();
    if (count($res)) {
        foreach ($res as $row) {
            $row = xmlFormat($row);

            $render['title'] = $row['title'.$langSuffix];
            $render['pretitle'] = $row['pretitle'.$langSuffix];
            $render['intro'] = $row['intro'.$langSuffix];
            $render['intro2'] = $row['intro2'.$langSuffix];
            $render['text'] = $row['text'.$langSuffix];
            $render['image'] = $config['site_url'].'/files/rooms/'.$row['image'];
            $render['servicesImage'] = $config['site_url'].'/files/rooms/'.$row['servicesImage'];
            
            if ($row['services'.$langSuffix])
                $render['services'] = explode('<br />', removeNl(nl2br(trim($row['services'.$langSuffix]))));

            $render['specialInfoTitle'] = $row['specialInfoTitle'.$langSuffix];
            $render['specialInfoText'] = $row['specialInfoText'.$langSuffix];

            $queryTmp = $conn->prepare("select * from {$config['prefix']}rooms_slider where status = '1' and idRelated = '{$row[$tableId]}' order by position");
            $queryTmp->execute();
            $resTmp = $queryTmp->fetchAll();
            if (count($resTmp)) {
                foreach ($resTmp as $rowTmp) {
                    $rowTmp = xmlFormat($rowTmp);
                    $items[$rowTmp[$tableId]]['image'] = $config['site_url'].'/files/rooms/'.$rowTmp['image'];
                }

                $render['slider'] = $items;
            }

            // rooms - all
            $tableId = 'id';
            $query = $conn->prepare("select * from {$config['prefix']}rooms where status = '1' and $tableId != '{$row[$tableId]}' order by position");
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
        }
    } else {
        header("location: ".$render['link_rooms']);
        exit;
    }

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>