<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * @var $page page
 * @var $user user
 * @var $resource array
 * @var $reslist array
 * @var $CombatCaps array
 * @var app::$planetrow planet
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if (!defined("INSIDE"))
	die("attemp hacking");

$MaxFlyingFleets = db::first(db::query("SELECT COUNT(fleet_owner) AS `actcnt` FROM game_fleets WHERE `fleet_owner` = '" . user::get()->data['id'] . "';", true));

$MaxExpedition = user::get()->data[$resource[124]];
$ExpeditionEnCours = 0;
$EnvoiMaxExpedition = 0;

if ($MaxExpedition >= 1)
{
	$ExpeditionEnCours = db::first(db::query("SELECT COUNT(fleet_owner) AS `expedi` FROM game_fleets WHERE `fleet_owner` = '" . user::get()->data['id'] . "' AND `fleet_mission` = '15';", true));
	$EnvoiMaxExpedition = 1 + floor($MaxExpedition / 3);
}

$MaxFlottes = 1 + user::get()->data[$resource[108]];
if (user::get()->data['rpg_admiral'] > time())
	$MaxFlottes += 2;

strings::includeLang('fleet');

$galaxy = (isset($_GET['galaxy'])) ? intval($_GET['galaxy']) : 0;
$system = (isset($_GET['system'])) ? intval($_GET['system']) : 0;
$planet = (isset($_GET['planet'])) ? intval($_GET['planet']) : 0;
$planettype = (isset($_GET['planettype'])) ? intval($_GET['planettype']) : 0;
$target_mission = (isset($_GET['target_mission'])) ? intval($_GET['target_mission']) : 0;

if (!$galaxy)
	$galaxy = app::$planetrow->data['galaxy'];

if (!$system)
	$system = app::$planetrow->data['system'];

if (!$planet)
	$planet = app::$planetrow->data['planet'];

if (!$planettype)
	$planettype = 1;

$html = "<script language=\"JavaScript\" src=\"/scripts/flotten.js\"></script>\n";
$html .= "<br><center>";
$html .= "<table width='100%' border='0' cellpadding='0' cellspacing='1'>";
$html .= "<tr height='20'>";
$html .= "<td colspan='9' class='c'>";
$html .= "<table border=\"0\" width=\"100%\">";
$html .= "<tr>";
$html .= "<td style=\"background-color: transparent;\" align=\"left\">";
$html .= _getText('fl_title') . " " . $MaxFlyingFleets . " " . _getText('fl_sur') . " " . $MaxFlottes;
$html .= "</td><td style=\"background-color: transparent;\" align=\"right\">";
$html .= (0 + $ExpeditionEnCours) . "/" . (0 + $EnvoiMaxExpedition) . " " . _getText('fl_expttl');
$html .= "</td>";
$html .= "</tr></table>";
$html .= "</td>";
$html .= "</tr><tr height='20'>";
$html .= "<th width='20'>" . _getText('fl_id') . "</th>";
$html .= "<th>" . _getText('fl_mission') . "</th>";
$html .= "<th>" . _getText('fl_count') . "</th>";
$html .= "<th>" . _getText('fl_from') . "</th>";
$html .= "<th width='80'>" . _getText('fl_start_t') . "</th>";
$html .= "<th>" . _getText('fl_dest') . "</th>";
$html .= "<th width='80'>" . _getText('fl_dest_t') . "</th>";
$html .= "<th>" . _getText('fl_back_in') . "</th>";
$html .= "<th width='110'>" . _getText('fl_order') . "</th>";
$html .= "</tr>";

$fq = db::query("SELECT * FROM game_fleets WHERE fleet_owner=" . user::get()->data['id'] . "");
$i = 0;

while ($f = db::fetch($fq))
{
	$i++;
	$html .= "<tr height=20>";
	$html .= "<th>" . $i . "</th>";
	$html .= "<th>";
	$html .= "<a>" . _getText('type_mission', $f['fleet_mission']) . "</a>";
	if (($f['fleet_start_time'] + 1) == $f['fleet_end_time'])
	{
		$html .= "<br><a title=\"" . _getText('fl_back_to_ttl') . "\">" . _getText('fl_back_to') . "</a>";
	}
	else
	{
		$html .= "<br><a title=\"" . _getText('fl_get_to_ttl') . "\">" . _getText('fl_get_to') . "</a>";
	}
	$html .= "</th>";
	$html .= "<th><a class=\"tooltip\" data-tooltip-content='";

	$fleet = explode(";", $f['fleet_array']);
	$fleet_count = 0;

	foreach ($fleet as $a => $b)
	{
		if ($b != '')
		{
			$a = explode(",", $b);
			$c = explode("!", $a[1]);
			$html .= _getText('tech', $a[0]) . ": " . $c[0] . "<br>";

			$fleet_count += $c[0];
		}
	}
	$html .= "'>" . strings::pretty_number($fleet_count) . "</a></th>";
	$html .= "<th><a href=\"?set=galaxy&r=0&galaxy=" . $f['fleet_start_galaxy'] . "&system=" . $f['fleet_start_system'] . "\">[" . $f['fleet_start_galaxy'] . ":" . $f['fleet_start_system'] . ":" . $f['fleet_start_planet'] . "]</a></th>";
	$html .= "<th>" . datezone("d H:i:s", $f['fleet_start_time']) . "</th>";
	$html .= "<th><a href=\"?set=galaxy&r=0&galaxy=" . $f['fleet_end_galaxy'] . "&system=" . $f['fleet_end_system'] . "\">[" . $f['fleet_end_galaxy'] . ":" . $f['fleet_end_system'] . ":" . $f['fleet_end_planet'] . "]</a></th>";
	$html .= "<th>" . datezone("d H:i:s", $f['fleet_end_time']) . "</th>";
	$html .= "<th><font color=\"lime\">" . strings::pretty_time(floor($f['fleet_end_time'] + 1 - time())) . "</font></th>";
	$html .= "<th>";
	if ($f['fleet_mess'] == 0 && $f['fleet_mission'] != 20 && $f['fleet_target_owner'] != 1)
	{
		$html .= "<form action=\"?set=fleet&page=back\" method=\"post\">";
		$html .= "<input name=\"fleetid\" value=\"" . $f['fleet_id'] . "\" type=\"hidden\">";
		$html .= "<input value=\" " . _getText('fl_back_to_ttl') . " \" type=\"submit\" name=\"send\" style=\"width:110px\">";
		$html .= "</form>";
		if ($f['fleet_mission'] == 1)
		{
			$html .= "<form action=\"?set=fleet&page=verband\" method=\"post\">";
			$html .= "<input name=\"fleetid\" value=\"" . $f['fleet_id'] . "\" type=\"hidden\">";
			$html .= "<input value=\" " . _getText('fl_associate') . " \" type=\"submit\" style=\"width:110px\">";
			$html .= "</form>";
		}

	}
	elseif ($f['fleet_mess'] == 3 && $f['fleet_mission'] != 15)
	{
		$html .= "<form action=\"?set=fleet&page=back\" method=\"post\">";
		$html .= "<input name=\"fleetid\" value=\"" . $f['fleet_id'] . "\" type=\"hidden\">";
		$html .= "<input value=\" Отозвать \" type=\"submit\" name=\"send\" style=\"width:110px\">";
		$html .= "</form>";
	}
	else
	{
		$html .= "&nbsp;-&nbsp;";
	}
	$html .= "</th>";
	$html .= "</tr>";
}


if ($i == 0)
{
	$html .= "<tr>";
	$html .= "<th>-</th>";
	$html .= "<th>-</th>";
	$html .= "<th>-</th>";
	$html .= "<th>-</th>";
	$html .= "<th>-</th>";
	$html .= "<th>-</th>";
	$html .= "<th>-</th>";
	$html .= "<th>-</th>";
	$html .= "<th>-</th>";
	$html .= "</tr>";
}

if ($MaxFlottes == $MaxFlyingFleets)
{
	$html .= "<tr height=\"20\"><th colspan=\"9\"><font color=\"red\">" . _getText('fl_noslotfree') . "</font></th></tr>";
}

$html .= "</table></center>";

$html .= "<center>";

$html .= "<br><form action=\"?set=fleet&page=fleet_1\" method=\"post\">";
$html .= "<table class=\"table\">";
$html .= "<tr height=\"20\">";
$html .= "<td colspan=\"4\" class=\"c\">Выбрать корабли";

if ($target_mission > 0)
	$html .= ' для миссии "' . _getText('type_mission', $target_mission) . '"';
if (($system > 0 && $galaxy > 0 && $planet > 0) && ($galaxy != app::$planetrow->data['galaxy'] || $system != app::$planetrow->data['system'] || $planet != app::$planetrow->data['planet']))
	$html .= ' на координаты [' . $galaxy . ':' . $system . ':' . $planet . ']';

$html .= ":</td>";
$html .= "</tr>";
$html .= "<tr height=\"20\">";
$html .= "<th>" . _getText('fl_fleet_typ') . "</th>";
$html .= "<th>" . _getText('fl_fleet_disp') . "</th>";
$html .= "<th>-</th>";
$html .= "<th>-</th>";
$html .= "</tr>";

if (!app::$planetrow)
	$this->message(_getText('fl_noplanetrow'), _getText('fl_error'));

$ShipData = "";
$have_ships = false;

foreach ($reslist['fleet'] as $n => $i)
{
	if (app::$planetrow->data[$resource[$i]] > 0)
	{
		$html .= "<tr height=\"20\">\n";
		$html .= "<th><a title=\"" . _getText('tech', $i) . "\">" . _getText('tech', $i) . "</a></th>\n";
		$html .= "<th>" . strings::pretty_number(app::$planetrow->data[$resource[$i]]);
		$ShipData .= "<input type=\"hidden\" name=\"maxship" . $i . "\" value=\"" . app::$planetrow->data[$resource[$i]] . "\" />\n";
		$ShipData .= "<input type=\"hidden\" name=\"consumption" . $i . "\" value=\"" . GetShipConsumption($i, user::get()) . "\" />\n";
		$ShipData .= "<input type=\"hidden\" name=\"speed" . $i . "\" value=\"" . GetFleetMaxSpeed("", $i, user::get()) . "\" />\n";
		$ShipData .= "<input type=\"hidden\" name=\"capacity" . $i . "\" value=\"";

		if (isset(user::get()->data['fleet_' . $i]) && isset($CombatCaps[$i]['power_consumption']) && $CombatCaps[$i]['power_consumption'] > 0)
			$ShipData .= round($CombatCaps[$i]['capacity'] * (1 + user::get()->data['fleet_' . $i] * ($CombatCaps[$i]['power_consumption'] / 100)));
		else
			$ShipData .= $CombatCaps[$i]['capacity'];

		$ShipData .= "\" />\n";

		$html .= "</th>\n";

		if ($i == 212)
		{
			$html .= "<th></th><th></th>\n";
		}
		else
		{
			$html .= "<th><a href=\"javascript:noShip('ship" . $i . "'); calc_capacity();\">min</a> / <a href=\"javascript:maxShip('ship" . $i . "'); calc_capacity();\">max</a></th>\n";
			$html .= "<th><a href=\"javascript:chShipCount('" . $i . "', '-1'); calc_capacity();\" title=\"Уменьшить на 1 ед.\" style=\"color:#FFD0D0\">- </a><input type=\"text\" name=\"ship" . $i . "\" size=\"10\" value=\"0\" onfocus=\"javascript:if(this.value == '0') this.value='';\" onblur=\"javascript:if(this.value == '') this.value='0';\" alt=\"" . _getText('tech', $i) . app::$planetrow->data[$resource[$i]] . "\" onChange=\"calc_capacity()\" onKeyUp=\"calc_capacity()\" /><a href=\"javascript:chShipCount('" . $i . "', '1'); calc_capacity();\" title=\"Увеличить на 1 ед.\" style=\"color:#D0FFD0\"> +</a></th>\n";
		}
		$html .= "</tr>\n";
	}
	$have_ships = true;
}

$btncontinue = "<tr height=\"20\"><th colspan=\"4\"><input type=\"submit\" value=\" " . _getText('fl_continue') . " \" /></th>\n";
$html .= "<tr height=\"20\">\n";
if (!$have_ships)
{
	$html .= "<th colspan=\"4\">" . _getText('fl_noships') . "</th>\n";
	$html .= "</tr>\n";
	$html .= $btncontinue;
}
else
{
	$html .= "<th colspan=\"2\"><a href=\"javascript:noShips(); calc_capacity();\" >" . _getText('fl_unselectall') . "</a></th>\n";
	$html .= "<th colspan=\"2\"><a href=\"javascript:maxShips(); calc_capacity();\" >" . _getText('fl_selectall') . "</a></th>\n";
	$html .= "</tr>\n";
	$html .= "<tr height=\"20\">\n";
	$html .= "	<th colspan=\"2\">-</th>\n";
	$html .= "	<th colspan=\"1\">Вместимость</th>\n";
	$html .= "	<th colspan=\"1\"><div id=\"allcapacity\">-</div></th>\n";
	$html .= "</tr>\n";
	$html .= "<tr height=\"20\">\n";
	$html .= "	<th colspan=\"2\">-</th>\n";
	$html .= "	<th colspan=\"1\">Скорость</th>\n";
	$html .= "	<th colspan=\"1\"><div id=\"allspeed\">-</div></th>\n";
	$html .= "</tr>\n";

	if ($MaxFlottes > $MaxFlyingFleets)
	{
		$html .= $btncontinue;
	}
}
$html .= "</tr>";
$html .= "</table>";
$html .= $ShipData;
$html .= "<input type=\"hidden\" name=\"galaxy\" value=\"" . $galaxy . "\" />";
$html .= "<input type=\"hidden\" name=\"system\" value=\"" . $system . "\" />";
$html .= "<input type=\"hidden\" name=\"planet\" value=\"" . $planet . "\" />";
$html .= "<input type=\"hidden\" name=\"planet_type\" value=\"" . $planettype . "\" />";
$html .= "<input type=\"hidden\" name=\"mission\" value=\"" . $target_mission . "\" />";
$html .= "<input type=\"hidden\" name=\"maxepedition\" value=\"" . $EnvoiMaxExpedition . "\" />";
$html .= "<input type=\"hidden\" name=\"curepedition\" value=\"" . $ExpeditionEnCours . "\" />";
$html .= "<input type=\"hidden\" name=\"target_mission\" value=\"" . $target_mission . "\" />";
$html .= "<input type=\"hidden\" name=\"crc\" value=\"" . md5(user::get()->data['id'] . '-CHeAT_CoNTROL_Stage_01-' . date("dmY", time())) . "\" />";
$html .= "</form>";

$html .= "</center>";

$this->display($html, _getText('fl_title'));

?>