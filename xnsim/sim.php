<?
define('INSIDE', true);

$_SERVER['DOCUMENT_ROOT'] = '/var/www/xnova/data/www/uni3.xnova.su';

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/class.core.php');
core::init();

error_reporting(E_ALL);

include(ROOT_DIR.APP_PATH.'functions/functions.php');
include(ROOT_DIR.APP_PATH.'varsGlobal.php');
include(ROOT_DIR.APP_PATH.'functions/formatCR.php');
include(ROOT_DIR.APP_PATH.'functions/calculateAttack.php');

strings::setLang('ru');
strings::includeLang('tech');

$r = (isset($_GET['r'])) ? explode("|", $_GET['r']) : explode("|", $_POST['r']);

if (!isset($r['0']) || !isset($r['5'])) die('Нет данных для симуляции боя');

$attackUsers = array();
$attackFleets = array();
$defenseUsers = array();
$defense = array();

for ($i = 0; $i < 10; $i++)
{
    if ($i < 5 && $r[$i] != "")
	{
        $attackUsers[$i]['fleet'] 	= array(1, 1, 1);
        $attackUsers[$i]['tech'] 	= array('id' => $i, 'military_tech' => 0, 'shield_tech' => 0, 'defence_tech' => 0, 'laser_tech' => 0, 'ionic_tech' => 0, 'buster_tech' => 0);
        $attackUsers[$i]['flvl'] 	= array();
        $attackUsers[$i]['username']= 'Игрок '.($i+1);

        $attackFleets[$i] = array();
        $temp = explode(';', $r[$i]);

        foreach ($temp as $temp2)
		{
            if ($temp2 == "")
                continue;

            $temp2 = explode(',', $temp2);

            if ($temp2[0] > 200)
			{
                $temp3 = explode('!', $temp2[1]);
                $attackFleets[$i][$temp2[0]] = $temp3[0];
                $attackUsers[$i]['flvl'][$temp2[0]] = $temp3[1];
            }
			else
                $attackUsers[$i]['tech'][$resource[$temp2[0]]] = $temp2[1];
        }
    }
    if ($i >= 5 && isset($r[$i]) && $r[$i] != "")
	{
        $q = $i - 5;

        $defenseUsers[$q]['fleet'] 	= array(2, 2, 2);
        $defenseUsers[$q]['tech'] 	= array('id' => $i, 'military_tech' => 0, 'shield_tech' => 0, 'defence_tech' => 0, 'laser_tech' => 0, 'ionic_tech' => 0, 'buster_tech' => 0);
        $defenseUsers[$q]['flvl']	= array();
        $defenseUsers[$q]['username']= 'Игрок '.($i+1);

        $defense[$q] = array();
        $temp = explode(';', $r[$i]);
        foreach ($temp as $temp2)
		{
            if ($temp2 == "")
                continue;

            $temp2 = explode(',', $temp2);

            if ($temp2[0] > 200)
			{
                $temp3 = explode('!', $temp2[1]);
                $defense[$q][$temp2[0]] = $temp3[0];
                $defenseUsers[$q]['flvl'][$temp2[0]] = $temp3[1];
            }
			else
                $defenseUsers[$q]['tech'][$resource[$temp2[0]]] = $temp2[1];
        }
    }
}

$mtime        = microtime();
$mtime        = explode(" ", $mtime);
$mtime        = $mtime[1] + $mtime[0];
$starttime    = $mtime;

$result        = calculateAttack($attackFleets, $defense, $attackUsers, $defenseUsers, 0);

$mtime        = microtime();
$mtime        = explode(" ", $mtime);
$mtime        = $mtime[1] + $mtime[0];
$endtime      = $mtime;
$totaltime    = ($endtime - $starttime);

$FleetDebris      = $result['debree']['att'][0] + $result['debree']['def'][0] + $result['debree']['att'][1] + $result['debree']['def'][1];

$MoonChance  = round($FleetDebris / 100000);

if ($FleetDebris > 2000000)
    $MoonChance = 20;

$result = array($result, $attackUsers, $defenseUsers, array('metal' => 0, 'crystal' => 0, 'deuterium' => 0), $MoonChance, '', $totaltime);

$Page = '';

if (isset($_GET['ingame']))
{
	$formatted_cr = formatCREx($result[0], $result[1], $result[2], $result[3], $result[4], $result[5], $result[6]);

	$Page .= stripslashes( $formatted_cr['html'] );
	$Page .= '<script>$(function(){$(\'#raportRaw\').multiAccordion({active: ['.(count($result[0]['rw']) - 1).']})});;</script>';

	echo $Page;
}
else
{
	$formatted_cr = formatCR($result[0], $result[1], $result[2], $result[3], $result[4], $result[5], $result[6]);

	$Page  = "<html>";
	$Page .= "<head>";
	$Page .= "<title>XNova SIM (0.5) Симуляция боя</title>";
	$Page .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"/xnsim/report.css\">";
	$Page .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />";
	$Page .= "</head>";
	$Page .= "<body><script>function show(id){if(document.getElementById(id).style.display==\"block\")document.getElementById(id).style.display=\"none\"; else document.getElementById(id).style.display=\"block\";}</script>";
	$Page .= "<center>";
	$Page .= "<table width=\"99%\">";
	$Page .= "<tr>";
	$Page .= "<td>". stripslashes( $formatted_cr['html'] ) ."</td>";
	$Page .= "</tr>";
	$Page .= "</table>";
	$Page .= "</div></div></center>";
	$Page .= "<center>Made by AlexPro for <a href=\"http://xnova.su/\" target=\"_blank\">XNova - ".UNIVERSE." UNIVERSE</a></center>";
	$Page .= "</body>";
	$Page .= "</html>";

	echo $Page;
}

?>