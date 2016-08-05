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

if (!isset($_POST['crc']) || ($_POST['crc'] != md5(user::get()->data['id'] . '-CHeAT_CoNTROL_Stage_01-' . date("dmY", time()))))
	$this->message('Ошибка контрольной суммы!');

strings::includeLang('fleet');

$parse = array();

$speed = array(
	10 => 100,
	9 => 90,
	8 => 80,
	7 => 70,
	6 => 60,
	5 => 50,
	4 => 40,
	3 => 30,
	2 => 20,
	1 => 10,
);

$g = request::P('galaxy', 0, VALUE_INT);
$s = request::P('system', 0, VALUE_INT);
$p = request::P('planet', 0, VALUE_INT);
$t = request::P('planet_type', 0, VALUE_INT);

if (!$g)
	$g = app::$planetrow->data['galaxy'];

if (!$s)
	$s = app::$planetrow->data['system'];

if (!$p)
	$p = app::$planetrow->data['planet'];

if (!$t)
	$t = 1;

$FleetHiddenBlock = "";
$fleet['fleetlist'] = "";
$fleet['amount'] = 0;

foreach ($reslist['fleet'] as $n => $i)
{
	if (isset($_POST["ship" . $i]) && in_array($i, $reslist['fleet']) && intval($_POST["ship" . $i]) > 0)
	{
		if (intval($_POST["ship" . $i]) > app::$planetrow->data[$resource[$i]])
			$speedalls[$i] = GetFleetMaxSpeed("", $i, user::get());
		else
		{
			$fleet['fleetarray'][$i] = intval($_POST["ship" . $i]);
			$fleet['fleetlist'] .= $i . "," . intval($_POST["ship" . $i]) . ";";
			$fleet['amount'] += intval($_POST["ship" . $i]);
			$FleetHiddenBlock .= "<input type=\"hidden\" name=\"consumption" . $i . "\" value=\"" . GetShipConsumption($i, user::get()) . "\" />";
			$FleetHiddenBlock .= "<input type=\"hidden\" name=\"speed" . $i . "\"       value=\"" . GetFleetMaxSpeed("", $i, user::get()) . "\" />";
			$FleetHiddenBlock .= "<input type=\"hidden\" name=\"capacity" . $i . "\"    value=\"";

			if (isset(user::get()->data['fleet_' . $i]) && isset($CombatCaps[$i]['power_consumption']) && $CombatCaps[$i]['power_consumption'] > 0)
				$FleetHiddenBlock .= round($CombatCaps[$i]['capacity'] * (1 + user::get()->data['fleet_' . $i] * ($CombatCaps[$i]['power_consumption'] / 100)));
			else
				$FleetHiddenBlock .= $CombatCaps[$i]['capacity'];

			$FleetHiddenBlock .= "\" />";
			$FleetHiddenBlock .= "<input type=\"hidden\" name=\"ship" . $i . "\"        value=\"" . intval($_POST["ship" . $i]) . "\" />";
			$speedalls[$i] = GetFleetMaxSpeed("", $i, user::get());
		}
	}
}

if (!$fleet['fleetlist'])
	$this->message(_getText('fl_unselectall'), _getText('fl_error'), "?set=fleet", 1);

$html = "<script type=\"text/javascript\" src='/scripts/flotten.js'></script>";
$html .= "<form action=\"/?set=fleet&page=fleet_2\" method=\"post\">";
$html .= $FleetHiddenBlock;
$html .= "<input type=\"hidden\" name=\"usedfleet\"      value=\"" . str_rot13(base64_encode(json_encode($fleet['fleetarray']))) . "\" />";
$html .= "<input type=\"hidden\" name=\"thisgalaxy\"     value=\"" . app::$planetrow->data['galaxy'] . "\" />";
$html .= "<input type=\"hidden\" name=\"thissystem\"     value=\"" . app::$planetrow->data['system'] . "\" />";
$html .= "<input type=\"hidden\" name=\"thisplanet\"     value=\"" . app::$planetrow->data['planet'] . "\" />";
$html .= "<input type=\"hidden\" name=\"galaxyend\"      value=\"" . intval($_POST['galaxy']) . "\" />";
$html .= "<input type=\"hidden\" name=\"systemend\"      value=\"" . intval($_POST['system']) . "\" />";
$html .= "<input type=\"hidden\" name=\"planetend\"      value=\"" . intval($_POST['planet']) . "\" />";
$html .= "<input type=\"hidden\" name=\"speedfactor\"    value=\"" . GetGameSpeedFactor() . "\" />";
$html .= "<input type=\"hidden\" name=\"thisresource1\"  value=\"" . floor(app::$planetrow->data['metal']) . "\" />";
$html .= "<input type=\"hidden\" name=\"thisresource2\"  value=\"" . floor(app::$planetrow->data['crystal']) . "\" />";
$html .= "<input type=\"hidden\" name=\"thisresource3\"  value=\"" . floor(app::$planetrow->data['deuterium']) . "\" />";

$html .= "<br><div><center>";
$html .= "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">";
$html .= "<tr height=\"20\">";
$html .= "<td colspan=\"2\" class=\"c\">" . _getText('fl_floten1_ttl') . "</td>";
$html .= "</tr>";
$html .= "<tr height=\"20\">";
$html .= "<th width=\"50%\">" . _getText('fl_dest') . "</th>";
$html .= "<th>";
$html .= "<input type=\"text\" name=\"galaxy\" size=\"3\" maxlength=\"2\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\" value=\"" . $g . "\" />";
$html .= "<input type=\"text\" name=\"system\" size=\"3\" maxlength=\"3\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\" value=\"" . $s . "\" />";
$html .= "<input type=\"text\" name=\"planet\" size=\"3\" maxlength=\"2\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\" value=\"" . $p . "\" />";
$html .= "<select name=\"planettype\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\">";
$html .= "<option value=\"1\"" . (($t == 1) ? " SELECTED" : "") . ">" . _getText('fl_planet') . " </option>";
$html .= "<option value=\"2\"" . (($t == 2) ? " SELECTED" : "") . ">" . _getText('fl_ruins') . " </option>";
$html .= "<option value=\"3\"" . (($t == 3) ? " SELECTED" : "") . ">" . _getText('fl_moon') . " </option>";
$html .= "<option value=\"5\"" . (($t == 5) ? " SELECTED" : "") . ">" . _getText('fl_base') . " </option>";
$html .= "</select>";
$html .= "</th>";
$html .= "</tr>";
$html .= "<tr height=\"20\">";
$html .= "<th>" . _getText('fl_speed') . "</th>";
$html .= "<th>";
$html .= "<select name=\"speed\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\">";
foreach ($speed as $a => $b)
{
	$html .= "<option value=\"" . $a . "\">" . $b . "</option>";
}
$html .= "</select> %";
$html .= "</th>";
$html .= "</tr>";

$html .= "<tr height=\"20\">";
$html .= "<th>" . _getText('fl_dist') . "</th>";
$html .= "<th><div id=\"distance\">-</div></th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_fltime') . "</th>";
$html .= "<th><div id=\"duration\">-</div></th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_time_go') . "</th>";
$html .= "<th><div id=\"end_time\">-</div></th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_deute_need') . "</th>";
$html .= "<th><div id=\"consumption\">-</div></th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_speed_max') . "</th>";
$html .= "<th><div id=\"maxspeed\">-</div></th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_max_load') . "</th>";
$html .= "<th><div id=\"storage\">-</div></th>";
$html .= "</tr>";


$html .= "<tr height=\"20\">";
$html .= "<td colspan=\"2\" class=\"c\">" . _getText('fl_shortcut') . " <a href=\"?set=fleet&page=shortcut\">" . _getText('fl_shortlnk') . "</a></td>";
$html .= "</tr>";

$inf = db::query("SELECT fleet_shortcut FROM game_users_inf WHERE id = " . user::get()->data['id'] . ";", true);

if ($inf['fleet_shortcut'])
{
	$scarray = explode("\r\n", $inf['fleet_shortcut']);
	$i = 0;
	foreach ($scarray as $a => $b)
	{
		if ($b != "")
		{
			$c = explode(',', $b);

			if ($i == 0)
				$html .= "<tr height=\"20\">";

			$html .= "<th><a href=\"javascript:setTarget(" . $c[1] . "," . $c[2] . "," . $c[3] . "," . $c[4] . "); shortInfo();\"";
			$html .= ">" . $c[0] . " " . $c[1] . ":" . $c[2] . ":" . $c[3] . " ";

			if ($c[4] == 1)
				$html .= _getText('fl_shrtcup1');
			elseif ($c[4] == 2)
				$html .= _getText('fl_shrtcup2');
			elseif ($c[4] == 3)
				$html .= _getText('fl_shrtcup3');

			$html .= "</a></th>";

			if ($i == 1)
				$html .= "</tr>";
			if ($i == 1)
				$i = 0;
			else
				$i = 1;
		}
	}
	if ($i == 1)
		$html .= "<th></th></tr>";
}

$kolonien = user::get()->getUserPlanets(user::get()->getId(), true, user::get()->data['ally_id']);

if (count($kolonien) > 1)
{
	$html .= "<tr height=\"20\"><td colspan=\"2\" class=\"c\">" . _getText('fl_myplanets') . "</td></tr>";

	$i = 0;
	$w = 0;
	$tr = true;

	foreach ($kolonien AS $row)
	{
		if ($row['id'] == app::$planetrow->data['id'])
			continue;

		if ($w == 0 && $tr)
		{
			$html .= "<tr height=\"20\">";
			$tr = false;
		}
		if ($w == 2)
		{
			$html .= "</tr>";
			$w = 0;
			$tr = true;
		}

		if ($row['planet_type'] == 3)
		{
			$row['name'] .= " " . _getText('fl_shrtcup3');
		}

		$html .= "<th><a href=\"javascript:setTarget(" . $row['galaxy'] . "," . $row['system'] . "," . $row['planet'] . "," . $row['planet_type'] . "); shortInfo();\">" . $row['name'] . " " . $row['galaxy'] . ":" . $row['system'] . ":" . $row['planet'] . "</a></th>";
		$w++;
		$i++;
	}

	if ($i % 2 != 0)
	{
		$html .= "<th>&nbsp;</th></tr>";
	}
	elseif ($w == 2)
	{
		$html .= "</tr>";
	}
}

$aks_madnessred = db::query("SELECT a.* FROM game_aks a, game_aks_user au WHERE au.aks_id = a.id AND au.user_id = " . user::get()->data['id'] . " ;", '');

if (db::num_rows($aks_madnessred))
{
	$html .= "</tr>";
	$html .= "<tr height=\"20\">";
	$html .= "<td colspan=\"2\" class=\"c\">" . _getText('fl_grattack') . "</td>";
	$html .= "</tr>";

	while ($row = db::fetch($aks_madnessred))
	{
		$html .= "<tr height=\"20\">";
		$html .= "<th colspan=\"2\">";
		$html .= "<a href=\"javascript:";
		$html .= "setTarget(" . $row['galaxy'] . "," . $row['system'] . "," . $row['planet'] . "," . $row['planet_type'] . "); ";
		$html .= "shortInfo(); ACS(" . $row['id'] . ");";
		$html .= "\">";
		$html .= "(" . $row['name'] . ")";
		$html .= "</a>";
		$html .= "</th>";
		$html .= "</tr>";
	}
}

$html .= "<tr height=\"20\">";
$html .= "<th colspan=\"2\"><input type=\"submit\" value=\"" . _getText('fl_continue') . "\" /></th>";
$html .= "</tr>";
$html .= "</table>";
$html .= "</div></center>";
$html .= "<input type=\"hidden\" name=\"acs\" value=\"0\" />";
$html .= "<input type=\"hidden\" name=\"maxepedition\" value=\"" . intval($_POST['maxepedition']) . "\" />";
$html .= "<input type=\"hidden\" name=\"curepedition\" value=\"" . intval($_POST['curepedition']) . "\" />";
$html .= "<input type=\"hidden\" name=\"target_mission\" value=\"" . intval($_POST['target_mission']) . "\" />";
$html .= "<input type=\"hidden\" name=\"crc\" value=\"" . md5(user::get()->data['id'] . '-CHeAT_CoNTROL_Stage_02-' . date("dmY", time()) . '-' . str_rot13(base64_encode(json_encode($fleet['fleetarray'])))) . "\" />";
$html .= "</form>";

$this->setTemplate('fleet/stage_1');
$this->set('parse', $parse);

$this->display($html, _getText('fl_title'));

?>