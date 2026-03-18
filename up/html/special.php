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

    // specials
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}specials where slug{$langSuffix} = :slug and status{$langSuffix} = '1'");
    $query->execute(array('slug' => $_GET['slug']));
    $res = $query->fetchAll();
    if (count($res)) {
        foreach ($res as $row) {
            $row = xmlFormat($row);

            $render['title'] = $row['title'.$langSuffix];
            $render['intro'] = $row['intro'.$langSuffix];
            $render['text'] = $row['text'.$langSuffix];
            $render['image'] = $config['site_url'].'/files/specials/'.$row['image'];
            
            $render['link'] = getLinkSpecial($config, $row['slug'.$langSuffix]);

            $tmp = getimagesize($render['image']);
            $render['image_w'] = $tmp[0];
            $render['image_h'] = $tmp[1];

            // share
            $render['fb_share_url'] = 'https://www.facebook.com/sharer/sharer.php?u='.$render['link'];
            $render['tw_share_url'] = 'https://twitter.com/intent/tweet?text='.urlencode($render['title'].' '.$render['link']);
            $render['wa_share_url'] = 'https://api.whatsapp.com/send?text='.($render['title'].' / '.$render['link']);
        }
    } else {
        header("location: ".$render['link_specials']);
        exit;
    }

echo $twig->render('template_'.getCurrentPage().'.html', $render);

?>