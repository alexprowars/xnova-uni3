<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * @var $this pageHelper
 * @var $user user
 * @var $resource array
 * @var $reslist array
 * @var $CombatCaps array
 * @var app::$planetrow planet
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['urlaubs_modus_time'] > 0)
	$this->message("Нет доступа!");

if (!isset($_POST['crc']) || ($_POST['crc'] != md5(user::get()->data['id'] . '-CHeAT_CoNTROL_Stage_02-' . date("dmY", time()) . '-' . $_POST["usedfleet"])))
	$this->message('Ошибка контрольной суммы!');

strings::includeLang('fleet');

$parse = array();

$galaxy 	= request::P('galaxy', 0, VALUE_INT);
$system 	= request::P('system', 0, VALUE_INT);
$planet		= request::P('planet', 0, VALUE_INT);
$type 		= request::P('planettype', 0, VALUE_INT);
$acs 		= request::P('acs', 0, VALUE_INT);

$fleetmission 	= request::P('target_mission', 0, VALUE_INT);
$fleetarray 	= json_decode(base64_decode(str_rot13(request::P('usedfleet', ''))), true);

$YourPlanet = false;
$UsedPlanet = false;

$TargetPlanet = db::query("SELECT * FROM game_planets WHERE `galaxy` = '" . $galaxy . "' AND `system` = '" . $system . "' AND `planet` = '" . $planet . "' AND `planet_type` = '" . $type . "'", true);

if ($galaxy == $TargetPlanet['galaxy'] && $system == $TargetPlanet['system'] && $planet == $TargetPlanet['planet'] && $type == $TargetPlanet['planet_type'])
{
	if ($TargetPlanet['id_owner'] == user::get()->data['id'] || (user::get()->data['ally_id'] > 0 && $TargetPlanet['id_ally'] == user::get()->data['ally_id']))
	{
		$YourPlanet = true;
		$UsedPlanet = true;
	}
	else
		$UsedPlanet = true;
}

$missiontype = getFleetMissions($fleetarray, Array($galaxy, $system, $planet, $type), $YourPlanet, $UsedPlanet, ($acs > 0));

if ($TargetPlanet['id_owner'] == 1 || user::get()->isAdmin())
	$missiontype[4] = _getText('type_mission', 4);

$SpeedFactor = GetGameSpeedFactor();
$AllFleetSpeed = GetFleetMaxSpeed($fleetarray, 0, user::get());
$GenFleetSpeed = intval($_POST['speed']);
$MaxFleetSpeed = min($AllFleetSpeed);

$distance = GetTargetDistance(app::$planetrow->data['galaxy'], $_POST['galaxy'], app::$planetrow->data['system'], $_POST['system'], app::$planetrow->data['planet'], $_POST['planet']);
$duration = GetMissionDuration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
$consumption = GetFleetConsumption($fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, user::get());

$stayConsumption = GetFleetStay($fleetarray);

if (user::get()->data['rpg_meta'] > time())
	$stayConsumption = ceil($stayConsumption * 0.9);

$MissionSelector = "";

if (count($missiontype) > 0)
{
	$i = 0;

	foreach ($missiontype as $a => $b)
	{
		$MissionSelector .= "<tr height=\"20\">";
		$MissionSelector .= "<th style=\"text-align: left !important\">";
		$MissionSelector .= "<input id=\"m_" . $a . "\" type=\"radio\" name=\"mission\" value=\"" . $a . "\"" . ((($fleetmission > 0 && $fleetmission == $a) || (!isset($missiontype[$fleetmission]) && $i == 0) || count($missiontype) == 1) ? " checked=\"checked\"" : "") . ">";
		$MissionSelector .= "<label for=\"m_" . $a . "\">" . $b . "</label>";

		if ($a == 15)
			$MissionSelector .= "<center><font color=\"red\">" . _getText('fl_expe_warning') . "</font></center>";

		$MissionSelector .= "</th>";
		$MissionSelector .= "</tr>";

		$i++;
	}
}
else
	$MissionSelector .= "<tr height=\"20\"><th><font color=\"red\">" . _getText('fl_bad_mission') . "</font></th></tr>";

$TableTitle = "" . $_POST['galaxy'] . ":" . $_POST['system'] . ":" . $_POST['planet'] . " - "._getText('type_planet', $_POST["planettype"]);

$html = "<script type=\"text/javascript\" src='/scripts/flotten.js'></script>";
$html .= "<form action=\"?set=fleet&page=fleet_3\" method=\"post\">\n";
$html .= "<input type=\"hidden\" name=\"thisresource1\"  value=\"" . floor(app::$planetrow->data["metal"]) . "\" />\n";
$html .= "<input type=\"hidden\" name=\"thisresource2\"  value=\"" . floor(app::$planetrow->data["crystal"]) . "\" />\n";
$html .= "<input type=\"hidden\" name=\"thisresource3\"  value=\"" . floor(app::$planetrow->data["deuterium"]) . "\" />\n";
$html .= "<input type=\"hidden\" name=\"consumption\"    value=\"" . $consumption . "\" />\n";
$html .= "<input type=\"hidden\" name=\"stayConsumption\" value=\"" . $stayConsumption . "\" />\n";
$html .= "<input type=\"hidden\" name=\"dist\"           value=\"" . $distance . "\" />\n";
$html .= "<input type=\"hidden\" name=\"acs\"            value=\"" . $acs . "\" />\n";
$html .= "<input type=\"hidden\" name=\"thisgalaxy\"     value=\"" . app::$planetrow->data['galaxy'] . "\" />";
$html .= "<input type=\"hidden\" name=\"thissystem\"     value=\"" . app::$planetrow->data['system'] . "\" />";
$html .= "<input type=\"hidden\" name=\"thisplanet\"     value=\"" . app::$planetrow->data['planet'] . "\" />";
$html .= "<input type=\"hidden\" name=\"galaxy\"         value=\"" . $_POST["galaxy"] . "\" />\n";
$html .= "<input type=\"hidden\" name=\"system\"         value=\"" . $_POST["system"] . "\" />\n";
$html .= "<input type=\"hidden\" name=\"planet\"         value=\"" . $_POST["planet"] . "\" />\n";
$html .= "<input type=\"hidden\" name=\"planettype\"     value=\"" . $_POST["planettype"] . "\" />\n";
$html .= "<input type=\"hidden\" name=\"speed\"          value=\"" . $_POST['speed'] . "\" />\n";
$html .= "<input type=\"hidden\" name=\"speedfactor\"    value=\"" . $_POST["speedfactor"] . "\" />\n";
$html .= "<input type=\"hidden\" name=\"usedfleet\"      value=\"" . $_POST["usedfleet"] . "\" />\n";
$html .= "<input type=\"hidden\" name=\"crc\"            value=\"" . md5(user::get()->data['id'] . '-CHeAT_CoNTROL_Stage_03-' . date("dmY", time()) . '-' . $_POST["usedfleet"]) . "\" />\n";
$html .= "<input type=\"hidden\" name=\"maxepedition\"   value=\"" . $_POST['maxepedition'] . "\" />\n";
$html .= "<input type=\"hidden\" name=\"curepedition\"   value=\"" . $_POST['curepedition'] . "\" />\n";

foreach ($fleetarray as $Ship => $Count)
{
	$html .= "<input type=\"hidden\" name=\"ship" . $Ship . "\"        value=\"" . $Count . "\" />\n";
	$html .= "<input type=\"hidden\" name=\"stay" . $Ship . "\"        value=\"" . $CombatCaps[$Ship]['stay'] . "\" />\n";
	$html .= "<input type=\"hidden\" name=\"capacity" . $Ship . "\"    value=\"";

	if (isset(user::get()->data['fleet_' . $Ship]) && isset($CombatCaps[$Ship]['power_consumption']) && $CombatCaps[$Ship]['power_consumption'] > 0)
		$html .= round($CombatCaps[$Ship]['capacity'] * (1 + user::get()->data['fleet_' . $Ship] * ($CombatCaps[$Ship]['power_consumption'] / 100)));
	else
		$html .= $CombatCaps[$Ship]['capacity'];

	$html .= "\" />\n<input type=\"hidden\" name=\"consumption" . $Ship . "\" value=\"" . GetShipConsumption($Ship, user::get()) . "\" />\n";
	$html .= "<input type=\"hidden\" name=\"speed" . $Ship . "\"       value=\"" . GetFleetMaxSpeed("", $Ship, user::get()) . "\" />\n";
}

$html .= "<table class='table'>\n";
$html .= "<tr align=\"left\" height=\"20\">\n";
$html .= "<td class=\"c\" colspan=\"2\">" . $TableTitle . "</td>\n";
$html .= "</tr>\n";
$html .= "<tr align=\"left\" valign=\"top\">\n";
$html .= "<th width=\"50%\">\n";
$html .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
$html .= "<tr height=\"20\">\n";
$html .= "<td class=\"c\" colspan=\"2\">" . _getText('fl_mission') . "</td>\n";
$html .= "</tr>\n";
$html .= $MissionSelector;
$html .= "<tr><th>Время прилёта: <span id='end_time'>00:00:00</span></th></tr>";
$html .= "</table>\n";
$html .= "</th>\n";
$html .= "<th>\n";
$html .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
$html .= "<tr height=\"20\">\n";
$html .= "<td colspan=\"3\" class=\"c\">" . _getText('fl_ressources') . "</td>\n";
$html .= "</tr><tr height=\"20\">\n";
$html .= "<th>" . _getText('Metal') . "</th>\n";
$html .= "<th><a href=\"javascript:maxResource('1');\">" . _getText('fl_selmax') . "</a></th>\n";
$html .= "<th><input name=\"resource1\" alt=\"" . _getText('Metal') . " " . floor(app::$planetrow->data["metal"]) . "\" size=\"10\" onchange=\"calculateTransportCapacity();\" type=\"text\"></th>\n";
$html .= "</tr><tr height=\"20\">\n";
$html .= "<th>" . _getText('Crystal') . "</th>\n";
$html .= "<th><a href=\"javascript:maxResource('2');\">" . _getText('fl_selmax') . "</a></th>\n";
$html .= "<th><input name=\"resource2\" alt=\"" . _getText('Crystal') . " " . floor(app::$planetrow->data["crystal"]) . "\" size=\"10\" onchange=\"calculateTransportCapacity();\" type=\"text\"></th>\n";
$html .= "</tr><tr height=\"20\">\n";
$html .= "<th>" . _getText('Deuterium') . "</th>\n";
$html .= "<th><a href=\"javascript:maxResource('3');\">" . _getText('fl_selmax') . "</a></th>\n";
$html .= "<th><input name=\"resource3\" alt=\"" . _getText('Deuterium') . " " . floor(app::$planetrow->data["deuterium"]) . "\" size=\"10\" onchange=\"calculateTransportCapacity();\" type=\"text\"></th>\n";
$html .= "</tr><tr height=\"20\">\n";
$html .= "<th>" . _getText('fl_space_left') . "</th>\n";
$html .= "<th colspan=\"2\"><div id=\"remainingresources\">-</div></th>\n";
$html .= "</tr><tr height=\"20\">\n";
$html .= "<th colspan=\"3\"><a href=\"javascript:maxResources()\">" . _getText('fl_allressources') . "</a></th>\n";
$html .= "</tr><tr height=\"20\">\n";
$html .= "<th colspan=\"3\">&nbsp;</th>\n";
$html .= "</tr>\n";

if (isset($missiontype[15]))
{
	$html .= "<tr height=\"20\" class=\"mission m_15\"><td class=\"c\" colspan=\"3\">Время экспедиции</td></tr>";
	$html .= "<tr height=\"20\" class=\"mission m_15\">";
	$html .= "<th colspan=\"3\">";
	$html .= "<select name=\"expeditiontime\" >";
	for ($i = 1; $i <= round(user::get()->data[$resource[124]] / 2) + 1; $i++)
	{
		$html .= "<option value=\"" . $i . "\">" . $i . " ч.</option>";
	}
	$html .= "</select></th></tr>";
}
elseif (isset($missiontype[5]))
{
	$html .= "<tr height=\"20\" class=\"mission m_5\">";
	$html .= "<td class=\"c\" colspan=\"3\">Оставаться часов на орбите</td>";
	$html .= "</tr>";
	$html .= "<tr height=\"20\" class=\"mission m_5\">";
	$html .= "<th colspan=\"3\">";
	$html .= "<select name=\"holdingtime\" >";
	$html .= "<option value=\"0\">0</option>";
	$html .= "<option value=\"1\">1</option>";
	$html .= "<option value=\"2\">2</option>";
	$html .= "<option value=\"4\">4</option>";
	$html .= "<option value=\"8\">8</option>";
	$html .= "<option value=\"16\">16</option>";
	$html .= "<option value=\"32\">32</option>";
	$html .= "</select><div id=\"stayRes\"></div>";
	$html .= "</th>";
	$html .= "</tr>";
}

if (isset($missiontype[1]))
{
	$html .= "<tr height=\"20\" class=\"mission m_1\"><td class=\"c\" colspan=\"3\">Кол-во раундов боя</td></tr>";
	$html .= "<tr height=\"20\" class=\"mission m_1\">";
	$html .= "<th colspan=\"3\">";
	$html .= "<select name=\"raunds\" >";
	$html .= "<option value=\"6\" selected>6</option>";
	$html .= "<option value=\"7\">7</option>";
	$html .= "<option value=\"8\">8</option>";
	$html .= "<option value=\"9\">9</option>";
	$html .= "<option value=\"10\">10</option>";
	$html .= "</select></th></tr>";
}

$html .= "</table>\n";
$html .= "</th>\n";

if (count($missiontype) > 0)
{
	$html .= "</tr><tr height=\"20\">\n";
	$html .= "<th colspan=\"2\"><input accesskey=\"z\" value=\"" . _getText('fl_continue') . "\" type=\"submit\"></th>\n";
	$html .= "</tr>\n";
}

$html .= "</table></form>";

$this->setTemplate('fleet/stage_2');
$this->set('parse', $parse);

$this->display($html, _getText('fl_title'));

?>