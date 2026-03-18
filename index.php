<?php

session_start();
require '../vendor/autoload.php';
include './cms/inc/config.php';
include './cms/inc/lang.php';
include './cms/inc/functions.php';
$langSuffix = getLang();

$conn = getDbConnection($config);

if (isset($_POST['footerNewsletterEmail'])) {

    if (stristr($_SERVER['HTTP_REFERER'], '/es/') !== FALSE) {
        $langSuffix = 'Es';
    } else {
        $langSuffix = 'En';
    }

    $_POST['footerNewsletterEmail'] = trim($_POST['footerNewsletterEmail']);

    $errorMsg = '';

    if (!checkMail($_POST['footerNewsletterEmail'])) {
        $errorMsg = $language['translationCompleteEmail'][strtolower($langSuffix)];
    } else {

        $queryTmp = $conn->prepare("select * from {$config['prefix']}newsletter where emailPost = :emailPost");
        $queryTmp->execute(array('emailPost' => $_POST['footerNewsletterEmail']));
        $resTmp = $queryTmp->fetchAll();
        if (count($resTmp)) {
            $errorMsg = $language['translationCompleteAnotherEmail'][strtolower($langSuffix)];
        } else {

            unset($fields);
            $fields['datePost'] = date("Y-m-d H:i:s");
            $fields['emailPost'] = $_POST['footerNewsletterEmail'];
            $fields['ipPost'] = getIp();
            $fields['langPost'] = $langSuffix;

            $stmt = $conn->prepare("insert into {$config['prefix']}newsletter (".implode(', ', array_keys($fields)).") values (".prepareFields($fields, 'insert').")");
            if ($stmt->execute(prepareFieldsArray($fields))) {
                echo 'ok';
                exit;
            } else {
                $errorMsg = $language['translationErrorSaving'][strtolower($langSuffix)];
            }
        }
    }

    if ($errorMsg != '') {
        echo $errorMsg;
    }

    exit;
}

$loader = new \Twig\Loader\FilesystemLoader($langSuffix == 'En' ? '.' : 'es');
$twig = new \Twig\Environment($loader);

    $render = array();
    include './inc/uris.php';

    // slider
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}slider where status = '1' order by position");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);
            $items[$row[$tableId]]['image'] = $config['site_url'].'/files/slider/'.$row['image'];
        }

        $render['slider'] = $items;
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

    // specials
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}specials where status{$langSuffix} = '1' order by position");
    $query->execute();
    $res = $query->fetchAll();
    if (count($res)) {
        unset($items);
        foreach ($res as $row) {
            $row = xmlFormat($row);
            $items[$row[$tableId]]['title'] = $row['title'.$langSuffix];
            $items[$row[$tableId]]['intro'] = $row['intro'.$langSuffix];
            $items[$row[$tableId]]['link'] = getLinkSpecial($config, $row['slug'.$langSuffix]);
            $items[$row[$tableId]]['image'] = $config['site_url'].'/files/specials/'.$row['image'];
        }

        $render['specials'] = $items;
    }

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
    $query = $conn->prepare("select * from {$config['prefix']}articles where status{$langSuffix} = '1' order by pubDate desc limit 5");
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