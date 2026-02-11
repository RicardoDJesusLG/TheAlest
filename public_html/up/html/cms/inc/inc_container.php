<?php

$render['siteName'] = $config['siteName'];
$render['siteLang'] = $config['lang'];
$render['showAdminSection'] = $_SESSION['cms_type'] == 1 ? true : false;
$render['adminProfile'] = $_SESSION['cms_name'];

if (is_array($language)) {
    foreach ($language as $c => $v) {
        $render[$c] = $v[$config['lang']];
    }
}

$render['base_href'] = $config['site_url'].'/cms/';

?>
