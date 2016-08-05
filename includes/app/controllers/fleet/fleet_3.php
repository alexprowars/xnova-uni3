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
 * @var core::getConfig array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['urlaubs_modus_time'] > 0)
{
	$this->message("Нет доступа!");
}

if (!isset($_POST['crc']) || $_POST['crc'] != md5(user::get()->data['id'] . '-CHeAT_CoNTROL_Stage_03-' . date("dmY", time()) . '-' . $_POST["usedfleet"]))
	$this->message('Ошибка контрольной суммы!');

strings::includeLang('fleet');

$error = 0;
$galaxy = intval($_POST['galaxy']);
$system = intval($_POST['system']);
$planet = intval($_POST['planet']);
$planettype = intval($_POST['planettype']);
$fleetmission = intval($_POST['mission']);

$fleetarray = json_decode(base64_decode(str_rot13($_POST["usedfleet"])), true);

if (!$fleetmission)
	$this->message("<font color=\"red\"><b>Не выбрана миссия!</b></font>", 'Ошибка', "?set=fleet", 2);

if (($fleetmission == 1 || $fleetmission == 6 || $fleetmission == 9 || $fleetmission == 2) && core::getConfig('disableAttacks', 0) > 0 && time() > 1388476800 && time() < core::getConfig('disableAttacks', 0))
	$this->message("<font color=\"red\"><b>Посылать флот в атаку временно запрещено.<br>Дата включения атак " . datezone("d.m.Y H ч. i мин.", core::getConfig('disableAttacks', 0)) . "</b></font>", 'Ошибка');

$fleet_group_mr = 0;

if ($_POST['acs'] > 0)
{
	if ($fleetmission == 2)
	{
		$aks_count_mr = db::query("SELECT a.* FROM game_aks a, game_aks_user au WHERE au.aks_id = a.id AND au.user_id = " . user::get()->data['id'] . " AND au.aks_id = " . intval($_POST['acs']) . "");

		if (db::num_rows($aks_count_mr) > 0)
		{
			$aks_tr = db::fetch($aks_count_mr);

			if ($aks_tr['galaxy'] == $_POST["galaxy"] && $aks_tr['system'] == $_POST["system"] && $aks_tr['planet'] == $_POST["planet"] && $aks_tr['planet_type'] == $_POST["planettype"])
			{
				$fleet_group_mr = $_POST['acs'];
			}
		}
	}
}
if (($_POST['acs'] == 0 || $fleet_group_mr == 0) && ($fleetmission == 2))
{
	$fleetmission = 1;
}

$protection = core::getConfig('noobprotection');
$protectiontime = core::getConfig('noobprotectiontime');
$protectionmulti = core::getConfig('noobprotectionmulti');
if ($protectiontime < 1)
{
	$protectiontime = 9999999999999999;
}

if (!is_array($fleetarray))
{
	$this->message("<font color=\"red\"><b>Ошибка в передаче параметров!</b></font>", 'Ошибка', "?set=fleet", 2);
}

foreach ($fleetarray as $Ship => $Count)
{
	if ($Count > app::$planetrow->data[$resource[$Ship]])
	{
		$this->message("<font color=\"red\"><b>Недостаточно флота для отправки на планете!</b></font>", 'Ошибка', "?set=fleet", 2);
	}
}

if ($planettype != 1 && $planettype != 2 && $planettype != 3 && $planettype != 5)
{
	$this->message("<font color=\"red\"><b>Неизвестный тип планеты!</b></font>", 'Ошибка', "?set=fleet", 2);
}
if (app::$planetrow->data['galaxy'] == $galaxy && app::$planetrow->data['system'] == $system && app::$planetrow->data['planet'] == $planet && app::$planetrow->data['planet_type'] == $planettype)
{
	$this->message("<font color=\"red\"><b>Невозможно отправить флот на эту же планету!</b></font>", 'Ошибка', "?set=fleet", 2);
}

if ($fleetmission == 8)
{
	$YourPlanet = false;
	$UsedPlanet = false;
	$select = db::query("SELECT * FROM game_planets WHERE galaxy = '" . $galaxy . "' AND system = '" . $system . "' AND planet = '" . $planet . "' AND (planet_type = 1 OR planet_type = 5)");
}
else
{
	$YourPlanet = false;
	$UsedPlanet = false;
	$select = db::query("SELECT * FROM game_planets WHERE galaxy = '" . $galaxy . "' AND system = '" . $system . "' AND planet = '" . $planet . "' AND planet_type = '" . $planettype . "'");
}

if ($fleetmission != 15)
{
	if (db::num_rows($select) == 0 && $fleetmission != 7 && $fleetmission != 10)
	{
		$this->message("<font color=\"red\"><b>Данной планеты не существует!</b> - [".$galaxy.":".$system.":".$planet."]</font>", 'Ошибка #1', "?set=fleet", 20);
	}
	elseif ($fleetmission == 9 && db::num_rows($select) == 0)
	{
		$this->message("<font color=\"red\"><b>Данной планеты не существует!</b> - [".$galaxy.":".$system.":".$planet."]</font>", 'Ошибка #2', "?set=fleet", 20);
	}
	elseif (db::num_rows($select) == 0 && $fleetmission == 7 && $planettype != 1)
	{
		$this->message("<font color=\"red\"><b>Колонизировать можно только планету!</b></font>", 'Ошибка', "?set=fleet", 2);
	}
}
else
{
	if (user::get()->data[$resource[124]] >= 1)
	{
		$maxexp = db::query("SELECT COUNT(*) AS `expeditions` FROM game_fleets WHERE `fleet_owner` = '" . user::get()->data['id'] . "' AND `fleet_mission` = '15';", true);

		$ExpeditionEnCours = $maxexp['expeditions'];
		$MaxExpedition = 1 + floor(user::get()->data[$resource[124]] / 3);
	}
	else
	{
		$MaxExpedition = 0;
		$ExpeditionEnCours = 0;
	}

	if (user::get()->data[$resource[124]] == 0)
		$this->message("<font color=\"red\"><b>Вами не изучена \"Экспедиционная технология\"!</b></font>", 'Ошибка', "?set=fleet", 2);
	elseif ($ExpeditionEnCours >= $MaxExpedition)
		$this->message("<font color=\"red\"><b>Вы уже отправили максимальное количество экспедиций!</b></font>", 'Ошибка', "?set=fleet", 2);

	if (intval($_POST['expeditiontime']) <= 0 || intval($_POST['expeditiontime']) > (round(user::get()->data[$resource[124]] / 2) + 1))
		$this->message("<font color=\"red\"><b>Вы не можете столько времени летать в экспедиции!</b></font>", 'Ошибка', "?set=fleet", 2);
}

$TargetPlanet = db::fetch_assoc($select);

if ($TargetPlanet['id_owner'] == user::get()->data['id'] || (user::get()->data['ally_id'] > 0 && $TargetPlanet['id_ally'] == user::get()->data['ally_id']))
{
	$YourPlanet = true;
	$UsedPlanet = true;
}
elseif (!empty($TargetPlanet['id_owner']))
{
	$YourPlanet = false;
	$UsedPlanet = true;
}
else
{
	$YourPlanet = false;
	$UsedPlanet = false;
}

if ($fleetmission == 4 && ($TargetPlanet['id_owner'] == 1 || user::get()->isAdmin()))
	$YourPlanet = true;

$missiontype = getFleetMissions($fleetarray, Array($galaxy, $system, $planet, $planettype), $YourPlanet, $UsedPlanet, ($fleet_group_mr > 0));

if (!isset($missiontype[$fleetmission]))
	$this->message("<font color=\"red\"><b>Миссия неизвестна!</b></font>", 'Ошибка', "?set=fleet", 2);

if ($fleetmission == 8 && $TargetPlanet['debris_metal'] == 0 && $TargetPlanet['debris_crystal'] == 0)
{
	if ($TargetPlanet['debris_metal'] == 0 && $TargetPlanet['debris_crystal'] == 0)
		$this->message("<font color=\"red\"><b>Нет обломков для сбора.</b></font>", 'Ошибка', "?set=fleet", 2);
}

if (isset($TargetPlanet['id_owner']))
{
	$HeDBRec = db::query("SELECT * FROM game_users WHERE `id` = '" . $TargetPlanet['id_owner'] . "';", true);

	if (!isset($HeDBRec['id']))
		$this->message("<font color=\"red\"><b>Неизвестная ошибка #FLTNFU".$TargetPlanet['id_owner']."</b></font>", 'Ошибка', "?set=fleet", 2);
}
else
	$HeDBRec = user::get()->data;

if (($HeDBRec['id'] == 1 && user::get()->data['id'] != 1) && ($fleetmission != 4 && $fleetmission != 3))
	$this->message("<font color=\"red\"><b>На этого игрока запрещено нападать</b></font>", 'Ошибка', "?set=fleet", 2);

if (user::get()->data['ally_id'] != 0 && $HeDBRec['ally_id'] != 0 && $fleetmission == 1)
{
	$ad = db::query("SELECT * FROM game_alliance_diplomacy WHERE (a_id = " . $HeDBRec['ally_id'] . " AND d_id = " . user::get()->data['ally_id'] . ") AND status = 1", true);

	if ($ad['id'] != "" && $ad['type'] < 3)
		$this->message("<font color=\"red\"><b>Заключён мир или перемирие с альянсом атакуемого игрока.</b></font>", "Ошибка дипломатии", "?set=fleet", 2);

}

$VacationMode = $HeDBRec['urlaubs_modus_time'];

if (user::get()->data['authlevel'] < 2)
{
	$MyGameLevel = db::first(db::query("SELECT total_points FROM game_statpoints WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . user::get()->data['id'] . "';", true));
	$HeGameLevel = db::first(db::query("SELECT total_points FROM game_statpoints WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . $HeDBRec['id'] . "';", true));

	if (!$HeGameLevel)
		$HeGameLevel = 0;

	if ($HeDBRec['onlinetime'] < (time() - 60 * 60 * 24 * 7) || $HeDBRec['banaday'] != 0)
		$NoobNoActive = 1;
	else
		$NoobNoActive = 0;

	if (isset($TargetPlanet['id_owner']) && ($fleetmission == 1 || $fleetmission == 2 || $fleetmission == 5 || $fleetmission == 6 || $fleetmission == 9) && $protection && !$NoobNoActive && $HeGameLevel < ($protectiontime * 1000))
	{
		if ($MyGameLevel > ($HeGameLevel * $protectionmulti))
			$this->message("<font color=\"lime\"><b>Игрок находится под защитой новичков!</b></font>", 'Защита новичков', "?set=fleet", 2);
		if (($MyGameLevel * $protectionmulti) < $HeGameLevel)
			$this->message("<font color=\"lime\"><b>Вы слишком слабы для нападения на этого игрока!</b></font>", 'Защита новичков', "?set=fleet", 2);
	}
}

if ($VacationMode && $fleetmission != 8)
	$this->message("<font color=\"lime\"><b>Игрок в режиме отпуска!</b></font>", 'Режим отпуска', "?set=fleet", 2);

$ActualFleets = db::first(db::query("SELECT COUNT(fleet_id) as Number FROM game_fleets WHERE `fleet_owner`='".user::get()->data['id']."'", true));

$fleetmax = user::get()->data[$resource[108]] + 1;

if (user::get()->data['rpg_admiral'] > time())
	$fleetmax += 2;

if ($fleetmax <= $ActualFleets)
	$this->message("Все слоты флота заняты. Изучите компьютерную технологию для увеличения кол-ва летящего флота.", "Ошибка", "?set=fleet", 2);

if (($_POST['resource1'] + $_POST['resource2'] + $_POST['resource3']) < 1 AND $fleetmission == 3)
	$this->message("<font color=\"lime\"><b>Нет сырья для транспорта!</b></font>", _getText('type_mission', 3), "?set=fleet", 2);

if ($fleetmission != 15)
{
	if (!isset($TargetPlanet['id_owner']) AND $fleetmission < 7)
		$this->message("<font color=\"red\"><b>Планеты не существует!</b></font>", 'Ошибка', "?set=fleet", 2);

	if (isset($TargetPlanet['id_owner']) AND ($fleetmission == 7 || $fleetmission == 10))
		$this->message("<font color=\"red\"><b>Место занято</b></font>", 'Ошибка', "?set=fleet", 2);

	if ($TargetPlanet['ally_deposit'] == 0 && $HeDBRec['id'] != user::get()->data['id'] && $fleetmission == 5)
		$this->message("<font color=\"red\"><b>На планете нет склада альянса!</b></font>", 'Ошибка', "?set=fleet", 2);

	if ($fleetmission == 5)
	{
		$friend = db::query("SELECT id FROM game_buddy WHERE (sender = " . user::get()->data['id'] . " AND owner = " . $HeDBRec['id'] . ") OR (owner = " . user::get()->data['id'] . " AND sender = " . $HeDBRec['id'] . ") AND active = 1 LIMIT 1", true);

		if ($HeDBRec['ally_id'] != user::get()->data['ally_id'] && !isset($friend['id']))
			$this->message("<font color=\"red\"><b>Нельзя охранять вражеские планеты!</b></font>", 'Ошибка', "?set=fleet", 2);
	}

	if ($TargetPlanet['id_owner'] == user::get()->data['id'] && $fleetmission == 1)
		$this->message("<font color=\"red\"><b>Невозможно атаковать самого себя!</b></font>", 'Ошибка', "?set=fleet", 2);

	if ($TargetPlanet['id_owner'] == user::get()->data['id'] && $fleetmission == 6)
		$this->message("<font color=\"red\"><b>Невозможно шпионить самого себя!</b></font>", 'Ошибка', "?set=fleet", 2);

	if (!$YourPlanet && $fleetmission == 4)
		$this->message("<font color=\"red\"><b>Выполнение данной миссии невозможно!</b></font>", 'Ошибка', "?set=fleet", 2);
}

$speed_possible = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);

$AllFleetSpeed = GetFleetMaxSpeed($fleetarray, 0, user::get());
$GenFleetSpeed = $_POST['speed'];
$SpeedFactor = GetGameSpeedFactor();
$MaxFleetSpeed = min($AllFleetSpeed);

if (!in_array($GenFleetSpeed, $speed_possible))
	$this->message("<font color=\"red\"><b>Читеришь со скоростью?</b></font>", 'Ошибка', "?set=fleet", 2);

if (!$planettype)
	$this->message("<font color=\"red\"><b>Ошибочный тип планеты!</b></font>", 'Ошибка', "?set=fleet", 2);

$error = 0;
$errorlist = "";
if (!$galaxy || $galaxy > MAX_GALAXY_IN_WORLD || $galaxy < 1)
{
	$error++;
	$errorlist .= _getText('fl_limit_galaxy');
}
if (!$system || $system > MAX_SYSTEM_IN_GALAXY || $system < 1)
{
	$error++;
	$errorlist .= _getText('fl_limit_system');
}
if (!$planet || $planet > (MAX_PLANET_IN_SYSTEM + 1) || $planet < 1)
{
	$error++;
	$errorlist .= _getText('fl_limit_planet');
}

if ($error > 0)
	$this->message("<font color=\"red\"><ul>" . $errorlist . "</ul></font>", 'Ошибка', "?set=fleet", 2);

if (!isset($fleetarray))
	$this->message("<font color=\"red\"><b>" . _getText('fl_no_fleetarray') . "</b></font>", 'Ошибка', "?set=fleet", 2);

$distance = GetTargetDistance(app::$planetrow->data['galaxy'], $galaxy, app::$planetrow->data['system'], $system, app::$planetrow->data['planet'], $planet);
$duration = GetMissionDuration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
$consumption = GetFleetConsumption($fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, user::get());

if ($fleet_group_mr > 0)
{
	// Вычисляем время самого медленного флота в совместной атаке
	$flet = db::query("SELECT fleet_id, fleet_start_time, fleet_end_time FROM game_fleets WHERE fleet_group = '" . $fleet_group_mr . "'");
	$ttt = $duration + time();
	$arrr = array();
	$i = 0;
	while ($flt = db::fetch_assoc($flet))
	{
		$i++;
		if ($flt['fleet_start_time'] > $ttt)
			$ttt = $flt['fleet_start_time'];
		$arrr[$i]['id'] = $flt['fleet_id'];
		$arrr[$i]['start'] = $flt['fleet_start_time'];
		$arrr[$i]['end'] = $flt['fleet_end_time'];
	}
}

if ($fleet_group_mr > 0)
	$fleet['start_time'] = $ttt;
else
	$fleet['start_time'] = $duration + time();

if ($fleetmission == 15)
{
	$StayDuration = intval($_POST['expeditiontime']) * 3600;
	$StayTime = $fleet['start_time'] + intval($_POST['expeditiontime']) * 3600;
}
else
{
	$StayDuration = 0;
	$StayTime = 0;
}

$FleetStorage = 0;
$FleetShipCount = 0;
$fleet_array = "";
$FleetSubQRY = "";

foreach ($fleetarray as $Ship => $Count)
{
	$Count = intval($Count);

	if (isset(user::get()->data['fleet_' . $Ship]) && isset($CombatCaps[$Ship]['power_consumption']) && $CombatCaps[$Ship]['power_consumption'] > 0)
		$FleetStorage += round($CombatCaps[$Ship]['capacity'] * (1 + user::get()->data['fleet_' . $Ship] * ($CombatCaps[$Ship]['power_consumption'] / 100))) * $Count;
	else
		$FleetStorage += $CombatCaps[$Ship]['capacity'] * $Count;

	$FleetShipCount += $Count;
	$fleet_array .= (isset(user::get()->data['fleet_' . $Ship])) ? $Ship . "," . $Count . "!" . user::get()->data['fleet_' . $Ship] . ";" : $Ship . "," . $Count . "!0;";
	$FleetSubQRY .= "`" . $resource[$Ship] . "` = `" . $resource[$Ship] . "` - " . $Count . " , ";
}

$FleetStorage -= $consumption;
$StorageNeeded = 0;

if ($_POST['resource1'] < 1)
{
	$TransMetal = 0;
}
else
{
	$TransMetal = intval($_POST['resource1']);
	$StorageNeeded += $TransMetal;
}
if ($_POST['resource2'] < 1)
{
	$TransCrystal = 0;
}
else
{
	$TransCrystal = intval($_POST['resource2']);
	$StorageNeeded += $TransCrystal;
}
if ($_POST['resource3'] < 1)
{
	$TransDeuterium = 0;
}
else
{
	$TransDeuterium = intval($_POST['resource3']);
	$StorageNeeded += $TransDeuterium;
}

$TotalFleetCons = 0;

if ($fleetmission == 5)
{
	$StayArrayTime = array(0, 1, 2, 4, 8, 16, 32);

	if (!isset($_POST['holdingtime']) || !in_array($_POST['holdingtime'], $StayArrayTime))
		$_POST['holdingtime'] = 0;

	$FleetStayConsumption = GetFleetStay($fleetarray);

	if (user::get()->data['rpg_meta'] > time())
		$FleetStayConsumption = ceil($FleetStayConsumption * 0.9);

	$FleetStayAll = $FleetStayConsumption * intval($_POST['holdingtime']);

	if ($FleetStayAll >= (app::$planetrow->data['deuterium'] - $TransDeuterium))
		$TotalFleetCons = app::$planetrow->data['deuterium'] - $TransDeuterium;
	else
		$TotalFleetCons = $FleetStayAll;

	if ($FleetStorage < $TotalFleetCons)
		$TotalFleetCons = $FleetStorage;

	$FleetStayTime = round(($TotalFleetCons / $FleetStayConsumption) * 3600);

	$StayDuration = $FleetStayTime;
	$StayTime = $fleet['start_time'] + $FleetStayTime;
}

if ($fleet_group_mr > 0)
	$fleet['end_time'] = $StayDuration + $duration + $ttt;
else
	$fleet['end_time'] = $StayDuration + (2 * $duration) + time();

$StockMetal 	= app::$planetrow->data['metal'];
$StockCrystal 	= app::$planetrow->data['crystal'];
$StockDeuterium = app::$planetrow->data['deuterium'];
$StockDeuterium -= ($consumption + $TotalFleetCons);

$StockOk = ($StockMetal >= $TransMetal && $StockCrystal >= $TransCrystal && $StockDeuterium >= $TransDeuterium);

if (!$StockOk && $TargetPlanet['id_owner'] != 1)
	$this->message("<font color=\"red\"><b>" . _getText('fl_noressources') . strings::pretty_number($consumption) . "</b></font>", 'Ошибка', "?set=fleet", 2);

if ($StorageNeeded > $FleetStorage && !user::get()->isAdmin())
	$this->message("<font color=\"red\"><b>" . _getText('fl_nostoragespa') . strings::pretty_number($StorageNeeded - $FleetStorage) . "</b></font>", 'Ошибка', "?set=fleet", 2);

// Баш контроль
if ($fleetmission == 1)
{
	$night_time = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));

	$log = db::query("SELECT kolvo FROM game_logs WHERE `s_id` = '".user::get()->data['id']."' AND `mission` = 1 AND e_galaxy = " . $TargetPlanet['galaxy'] . " AND e_system = " . $TargetPlanet['system'] . " AND e_planet = " . $TargetPlanet['planet'] . " AND time > " . $night_time . "", true);

	if (isset($log['kolvo']) && $log['kolvo'] > 2 && $ad['type'] != 3)
		$this->message("<font color=\"red\"><b>Баш-контроль. Лимит ваших нападений на планету исчерпан.</b></font>", 'Ошибка', "?set=fleet", 2);

	if (isset($log['kolvo']))
		db::query("UPDATE game_logs SET kolvo = kolvo + 1 WHERE `s_id` = '".user::get()->data['id']."' AND `mission` = 1 AND e_galaxy = " . $TargetPlanet['galaxy'] . " AND e_system = " . $TargetPlanet['system'] . " AND e_planet = " . $TargetPlanet['planet'] . " AND time > " . $night_time . "");
	else
		db::query("INSERT INTO game_logs VALUES (1, " . time() . ", 1, " . user::get()->data['id'] . ", " . app::$planetrow->data['galaxy'] . ", " . app::$planetrow->data['system'] . ", " . app::$planetrow->data['planet'] . ", " . $TargetPlanet['id_owner'] . ", " . $TargetPlanet['galaxy'] . ", " . $TargetPlanet['system'] . ", " . $TargetPlanet['planet'] . ")");

}
//

// Увод флота
//$fleets_num = db::query("SELECT fleet_id FROM game_fleets WHERE fleet_mission = '1' AND fleet_end_galaxy = ".app::$planetrow->data['galaxy']." AND fleet_end_system = ".app::$planetrow->data['system']." AND fleet_end_planet = ".app::$planetrow->data['planet']." AND fleet_end_type = ".app::$planetrow->data['planet_type']." AND fleet_start_time < ".(time() + 5)."");

//if (db::num_rows($fleets_num) > 0)
//		message ("<font color=\"red\"><b>Ваш флот не может взлететь из-за находящегося по близости от орбиты планеты атакующего флота.</b></font>", 'Ошибка', "fleet." . $phpEx, 2);
//

if ($fleet_group_mr > 0 && $i > 0 && $ttt > 0)
{
	foreach ($arrr AS $id => $row)
	{
		$end = $ttt + $row['end'] - $row['start'];
		db::query("UPDATE game_fleets SET fleet_start_time = " . $ttt . ", fleet_end_time = " . $end . ", fleet_time = " . $ttt . " WHERE fleet_id = '" . $row['id'] . "'");
	}
}

if (($fleetmission == 1 || $fleetmission == 3) && $HeDBRec['id'] != user::get()->data['id'] && !user::get()->isAdmin())
{
	$check = db::first(db::query("SELECT COUNT(*) as num FROM game_log_ip WHERE id = ".$HeDBRec['id']." AND time > ".(time() - 86400 * 3)." AND ip IN (SELECT ip FROM game_log_ip WHERE id = ".user::get()->data['id']." AND time > ".(time() - 86400 * 3).")", true));

	if ($check > 0 || $HeDBRec['user_lastip'] == user::get()->data['user_lastip'])
		$this->message("<font color=\"red\"><b>Вы не можете посылать флот с миссией \"Транспорт\" и \"Атака\" к игрокам, с которыми были пересечения по IP адресу.</b></font>", 'Ошибка', "?set=fleet", 5);
}

if ($fleetmission == 3 && $HeDBRec['id'] != user::get()->data['id'] && !user::get()->isAdmin())
{
	if (isset($NoobNoActive) && $NoobNoActive == 1)
		$this->message("<font color=\"red\"><b>Вы не можете посылать флот с миссией \"Транспорт\" к неактивному игроку.</b></font>", 'Ошибка', "?set=fleet", 5);

	$cnt = db::first(db::query("SELECT COUNT(*) as num FROM game_log_transfers WHERE user_id = ".user::get()->data['id']." AND target_id = ".$HeDBRec['id']." AND time > ".(time() - 86400 * 7)."", true));

	if ($cnt >= 3)
		$this->message("<font color=\"red\"><b>Вы не можете посылать флот с миссией \"Транспорт\" другому игроку чаще 3х раз в неделю.</b></font>", 'Ошибка', "?set=fleet", 5);

	$cnt = db::first(db::query("SELECT COUNT(*) as num FROM game_log_transfers WHERE user_id = ".user::get()->data['id']." AND target_id = ".$HeDBRec['id']." AND time > ".(time() - 86400 * 1)."", true));

	if ($cnt > 0)
		$this->message("<font color=\"red\"><b>Вы не можете посылать флот с миссией \"Транспорт\" другому игроку чаще одного раза в день.</b></font>", 'Ошибка', "?set=fleet", 5);

	$equiv = $TransMetal + $TransCrystal * 2 + $TransDeuterium * 4;

	if ($equiv > 15000000)
		$this->message("<font color=\"red\"><b>Вы не можете посылать флот с миссией \"Транспорт\" другому игроку с количеством ресурсов большим чем 15кк в эквиваленте металла.</b></font>", 'Ошибка', "?set=fleet", 5);

	db::query("INSERT INTO game_log_transfers VALUES ('".time()."', '".user::get()->data['id']."', 's:[".app::$planetrow->data['galaxy'].":".app::$planetrow->data['system'].":".app::$planetrow->data['planet']."(".app::$planetrow->data['planet_type'].")];e:[".$galaxy.":".$system.":".$planet."(".$planettype.")];f:[".$fleet_array."];m:".$TransMetal.";c:".$TransCrystal.";d:".$TransDeuterium.";', '".$TargetPlanet['id_owner']."')");

	$str_error = "Информация о передаче ресурсов добавлена в журнал оператора.<br>";
}

if ($TargetPlanet['id_owner'] == 1)
{
	$fleet['start_time'] = time() + 30;
	$fleet['end_time'] = time() + 60;
	$consumption = 0;
}

if (user::get()->isAdmin() && $fleetmission != 6)
{
	$fleet['start_time'] 	= time() + 15;
	$fleet['end_time'] 		= time() + 30;

	if ($StayTime)
		$StayTime = $fleet['start_time'] + 5;

	$consumption = 0;
}

if ($fleetmission == 15 && user::get()->data['tutorial'] == 7 && user::get()->data['tutorial_value'] == 0)
	db::query("UPDATE game_users SET tutorial_value = 1 WHERE id = " . user::get()->data['id'] . ";");
if ($fleetmission == 8 && user::get()->data['tutorial'] == 9 && user::get()->data['tutorial_value'] == 0)
	db::query("UPDATE game_users SET tutorial_value = 1 WHERE id = " . user::get()->data['id'] . ";");
if ($fleetmission == 6 && user::get()->data['tutorial'] == 6 && user::get()->data['tutorial_value'] == 0)
	db::query("UPDATE game_users SET tutorial_value = 1 WHERE id = " . user::get()->data['id'] . ";");

if ($fleetmission == 1)
{
	$raunds = (isset($_POST['raunds'])) ? intval($_POST['raunds']) : 6;
	$raunds = ($raunds < 6 || $raunds > 10) ? 6 : $raunds;
}
else
	$raunds = 0;

sql::build()->insert('game_fleets')->set(Array
(
	'fleet_owner' 			=> user::get()->data['id'],
	'fleet_owner_name' 		=> app::$planetrow->data['name'],
	'fleet_mission' 		=> $fleetmission,
	'fleet_array' 			=> $fleet_array,
	'fleet_start_time' 		=> $fleet['start_time'],
	'fleet_start_galaxy' 	=> app::$planetrow->data['galaxy'],
	'fleet_start_system' 	=> app::$planetrow->data['system'],
	'fleet_start_planet' 	=> app::$planetrow->data['planet'],
	'fleet_start_type' 		=> app::$planetrow->data['planet_type'],
	'fleet_end_time' 		=> $fleet['end_time'],
	'fleet_end_stay' 		=> $StayTime,
	'fleet_end_galaxy' 		=> $galaxy,
	'fleet_end_system' 		=> $system,
	'fleet_end_planet' 		=> $planet,
	'fleet_end_type' 		=> $planettype,
	'fleet_resource_metal' 	=> $TransMetal,
	'fleet_resource_crystal' 	=> $TransCrystal,
	'fleet_resource_deuterium' 	=> $TransDeuterium,
	'fleet_target_owner' 	=> $TargetPlanet['id_owner'],
	'fleet_target_owner_name' 	=> $TargetPlanet['name'],
	'fleet_group' 			=> $fleet_group_mr,
	'raunds' 				=> $raunds,
	'start_time' 			=> time(),
	'fleet_time' 			=> $fleet['start_time']
))
->execute();

app::$planetrow->data["metal"] 		-= $TransMetal;
app::$planetrow->data["crystal"] 	-= $TransCrystal;
app::$planetrow->data["deuterium"] 	-= $TransDeuterium + $consumption + $TotalFleetCons;

$QryUpdatePlanet = "UPDATE game_planets SET ";
$QryUpdatePlanet .= $FleetSubQRY;
$QryUpdatePlanet .= "`metal` = '" . app::$planetrow->data["metal"] . "', ";
$QryUpdatePlanet .= "`crystal` = '" . app::$planetrow->data["crystal"] . "', ";
$QryUpdatePlanet .= "`deuterium` = '" . app::$planetrow->data["deuterium"] . "' ";
$QryUpdatePlanet .= "WHERE ";
$QryUpdatePlanet .= "`id` = '" . app::$planetrow->data['id'] . "'";
db::query($QryUpdatePlanet);

$html = "<center>";
$html .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"600\">";
$html .= "<tr height=\"20\">";
$html .= "<td class=\"c\" colspan=\"2\"><span class=\"success\">" . ((isset($str_error)) ? $str_error : _getText('fl_fleet_send')) . "</span></td>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_mission') . "</th>";
$html .= "<th>" . _getText('type_mission', $fleetmission) . "</th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_dist') . "</th>";
$html .= "<th>" . strings::pretty_number($distance) . "</th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_speed') . "</th>";
$html .= "<th>" . strings::pretty_number($MaxFleetSpeed) . "</th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_deute_need') . "</th>";
$html .= "<th>" . strings::pretty_number($consumption) . "</th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_from') . "</th>";
$html .= "<th>" . app::$planetrow->data['galaxy'] . ":" . app::$planetrow->data['system'] . ":" . app::$planetrow->data['planet'] . "</th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_dest') . "</th>";
$html .= "<th>" . $galaxy . ":" . $system . ":" . $planet . "</th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_time_go') . "</th>";
$html .= "<th>" . datezone("d H:i:s", $fleet['start_time']) . "</th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<th>" . _getText('fl_time_back') . "</th>";
$html .= "<th>" . datezone("d H:i:s", $fleet['end_time']) . "</th>";
$html .= "</tr><tr height=\"20\">";
$html .= "<td class=\"c\" colspan=\"2\">" . _getText('fl_title') . "</td>";


foreach ($fleetarray as $Ship => $Count)
{
	$html .= "</tr><tr height=\"20\">";
	$html .= "<th>" . _getText('tech', $Ship) . "</th>";
	$html .= "<th>" . strings::pretty_number($Count) . "</th>";
}
$html .= "</tr></table></center>";

$this->message($html, '' . _getText('fl_title') . '', '?set=fleet', '3')


?>