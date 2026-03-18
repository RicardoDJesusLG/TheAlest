<?php

// print_r($_REQUEST); exit;

$datein = explode('/', $_REQUEST['datein']);
$dateout = explode('/', $_REQUEST['dateout']);
$adults = abs($_REQUEST['adults']);
$children = abs($_REQUEST['children']);

// echo "location: https://be.synxis.com/?adult={$adults}&arrive=".$datein[2]."-".$datein[1]."-".$datein[0]."&chain=27087&child={$children}&childages=|&currency=USD&depart=".$dateout[2]."-".$dateout[1]."-".$dateout[0]."&hotel=31241&level=hotel&locale=en-US&rooms=1"; exit;



// header("location: https://be.synxis.com/?hotel=31241&arrive=".$datein[1]."/".$datein[0]."/".$datein[2]."&depart=".$dateout[1]."/".$dateout[0]."/".$dateout[2]."&adult={$adults}&child={$children}");
header("location: https://be.synxis.com/?adult={$adults}&arrive=".$datein[2]."-".$datein[1]."-".$datein[0]."&chain=27087&child={$children}&childages=|&currency=USD&depart=".$dateout[2]."-".$dateout[1]."-".$dateout[0]."&hotel=31241&level=hotel&locale=en-US&rooms=1");
exit;

// https://be.synxis.com/?adult=1&arrive=2021-08-31&chain=27087&child=2&childages=|&currency=USD&depart=2021-09-01&hotel=31241&level=hotel&locale=en-US&rooms=1

?>