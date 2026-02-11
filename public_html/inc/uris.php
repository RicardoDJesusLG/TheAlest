<?php

$render['base_href'] = $config['site_url'].'/';

//if ($langSuffix == 'Pt')
//	$render['base_href'] = $config['site_url'].$config['uri_pt'];

$render['uri_en'] = $config['uri_en'];
$render['uri_es'] = $config['uri_es'];

if ($langSuffix == 'Es')
	$site_root = $config['uri_es'];
elseif ($langSuffix == 'En')
	$site_root = $config['uri_en'];

// uris
foreach ($config['uris'][strtolower($langSuffix)] as $key => $value) {

	if ($key == 'link_root')
		$render[$key] = $site_root.$value;
	else
		$render[$key] = $site_root.$value.'/';

	if ($key == 'link_root')
		$render[$key.'_full'] = $config['site_url'].$site_root.$value;
	else
		$render[$key.'_full'] = $config['site_url'].$site_root.$value.'/';
}

/*
$render['base_href'] = $config['site_url'].'/';

// uris
foreach ($config['uris'] as $key => $value) {
	if ($key == 'link_root')
		$render[$key] = $value;
	else
		$render[$key] = $value.'/';

	if ($key == 'link_root')
		$render[$key.'_full'] = $config['site_url'].$value;
	else
		$render[$key.'_full'] = $config['site_url'].'/'.$value.'/';
}
*/

?>