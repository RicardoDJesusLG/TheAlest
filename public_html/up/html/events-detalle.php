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

    // events
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}events where slug = :slug");
    $query->execute(array('slug' => $_GET['slug']));
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);

            $render['title'] = $row['pretitle'];
            $render['pretitle'] = $row['pretitle'.$langSuffix];
            $render['intro'] = $row['intro'.$langSuffix];
            $render['text'] = $row['text'.$langSuffix];
            $render['imageHero'] = $config['site_url'].'/files/aabb/'.$row['imageHero'];
            $render['image'] = $config['site_url'].'/files/aabb/'.$row['image'];
            $render['caption'] = $row['caption'.$langSuffix];
            
            $render['link'] = getLinkEvents($config, $row[$tableId]);

            $tmp = getimagesize($render['image']);
            $render['image_w'] = $tmp[0];
            $render['image_h'] = $tmp[1];

            // share
            $render['fb_share_url'] = 'https://www.facebook.com/sharer/sharer.php?u='.$render['link'];
            $render['tw_share_url'] = 'https://twitter.com/intent/tweet?text='.urlencode($render['title'].' '.$render['link']);
            $render['wa_share_url'] = 'https://api.whatsapp.com/send?text='.($render['title'].' / '.$render['link']);
        }
    } else {
        header("location: ".$render['link_events']);
        exit;
    }

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>