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

    // rooms
    $items = array();
    $tableId = 'id';
    $query = $conn->prepare("select * from {$config['prefix']}rooms where slug{$langSuffix} = :slug and status = '1'");
    $query->execute(array('slug' => $_GET['slug']));
    $res = $query->fetchAll();
    if (count($res)) {
        foreach ($res as $row) {
            $row = xmlFormat($row);

            $render['idType'] = $row['idType'];
            $render['dimensions'] = $row['dimensions'.$langSuffix];
            $render['title'] = $row['title'.$langSuffix];
            $render['pretitle'] = $row['pretitle'.$langSuffix];
            $render['intro'] = $row['intro'.$langSuffix];
            $render['intro2'] = $row['intro2'.$langSuffix];
            $render['text'] = $row['text'.$langSuffix];
            $render['image'] = $config['site_url'].'/files/rooms/'.$row['image'];
            $render['servicesImage'] = $config['site_url'].'/files/rooms/'.$row['servicesImage'];
            
            /*
             if ($row['services'.$langSuffix])
                $render['services'] = explode('<br />', removeNl(nl2br(trim($row['services'.$langSuffix]))));
            */

            // 1) MAP: texto normalizado => icono FontAwesome
            $serviceIconMap = [
                'vista exterior' => 'fa fa-eye',
                'cama king size' => 'fa fa-bed',
                'aire acondicionado y calefacción' => 'fa fa-snowflake-o',
                'cortinas blackout' => 'fa fa-moon-o',
                'armario' => 'fa fa-suitcase',
                'escritorio' => 'fa fa-pencil',
                'caja de seguridad' => 'fa fa-lock',
                'tv lcd 55"' => 'fa fa-television',
                'tv lcd 50"' => 'fa fa-television',
                'teléfono (llamadas locales, a canadá y eua ilimitadas)' => 'fa fa-phone',
                'lujoso baño privado' => 'fa fa-bath',
                'doble lavabo' => 'fa fa-tint',
                'lavabo' => 'fa fa-tint',
                'tina y ducha' => 'fa fa-tint',
                'ducha' => 'fa fa-tint',
                'wifi en cortesía' => 'fa fa-wifi',
                'servicio de streaming (netflix y 40 canales de televisión)' => 'fa fa-play-circle',
                'bata de baño' => 'fa fa-user',
                'pantuflas' => 'fa fa-sun-o',
                'secador de pelo' => 'fa fa-check',
                'dyson – airwrap (long) – copper™' => 'fa fa-magic',
                'plancha de vapor para ropa' => 'fa fa-bolt',
                'cafetera nespresso' => 'fa fa-coffee',
                'tetera' => 'fa fa-leaf',
                'minibar (sodas y snacks de cortesía)' => 'fa fa-glass',
                'botellas con agua en cortesía' => 'fa fa-tint',
                'dos botellas de champaña mini' => 'fa fa-star',
                'una caja con tres macarrones' => 'fa fa-gift',
                'arreglo floral' => 'fa fa-pagelines',
                'servicio a cuartos de 7.00 am hasta las 11 pm. domingos 7.30 am a 11 pm' => 'fa fa-clock-o',
                'terraza: 2 sillones con descansa-pies y mesa de centro' => 'fa fa-tree',
                'salón extra: sofá-cama, medio baño para visitas y tv lcd 55"' => 'fa fa-plus',
                'coffee table books en la mesa de centro' => 'fa fa-book',
                'doble terraza: mesa de cristal con 6 sillas, dos camastros con mesa al centro.' => 'fa fa-tree',
                'servicio de transportación en the house car con cargo extra (lincoln navigator reserve)' => 'fa fa-car',


                /*--------------------------------------En ingles-------------------------------------*/

                'outdoor view' => 'fa fa-eye',
                'king size bed' => 'fa fa-bed',

                // Comfort & environment
                'air conditioning and heating' => 'fa fa-snowflake-o',
                'blackout curtains' => 'fa fa-moon-o',

                // Furniture & storage
                'closet' => 'fa fa-suitcase',
                'desk' => 'fa fa-pencil',

                // Security & tech
                'safe' => 'fa fa-lock',
                '55" lcd tv' => 'fa fa-television',
                'phone (unlimited local calls, and calls to canada and the u.s.)' => 'fa fa-phone',

                // Bathroom
                'luxurious private bathroom' => 'fa fa-bath',
                'double sink' => 'fa fa-tint',
                'bath and shower' => 'fa fa-tint',

                // Connectivity & entertainment
                'complimentary wi-fi' => 'fa fa-wifi',
                'streaming service (netflix and 40 television channels)' => 'fa fa-play-circle',

                // Personal care
                'bathrobe' => 'fa fa-user',
                'slippers' => 'fa fa-sun-o',
                'hair dryer' => 'fa fa-check',
                'dyson - airwrap (long) - copper™' => 'fa fa-magic',

                // Appliances
                'steam iron for clothes' => 'fa fa-bolt',
                'nespresso coffee machine' => 'fa fa-coffee',
                'tea kettle' => 'fa fa-leaf',

                // Minibar & courtesy
                'minibar (complimentary soft drinks and snacks)' => 'fa fa-glass',
                'complimentary bottles of water' => 'fa fa-tint',
                'two miniature champagne bottles' => 'fa fa-star',
                'box containing three macaroons' => 'fa fa-gift',

                // Extras & services
                'flower arrangement' => 'fa fa-pagelines',
                'room service from 7:00 am to 11:00 pm. sunday 7.30 am to 11pm' => 'fa fa-clock-o',
                'terrace: 2 armchairs with footrests and center table' => 'fa fa-tree',
                'transportation service in the house car, for an additional charge (lincoln navigator reserve)' => 'fa fa-car',
            ];

// 2) Normalizador de strings para poder matchear aunque cambien acentos / comillas
            $normalizeService = function(string $s): string {
                $s = trim($s);

                // arreglar comillas raras y guiones
                $s = str_replace(["â€","“","”","″"], '"', $s);
                $s = str_replace(["–","—"], "-", $s);

                // pasar a minúsculas
                $s = mb_strtolower($s, 'UTF-8');

                // normalizar espacios
                $s = preg_replace('/\s+/', ' ', $s);

                return $s;
            };

            if ($row['services'.$langSuffix]) {
                $rawServices = explode('<br />', removeNl(nl2br(trim($row['services'.$langSuffix]))));

                $servicesFinal = [];
                foreach ($rawServices as $srv) {
                    $srv = trim($srv);
                    if ($srv === '') continue;

                    $key = $normalizeService($srv);
                    $icon = $serviceIconMap[$key] ?? 'fa fa-check'; // default

                    $servicesFinal[] = [
                        'text' => $srv,
                        'icon' => $icon,
                    ];
                }

                $render['services'] = $servicesFinal;
            }


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