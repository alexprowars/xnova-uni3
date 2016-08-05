<?php

function GetGameSpeedFactor ()
{
	return core::getConfig('fleet_speed') / 2500;
}

function GetTargetDistance ($OrigGalaxy, $DestGalaxy, $OrigSystem, $DestSystem, $OrigPlanet, $DestPlanet)
{
	if (($OrigGalaxy - $DestGalaxy) != 0)
	{
		$distance = abs($OrigGalaxy - $DestGalaxy) * 20000;
	}
	elseif (($OrigSystem - $DestSystem) != 0)
	{
		$distance = abs($OrigSystem - $DestSystem) * 5 * 19 + 2700;
	}
	elseif (($OrigPlanet - $DestPlanet) != 0)
	{
		$distance = abs($OrigPlanet - $DestPlanet) * 5 + 1000;
	}
	else
	{
		$distance = 5;
	}

	return $distance;
}

function GetMissionDuration ($GameSpeed, $MaxFleetSpeed, $Distance, $SpeedFactor)
{
	if (!$GameSpeed || !$MaxFleetSpeed || !$SpeedFactor || !$Distance)
	{
		global $user;

		$user->sendMessage(1, false, 0, 0, 'cdv', '' . json_encode($_GET) . '---' . json_encode($_POST) . '');
	}

	return round(((35000 / $GameSpeed * sqrt($Distance * 10 / $MaxFleetSpeed) + 10) / $SpeedFactor));
}

/**
 * @param  $FleetArray
 * @param  $Fleet
 * @param  $user user
 * @return array|int
 */
function GetFleetMaxSpeed ($FleetArray, $Fleet, $user)
{
	global $CombatCaps;

	$speedalls = array();

	if ($Fleet != 0)
	{
		$FleetArray[$Fleet] = 1;
	}

	foreach ($FleetArray as $Ship => $Count)
	{
		switch ($CombatCaps[$Ship]['type_engine'])
		{
			case 1:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'] * (1 + ($user->data['combustion_tech'] * 0.1));
				break;
			case 2:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'] * (1 + ($user->data['impulse_motor_tech'] * 0.2));
				break;
			case 3:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'] * (1 + ($user->data['hyperspace_motor_tech'] * 0.3));
				break;
			default:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'];
		}

		if ($user->bonusValue('fleet_speed') != 1)
			$speedalls[$Ship] = round($speedalls[$Ship] * $user->bonusValue('fleet_speed'));
	}

	if ($Fleet != 0)
		$speedalls = $speedalls[$Fleet];

	return $speedalls;
}

function SetShipsEngine ($user)
{
	global $CombatCaps, $reslist, $resource;

	foreach ($reslist['fleet'] as $Ship)
	{
		if (isset($CombatCaps[$Ship]) && isset($CombatCaps[$Ship]['engine_up']))
		{
			if ($user[$resource[$CombatCaps[$Ship]['engine_up']['tech']]] >= $CombatCaps[$Ship]['engine_up']['lvl'])
			{
				$CombatCaps[$Ship]['type_engine']++;
				$CombatCaps[$Ship]['speed'] = $CombatCaps[$Ship]['engine_up']['speed'];

				unset($CombatCaps[$Ship]['engine_up']);
			}
		}
	}
}

/**
 * @param  $Ship
 * @param  $user user
 * @return float
 */
function GetShipConsumption ($Ship, $user)
{
	global $CombatCaps;

	return ceil($CombatCaps[$Ship]['consumption'] * $user->bonusValue('fleet_fuel'));
}

function GetFleetConsumption ($FleetArray, $SpeedFactor, $MissionDuration, $MissionDistance, $FleetMaxSpeed, $Player)
{
	$consumption = 0;

	foreach ($FleetArray as $Ship => $Count)
	{
		if ($Ship > 0)
		{
			$ShipSpeed = GetFleetMaxSpeed("", $Ship, $Player);
			$ShipConsumption = GetShipConsumption($Ship, $Player);
			$spd = 35000 / ($MissionDuration * $SpeedFactor - 10) * sqrt($MissionDistance * 10 / $ShipSpeed);
			$consumption += ($ShipConsumption * $Count) * $MissionDistance / 35000 * (($spd / 10) + 1) * (($spd / 10) + 1);
		}
	}

	$consumption = round($consumption) + 1;

	return $consumption;
}

function GetFleetStay ($FleetArray)
{
	global $CombatCaps;

	$stay = 0;
	foreach ($FleetArray as $Ship => $Count)
	{
		if ($Ship > 0)
		{
			$stay += $CombatCaps[$Ship]['stay'] * $Count;
		}
	}
	return $stay;
}

function unserializeFleet ($fleetAmount)
{
	$fleetTyps = explode(';', $fleetAmount);

	$fleetAmount = array();

	foreach ($fleetTyps as $fleetTyp)
	{
		$temp = explode(',', $fleetTyp);

		if (empty($temp[0]))
			continue;

		if (!isset($fleetAmount[$temp[0]]))
		{
			$fleetAmount[$temp[0]] = array('cnt' => 0, 'lvl' => 0);
		}

		$lvl = explode("!", $temp[1]);

		$fleetAmount[$temp[0]]['cnt'] += $lvl[0];
		$fleetAmount[$temp[0]]['lvl']  = $lvl[1];
	}

	return $fleetAmount;
}

function BuildFleetEventTable ($FleetRow, $Status, $Owner, $Label, $Record)
{
	$FleetStyle = array(
		1 => 'attack',
		2 => 'federation',
		3 => 'transport',
		4 => 'deploy',
		5 => 'transport',
		6 => 'espionage',
		7 => 'colony',
		8 => 'harvest',
		9 => 'destroy',
		10 => 'missile',
		15 => 'transport',
		20 => 'attack'
	);
	$FleetStatus = array(0 => 'flight', 1 => 'holding', 2 => 'return');
	if ($Owner == true)
	{
		$FleetPrefix = 'own';
	}
	else
	{
		$FleetPrefix = '';
	}

	$MissionType = $FleetRow['fleet_mission'];
	$FleetContent = CreateFleetPopupedFleetLink($FleetRow, _getText('ov_fleet'), $FleetPrefix . $FleetStyle[$MissionType]);
	$FleetCapacity = CreateFleetPopupedMissionLink($FleetRow, _getText('type_mission', $MissionType), $FleetPrefix . $FleetStyle[$MissionType]);

	$StartPlanet = $FleetRow['fleet_owner_name'];
	$StartType = $FleetRow['fleet_start_type'];
	$TargetPlanet = $FleetRow['fleet_target_owner_name'];
	$TargetType = $FleetRow['fleet_end_type'];

	if ($Status != 2)
	{

		if ($StartPlanet == '')
			$StartID = ' с координат ';
		else
		{
			if ($StartType == 1)
			{
				$StartID = _getText('ov_planet_to');
			}
			elseif ($StartType == 3)
			{
				$StartID = _getText('ov_moon_to');
			}
			elseif ($StartType == 5)
			{
				$StartID = ' с военной базы ';
			}
			$StartID .= $StartPlanet . " ";
		}
		$StartID .= GetStartAdressLink($FleetRow, $FleetPrefix . $FleetStyle[$MissionType]);

		if ($TargetPlanet == '')
			$TargetID = ' координаты ';
		else
		{
			if ($MissionType != 15 && $MissionType != 5)
			{
				if ($TargetType == 1)
				{
					$TargetID = _getText('ov_planet_to_target');
				}
				elseif ($TargetType == 2)
				{
					$TargetID = _getText('ov_debris_to_target');
				}
				elseif ($TargetType == 3)
				{
					$TargetID = _getText('ov_moon_to_target');
				}
				elseif ($TargetType == 5)
				{
					$TargetID = ' военной базе ';
				}
			}
			else
			{
				$TargetID = _getText('ov_explo_to_target');
			}
			$TargetID .= $TargetPlanet . " ";
		}
		$TargetID .= GetTargetAdressLink($FleetRow, $FleetPrefix . $FleetStyle[$MissionType]);
	}
	else
	{
		if ($StartPlanet == '')
			$StartID = ' на координаты ';
		else
		{
			if ($StartType == 1)
			{
				$StartID = _getText('ov_back_planet');
			}
			elseif ($StartType == 3)
			{
				$StartID = _getText('ov_back_moon');
			}
			$StartID .= $StartPlanet . " ";
		}
		$StartID .= GetStartAdressLink($FleetRow, $FleetPrefix . $FleetStyle[$MissionType]);

		if ($TargetPlanet == '')
			$TargetID = ' с координат ';
		else
		{
			if ($MissionType != 15)
			{
				if ($TargetType == 1)
				{
					$TargetID = _getText('ov_planet_from');
				}
				elseif ($TargetType == 2)
				{
					$TargetID = _getText('ov_debris_from');
				}
				elseif ($TargetType == 3)
				{
					$TargetID = _getText('ov_moon_from');
				}
				elseif ($TargetType == 5)
				{
					$TargetID = ' с военной базы ';
				}
			}
			else
			{
				$TargetID = _getText('ov_explo_from');
			}
			$TargetID .= $TargetPlanet . " ";
		}
		$TargetID .= GetTargetAdressLink($FleetRow, $FleetPrefix . $FleetStyle[$MissionType]);
	}

	if ($Owner == true)
	{
		$EventString = _getText('ov_une');
		$EventString .= $FleetContent;
	}
	else
	{
		$EventString = ($FleetRow['fleet_group'] != 0) ? 'Союзный ' : _getText('ov_une_hostile');
		$EventString .= $FleetContent;
		$EventString .= _getText('ov_hostile');
		$EventString .= BuildHostileFleetPlayerLink($FleetRow);
	}

	if ($Status == 0)
	{
		$Time = $FleetRow['fleet_start_time'];
		$Rest = $Time - time();
		$EventString .= _getText('ov_vennant');
		$EventString .= $StartID;
		$EventString .= _getText('ov_atteint');
		$EventString .= $TargetID;
		$EventString .= _getText('ov_mission');
	}
	elseif ($Status == 1)
	{
		$Time = $FleetRow['fleet_end_stay'];
		$Rest = $Time - time();
		$EventString .= _getText('ov_vennant');
		$EventString .= $StartID;

		if ($MissionType == 5)
			$EventString .= ' защищает ';
		else
			$EventString .= _getText('ov_explo_stay');

		$EventString .= $TargetID;
		$EventString .= _getText('ov_explo_mission');
	}
	elseif ($Status == 2)
	{
		$Time = $FleetRow['fleet_end_time'];
		$Rest = $Time - time();
		$EventString .= _getText('ov_rentrant');
		$EventString .= $TargetID;
		$EventString .= $StartID;
		$EventString .= _getText('ov_mission');
	}
	$EventString .= $FleetCapacity;

	$bloc['fleet_status'] = $FleetStatus[$Status];
	$bloc['fleet_prefix'] = $FleetPrefix;
	$bloc['fleet_style'] = $FleetStyle[$MissionType];
	$bloc['fleet_order'] = $Label . $Record;
	$bloc['fleet_time'] = datezone("H:i:s", $Time);
	$bloc['fleet_count_time'] = strings::pretty_time($Rest, ':');
	$bloc['fleet_descr'] = $EventString;
	$bloc['fleet_javas'] = InsertJavaScriptChronoApplet($Label, $Record, $Rest);

	return $bloc;
}

function CalculateMaxPlanetFields (&$planet)
{
	global $resource;

	return $planet["field_max"] + ($planet[$resource[33]] * 5) + (FIELDS_BY_MOONBASIS_LEVEL * $planet[$resource[41]]);
}

function ShowTopNavigationBar ()
{
	global $reslist, $resource;

	$parse = array();

	$parse['image'] = app::$planetrow->data['image'];
	$parse['name'] = app::$planetrow->data['name'];
	$parse['time'] = time();

	$parse['planetlist'] = '';

	if (core::getConfig('showPlanetListSelect', 0))
	{
		$planetsList = cache::get('app::planetlist_'.user::get()->getId().'');

		if ($planetsList === false)
		{
			$planetsList = user::get()->getUserPlanets(app::$user->data['id']);

			cache::set('app::planetlist_'.user::get()->getId().'', $planetsList, 300);
		}

		foreach ($planetsList AS $CurPlanet)
		{
			if ($CurPlanet['destruyed'] > 0)
				continue;

			$parse['planetlist'] .= "\n<option ";

			if ($CurPlanet['planet_type'] == 3)
				$parse['planetlist'] .= "style=\"color:red;\" ";
			elseif ($CurPlanet['planet_type'] == 5)
				$parse['planetlist'] .= "style=\"color:yellow;\" ";

			if ($CurPlanet['id'] == app::$user->data['current_planet'])
			{
				$parse['planetlist'] .= "selected=\"selected\" ";
			}
			if (isset($_GET['set']))
				$parse['planetlist'] .= "value=\"?set=" . $_GET['set'] . "";
			else
				$parse['planetlist'] .= "value=\"?set=overview";
			if (isset($_GET['mode']))
				$parse['planetlist'] .= "&amp;mode=" . $_GET['mode'];

			$parse['planetlist'] .= "&amp;cp=" . $CurPlanet['id'] . "&amp;re=0\">";

			$parse['planetlist'] .= "" . $CurPlanet['name'];
			$parse['planetlist'] .= "&nbsp;[" . $CurPlanet['galaxy'] . ":" . $CurPlanet['system'] . ":" . $CurPlanet['planet'];
			$parse['planetlist'] .= "]&nbsp;&nbsp;</option>";
		}
	}

	foreach ($reslist['res'] AS $res)
	{
		$parse[$res] = floor(app::$planetrow->data[$res]);

		$parse[$res.'_m'] = app::$planetrow->data[$res.'_max'];

		if (app::$planetrow->data[$res.'_max'] <= app::$planetrow->data[$res])
			$parse[$res.'_max'] = '<font class="full">';
		else
			$parse[$res.'_max'] = '<font color="#00ff00">';

		$parse[$res.'_max'] .= strings::pretty_number(app::$planetrow->data[$res.'_max']) . "</font>";
		$parse[$res.'_ph'] 	= app::$planetrow->data[$res.'_perhour'] + floor(core::getConfig($res.'_basic_income', 0) * core::getConfig('resource_multiplier', 1));
		$parse[$res.'_mp'] 	= app::$planetrow->data[$res.'_mine_porcent'] * 10;
	}

	$parse['energy_max'] 	= strings::pretty_number(app::$planetrow->data["energy_max"]);
	$parse['energy_total'] 	= strings::colorNumber(strings::pretty_number(app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"]));

	$parse['credits'] = strings::pretty_number(app::$user->data['credits']);

	$parse['officiers'] = array();

	foreach ($reslist['officier'] AS $officier)
	{
		$parse['officiers'][$officier] = app::$user->data[$resource[$officier]];
	}

	$parse['energy_ak'] = (app::$planetrow->data['ak_station'] > 0 ? round(app::$planetrow->data['energy_ak'] / (5000 * app::$planetrow->data['ak_station']), 2) * 100 : 0);
	$parse['energy_ak'] = min(100, max(0, $parse['energy_ak']));

	$parse['ak'] = round(app::$planetrow->data['energy_ak']) . " / " . round(5000 * app::$planetrow->data['ak_station']);

	if ($parse['energy_ak'] > 0 && $parse['energy_ak'] < 100)
	{
		if ((app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"]) > 0)
			$parse['ak'] .= '<br>Заряд: ' . strings::pretty_time(round(((round(5000 * app::$planetrow->data['ak_station']) - app::$planetrow->data['energy_ak']) / (app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"])) * 3600)) . '';
		elseif ((app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"]) < 0)
			$parse['ak'] .= '<br>Разряд: ' . strings::pretty_time(round((app::$planetrow->data['energy_ak'] / abs(app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"])) * 3600)) . '';
	}

	$parse['messages'] = app::$user->data['new_message'];

	if (app::$user->data['mnl_alliance'] > 0 && app::$user->data['ally_id'] == 0)
	{
		app::$user->data['mnl_alliance'] = 0;
		sql::build()->update('game_users')->setField('mnl_alliance', 0)->where('id', '=', app::$user->data['id'])->execute();
	}

	$parse['ally_messages'] = (app::$user->data['ally_id'] != 0) ? app::$user->data['mnl_alliance'] : '';

	return $parse;
}

function ShowTopNavigationBar2 ()
{
	global $reslist, $resource;

	$parse = array();

	$parse['image'] = app::$planetrow->data['image'];
	$parse['name'] = app::$planetrow->data['name'];
	$parse['time'] = time();

	$parse['planetlist'] = '';

	if (core::getConfig('showPlanetListSelect', 0))
	{
		$ThisUsersPlanets = user::get()->getUserPlanets(app::$user->data['id']);

		foreach ($ThisUsersPlanets AS $CurPlanet)
		{
			if ($CurPlanet['destruyed'] > 0)
				continue;

			$parse['planetlist'] .= "\n<option ";

			if ($CurPlanet['planet_type'] == 3)
				$parse['planetlist'] .= "style=\"color:red;\" ";
			elseif ($CurPlanet['planet_type'] == 5)
				$parse['planetlist'] .= "style=\"color:yellow;\" ";

			if ($CurPlanet['id'] == app::$user->data['current_planet'])
			{
				$parse['planetlist'] .= "selected=\"selected\" ";
			}
			if (isset($_GET['set']))
				$parse['planetlist'] .= "value=\"?set=" . $_GET['set'] . "";
			else
				$parse['planetlist'] .= "value=\"?set=overview";
			if (isset($_GET['mode']))
				$parse['planetlist'] .= "&amp;mode=" . $_GET['mode'];

			$parse['planetlist'] .= "&amp;cp=" . $CurPlanet['id'] . "&amp;re=0\">";

			$parse['planetlist'] .= "" . $CurPlanet['name'];
			$parse['planetlist'] .= "&nbsp;[" . $CurPlanet['galaxy'] . ":" . $CurPlanet['system'] . ":" . $CurPlanet['planet'];
			$parse['planetlist'] .= "]&nbsp;&nbsp;</option>";
		}
	}

	$parse['metal'] = floor(app::$planetrow->data["metal"]);
	$parse['crystal'] = floor(app::$planetrow->data["crystal"]);
	$parse['deuterium'] = round(app::$planetrow->data["deuterium"]);

	$parse['energy_max'] 	= strings::pretty_number(app::$planetrow->data["energy_max"]);
	$parse['energy_total'] 	= strings::colorNumber(strings::pretty_number(app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"]));

	if (app::$planetrow->data["metal_max"] <= app::$planetrow->data["metal"])
		$parse['metal_max'] = '<font class="full">';
	else
		$parse['metal_max'] = '<font color="#00ff00">';

	$parse['metal_m'] = app::$planetrow->data["metal_max"];
	$parse['metal_pm'] = (app::$planetrow->data["metal_perhour"] + floor(core::getConfig('metal_basic_income') * core::getConfig('resource_multiplier'))) / 3600;
	$parse['metal_mp'] = app::$planetrow->data['metal_mine_porcent'] * 10;
	$parse['metal_ph'] = strings::pretty_number(app::$planetrow->data["metal_perhour"] + floor(core::getConfig('metal_basic_income') * core::getConfig('resource_multiplier')));
	$parse['metal_pd'] = strings::pretty_number((app::$planetrow->data["metal_perhour"] + floor(core::getConfig('metal_basic_income') * core::getConfig('resource_multiplier'))) * 24);

	$parse['metal_max'] .= strings::pretty_number(app::$planetrow->data["metal_max"]) . "</font>";


	if (app::$planetrow->data["crystal_max"] <= app::$planetrow->data["crystal"])
		$parse['crystal_max'] = '<font class="full">';
	else
		$parse['crystal_max'] = '<font color="#00ff00">';

	$parse['crystal_m'] = app::$planetrow->data["crystal_max"];
	$parse['crystal_pm'] = (app::$planetrow->data["crystal_perhour"] + floor(core::getConfig('crystal_basic_income') * core::getConfig('resource_multiplier'))) / 3600;
	$parse['crystal_mp'] = app::$planetrow->data['crystal_mine_porcent'] * 10;
	$parse['crystal_ph'] = strings::pretty_number(app::$planetrow->data["crystal_perhour"] + floor(core::getConfig('crystal_basic_income') * core::getConfig('resource_multiplier')));
	$parse['crystal_pd'] = strings::pretty_number((app::$planetrow->data["crystal_perhour"] + floor(core::getConfig('crystal_basic_income') * core::getConfig('resource_multiplier'))) * 24);
	$parse['crystal_max'] .= strings::pretty_number(app::$planetrow->data["crystal_max"]) . "</font>";


	if (app::$planetrow->data["deuterium_max"] <= app::$planetrow->data["deuterium"])
		$parse['deuterium_max'] = '<font class="full">';
	else
		$parse['deuterium_max'] = '<font color="#00ff00">';

	$parse['deuterium_m'] = app::$planetrow->data["deuterium_max"];
	$parse['deuterium_pm'] = (app::$planetrow->data["deuterium_perhour"] + floor(core::getConfig('deuterium_basic_income') * core::getConfig('resource_multiplier'))) / 3600;
	$parse['deuterium_mp'] = app::$planetrow->data['deuterium_mine_porcent'] * 10;
	$parse['deuterium_ph'] = strings::pretty_number(app::$planetrow->data["deuterium_perhour"] + floor(core::getConfig('deuterium_basic_income') * core::getConfig('resource_multiplier')));
	$parse['deuterium_pd'] = strings::pretty_number((app::$planetrow->data["deuterium_perhour"] + floor(core::getConfig('deuterium_basic_income') * core::getConfig('resource_multiplier'))) * 24);
	$parse['deuterium_max'] .= strings::pretty_number(app::$planetrow->data["deuterium_max"]) . "</font>";

	$parse['credits'] = strings::pretty_number(app::$user->data['credits']);

	$parse['officiers'] = array();

	foreach ($reslist['officier'] AS $officier)
	{
		$parse['officiers'][$officier] = app::$user->data[$resource[$officier]];
	}

	$parse['isAkActive'] = app::$planetrow->data['ak_station'] > 0;
	$parse['energy_ak'] = (app::$planetrow->data['ak_station'] > 0 ? round(app::$planetrow->data['energy_ak'] / (5000 * app::$planetrow->data['ak_station']), 2) * 100 : 0);

	if ($parse['energy_ak'] == 0)
		$parse['energy'] = "batt0.png";
	elseif ($parse['energy_ak'] >= 100)
		$parse['energy'] = "batt100.png";
	else
		$parse['energy'] = "batt.php?p=" . $parse['energy_ak'];

	$parse['ak'] = round(app::$planetrow->data['energy_ak']) . " / " . round(5000 * app::$planetrow->data['ak_station']);

	if ($parse['energy_ak'] > 0 && $parse['energy_ak'] < 100)
	{
		if ((app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"]) > 0)
			$parse['ak'] .= '<br>Заряд: ' . strings::pretty_time(round(((round(5000 * app::$planetrow->data['ak_station']) - app::$planetrow->data['energy_ak']) / (app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"])) * 3600)) . '';
		elseif ((app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"]) < 0)
			$parse['ak'] .= '<br>Разряд: ' . strings::pretty_time(round((app::$planetrow->data['energy_ak'] / abs(app::$planetrow->data['energy_max'] + app::$planetrow->data["energy_used"])) * 3600)) . '';
	}

	$parse['messages'] = app::$user->data['new_message'];

	if (app::$user->data['mnl_alliance'] > 0 && app::$user->data['ally_id'] == 0)
	{
		db::query("UPDATE game_users SET mnl_alliance = 0 WHERE id = " . app::$user->data['id'] . "");
		app::$user->data['mnl_alliance'] = 0;
	}

	$parse['ally_messages'] = (app::$user->data['ally_id'] != 0) ? app::$user->data['mnl_alliance'] : '';

	return $parse;
}

/**
 * @param  $CurrentUser user
 * @param  $CurrentPlanet
 * @param  $Element
 * @param bool $Incremental
 * @param bool $ForDestroy
 * @return bool
 */
function IsElementBuyable ($CurrentUser, $CurrentPlanet, $Element, $Incremental = true, $ForDestroy = false)
{
	$RetValue = true;

	$cost = GetBuildingPrice($CurrentUser, $CurrentPlanet, $Element, $Incremental, $ForDestroy);

	foreach ($cost AS $ResType => $ResCount)
	{
		if (!isset($CurrentPlanet[$ResType]) || $ResCount > $CurrentPlanet[$ResType])
		{
			$RetValue = false;
			break;
		}
	}

	return $RetValue;
}

function IsTechnologieAccessible ($user, $planet, $Element)
{
	global $requeriments, $resource;

	if (isset($requeriments[$Element]))
	{
		$enabled = true;
		foreach ($requeriments[$Element] as $ReqElement => $EleLevel)
		{
			if ($ReqElement == 700 && $user[$resource[$ReqElement]] != $EleLevel)
			{
				return false;
			}
			elseif (isset($user[$resource[$ReqElement]]) && $user[$resource[$ReqElement]] >= $EleLevel)
			{
				// break;
			}
			elseif (isset($planet[$resource[$ReqElement]]) && $planet[$resource[$ReqElement]] >= $EleLevel)
			{
				$enabled = true;
			}
			elseif (isset($planet['planet_type']) && $planet['planet_type'] == 5 && ($Element == 43 || $Element == 502 || $Element == 503) && ($ReqElement == 21 || $ReqElement == 41))
			{
				$enabled = true;
			}
			else
			{
				return false;
			}
		}
		return $enabled;
	}
	else
		return true;
}

function checkTechnologyRace ($user, $Element)
{
	global $requeriments, $resource;

	if (isset($requeriments[$Element]))
	{
		foreach ($requeriments[$Element] as $ReqElement => $EleLevel)
		{
			if ($ReqElement == 700 && $user[$resource[$ReqElement]] != $EleLevel)
			{
				return false;
			}
		}

		return true;
	}
	else
		return true;
}

function CheckLabSettingsInQueue ($CurrentPlanet)
{
	if ($CurrentPlanet['b_building_id'] != '')
	{
		$BuildQueue = $CurrentPlanet['b_building_id'];

		if (strpos($BuildQueue, ";"))
		{
			$Queue = explode(";", $BuildQueue);
			$CurrentBuilding = $Queue[0];
		}
		else
		{
			$CurrentBuilding = $BuildQueue;
		}

		if ($CurrentBuilding == 31 && core::getConfig('BuildLabWhileRun', 0) != 1)
			$return = false;
		else
			$return = true;

	}
	else
		$return = true;

	return $return;
}

function GetStartAdressLink ($FleetRow, $FleetType = '')
{
	$Link = "<a href=\"?set=galaxy&amp;r=3&amp;galaxy=" . $FleetRow['fleet_start_galaxy'] . "&amp;system=" . $FleetRow['fleet_start_system'] . "\" " . $FleetType . " >";
	$Link .= "[" . $FleetRow['fleet_start_galaxy'] . ":" . $FleetRow['fleet_start_system'] . ":" . $FleetRow['fleet_start_planet'] . "]</a>";
	return $Link;
}

function GetTargetAdressLink ($FleetRow, $FleetType = '')
{
	$Link = "<a href=\"?set=galaxy&amp;r=3&amp;galaxy=" . $FleetRow['fleet_end_galaxy'] . "&amp;system=" . $FleetRow['fleet_end_system'] . "\" " . $FleetType . " >";
	$Link .= "[" . $FleetRow['fleet_end_galaxy'] . ":" . $FleetRow['fleet_end_system'] . ":" . $FleetRow['fleet_end_planet'] . "]</a>";
	return $Link;
}

function BuildPlanetAdressLink ($CurrentPlanet)
{
	$Link = "<a href=\"?set=galaxy&amp;r=3&amp;galaxy=" . $CurrentPlanet['galaxy'] . "&amp;system=" . $CurrentPlanet['system'] . "\">";
	$Link .= "[" . $CurrentPlanet['galaxy'] . ":" . $CurrentPlanet['system'] . ":" . $CurrentPlanet['planet'] . "]</a>";
	return $Link;
}

function BuildHostileFleetPlayerLink ($FleetRow)
{
	$PlayerName = db::query("SELECT `username` FROM game_users WHERE `id` = '" . $FleetRow['fleet_owner'] . "';", true);

	$Link = $PlayerName['username'] . " ";
	$Link .= "<a href=\"?set=messages&amp;mode=write&amp;id=" . $FleetRow['fleet_owner'] . "\" title=\"" . _getText('ov_message') . "\"><span class='sprite skin_m'></span></a>";

	return $Link;
}

function GetNextJumpWaitTime ($CurMoon)
{
	global $resource;

	$JumpGateLevel = $CurMoon[$resource[43]];
	$LastJumpTime = $CurMoon['last_jump_time'];
	if ($JumpGateLevel > 0)
	{
		$WaitBetweenJmp = (60 * 60) * (1 / $JumpGateLevel);
		$NextJumpTime = $LastJumpTime + $WaitBetweenJmp;
		if ($NextJumpTime >= time())
		{
			$RestWait = $NextJumpTime - time();
			$RestString = " " . strings::pretty_time($RestWait);
		}
		else
		{
			$RestWait = 0;
			$RestString = "";
		}
	}
	else
	{
		$RestWait = 0;
		$RestString = "";
	}
	$RetValue['string'] = $RestString;
	$RetValue['value'] = $RestWait;

	return $RetValue;
}

function InsertJavaScriptChronoApplet ($Type, $Ref, $Value)
{
	return "<script>FlotenTime('bxx" . $Type . $Ref . "', " . $Value . ");</script>";
}

function CreateFleetPopupedFleetLink ($FleetRow, $Texte, $FleetType)
{
	global $user;

	$FleetRec = explode(";", $FleetRow['fleet_array']);

	$FleetPopup = "<table width=200>";
	$r = 'javascript:;';
	$Total = 0;

	if ($FleetRow['fleet_owner'] != $user->data['id'] && $user->data['spy_tech'] < 2)
	{
		$FleetPopup .= "<tr><td width=100% align=center><font color=white>Нет информации<font></td></tr>";
	}
	elseif ($FleetRow['fleet_owner'] != $user->data['id'] && $user->data['spy_tech'] < 4)
	{
		foreach ($FleetRec as $Group)
		{
			if ($Group != '')
			{
				$Ship = explode(",", $Group);
				$Count = explode("!", $Ship[1]);
				$Total = $Total + $Count[0];
			}
		}
		$FleetPopup .= "<tr><td width=50% align=left><font color=white>Численность:<font></td><td width=50% align=right><font color=white>" . strings::pretty_number($Total) . "<font></td></tr>";
	}
	elseif ($FleetRow['fleet_owner'] != $user->data['id'] && $user->data['spy_tech'] < 8)
	{
		foreach ($FleetRec as $Group)
		{
			if ($Group != '')
			{
				$Ship = explode(",", $Group);
				$Count = explode("!", $Ship[1]);
				$Total = $Total + $Count[0];
				$FleetPopup .= "<tr><td width=100% align=center colspan=2><font color=white>" . _getText('tech', $Ship[0]) . "<font></td></tr>";
			}
		}
		$FleetPopup .= "<tr><td width=50% align=left><font color=white>Численность:<font></td><td width=50% align=right><font color=white>" . strings::pretty_number($Total) . "<font></td></tr>";
	}
	else
	{
		if ($FleetRow['fleet_target_owner'] == $user->data['id'] && $FleetRow['fleet_mission'] == 1)
			$r = '?set=sim&r=';

		foreach ($FleetRec as $Group)
		{
			if ($Group != '')
			{
				$Ship = explode(",", $Group);
				$Count = explode("!", $Ship[1]);
				$FleetPopup .= "<tr><td width=75% align=left><font color=white>" . _getText('tech', $Ship[0]) . ":<font></td><td width=25% align=right><font color=white>" . strings::pretty_number($Count[0]) . "<font></td></tr>";

				if ($r != 'javascript:;')
					$r .= $Group . ';';
			}
		}
	}

	$FleetPopup .= "</table>";
	$FleetPopup .= "' class=\"" . $FleetType . "\">" . $Texte . "</a>";

	$FleetPopup = "<a href='" . $r . "' class=\"tooltip\" data-tooltip-content='" . $FleetPopup;

	return $FleetPopup;

}

function CreateFleetPopupedMissionLink ($FleetRow, $Texte, $FleetType)
{
	$FleetTotalC = $FleetRow['fleet_resource_metal'] + $FleetRow['fleet_resource_crystal'] + $FleetRow['fleet_resource_deuterium'];

	if ($FleetTotalC != 0)
	{
		$FRessource = "<table width=200>";
		$FRessource .= "<tr><td width=50% align=left><font color=white>" . _getText('Metal') . "<font></td><td width=50% align=right><font color=white>" . strings::pretty_number($FleetRow['fleet_resource_metal']) . "<font></td></tr>";
		$FRessource .= "<tr><td width=50% align=left><font color=white>" . _getText('Crystal') . "<font></td><td width=50% align=right><font color=white>" . strings::pretty_number($FleetRow['fleet_resource_crystal']) . "<font></td></tr>";
		$FRessource .= "<tr><td width=50% align=left><font color=white>" . _getText('Deuterium') . "<font></td><td width=50% align=right><font color=white>" . strings::pretty_number($FleetRow['fleet_resource_deuterium']) . "<font></td></tr>";
		$FRessource .= "</table>";
	}
	else
	{
		$FRessource = "";
	}

	if ($FRessource <> "")
	{
		$MissionPopup = "<a href='javascript:;' data-tooltip-content='" . $FRessource . "' class=\"tooltip " . $FleetType . "\">" . $Texte . "</a>";
	}
	else
	{
		$MissionPopup = $Texte . "";
	}

	return $MissionPopup;
}

function getTechTree ($Element)
{
	global $requeriments, $resource, $planetrow;

	$result = '';

	if (isset($requeriments[$Element]))
	{
		$result = "";

		foreach ($requeriments[$Element] as $ResClass => $Level)
		{
			if ($ResClass != 700)
			{
				if (isset(user::get()->data[$resource[$ResClass]]) && user::get()->data[$resource[$ResClass]] >= $Level)
				{
					$result .= "<span class=\"positive\">";
				}
				elseif (isset($planetrow->data[$resource[$ResClass]]) && $planetrow->data[$resource[$ResClass]] >= $Level)
				{
					$result .= "<span class=\"positive\">";
				}
				else
				{
					$result .= "<span class=\"negative\">";
				}
				$result .= _getText('tech', $ResClass) . " (" . _getText('level') . " " . $Level . "";

				if (isset(user::get()->data[$resource[$ResClass]]) && user::get()->data[$resource[$ResClass]] < $Level)
				{
					$minus = $Level - user::get()->data[$resource[$ResClass]];
					$result .= " + <b>" . $minus . "</b>";
				}
				elseif (isset($planetrow->data[$resource[$ResClass]]) && $planetrow->data[$resource[$ResClass]] < $Level)
				{
					$minus = $Level - $planetrow->data[$resource[$ResClass]];
					$result .= " + <b>" . $minus . "</b>";
				}
			}
			else
			{
				$result .= _getText('tech', $ResClass) . " (";

				if (user::get()->data['race'] != $Level)
					$result .= "<span class=\"negative\">" . _getText('race', $Level);
				else
					$result .= "<span class=\"positive\">" . _getText('race', $Level);
			}

			$result .= ")</span><br>";
		}
	}

	return $result;
}

/**
 * @param  $user user
 * @param  $planet array
 * @param  $Element integer
 * @return int
 */
function GetBuildingTime ($user, $planet, $Element)
{
	global $resource, $reslist;

	$time = 0;

	$cost = GetBuildingPrice($user, $planet, $Element, !(in_array($Element, $reslist['defense']) || in_array($Element, $reslist['fleet'])), false, false);
	$cost = $cost['metal'] + $cost['crystal'];

	if (in_array($Element, $reslist['build']))
	{
		$time = ($cost / core::getConfig('game_speed')) * (1 / ($planet[$resource['14']] + 1)) * pow(0.5, $planet[$resource['15']]);
		$time = floor($time * 3600 * $user->bonusValue('time_building'));
	}
	elseif (in_array($Element, $reslist['tech']) || in_array($Element, $reslist['tech_f']))
	{
		if (isset($planet['spaceLabs']) && count($planet['spaceLabs']))
		{
			$lablevel = 0;

			global $requeriments;

			foreach ($planet['spaceLabs'] as $Levels)
			{
				if (!isset($requeriments[$Element][31]) || $Levels >= $requeriments[$Element][31])
					$lablevel += $Levels;
			}
		}
		else
			$lablevel = $planet[$resource['31']];

		$time = ($cost / core::getConfig('game_speed')) / (($lablevel + 1) * 2);
		$time = floor($time * 3600 * $user->bonusValue('time_research'));
	}
	elseif (in_array($Element, $reslist['defense']))
	{
		$time = ($cost / core::getConfig('game_speed')) * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
		$time = floor($time * 3600 * $user->bonusValue('time_defence'));
	}
	elseif (in_array($Element, $reslist['fleet']))
	{
		$time = ($cost / core::getConfig('game_speed')) * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
		$time = floor($time * 3600 * $user->bonusValue('time_fleet'));
	}

	if ($time < 1)
		$time = 1;

	return $time;
}

/**
 * @param $cost array
 * @param  $planet array
 * @return string
 */
function GetElementPrice ($cost, $planet)
{
	$array = array(
		'metal' 	=> array(_getText('Metal'), 'metall'),
		'crystal' 	=> array(_getText('Crystal'), 'kristall'),
		'deuterium' => array(_getText('Deuterium'), 'deuterium'),
		'energy_max'=> array(_getText('Energy'), 'energie')
	);

	$text = "";

	foreach ($array as $ResType => $ResTitle)
	{
		if (isset($cost[$ResType]) && $cost[$ResType] != 0)
		{
			$text .= "<div><img src='" . DPATH . "images/s_" . $ResTitle[1] . ".png' align=\"absmiddle\" class=\"tooltip\" data-tooltip-content='" . $ResTitle[0] . "'>";

			if ($cost[$ResType] > $planet[$ResType])
			{
				$text .= "<span class=\"resNo tooltip\" data-tooltip-content=\"необходимо: ".strings::pretty_number($cost[$ResType] - $planet[$ResType])."\">" . strings::pretty_number($cost[$ResType]) . "</span> ";
			}
			else
			{
				$text .= "<span class=\"resYes\">" . strings::pretty_number($cost[$ResType]) . "</span> ";
			}
			$text .= "</div>";
		}
	}

	return $text;
}

/**
 * @param $user user
 * @param $planet array
 * @param $Element
 * @param bool $Incremental
 * @param bool $ForDestroy
 * @param bool $withBonus
 * @return array
 */
function GetBuildingPrice ($user, $planet, $Element, $Incremental = true, $ForDestroy = false, $withBonus = true)
{
	global $pricelist, $resource, $reslist;

	if ($Incremental)
		$level = (isset($planet[$resource[$Element]])) ? $planet[$resource[$Element]] : $user->data[$resource[$Element]];
	else
		$level = 0;

	$array 	= array('metal', 'crystal', 'deuterium', 'energy_max');
	$cost 	= array();

	foreach ($array as $ResType)
	{
		if (!isset($pricelist[$Element][$ResType]))
			continue;

		if ($Incremental)
			$cost[$ResType] = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
		else
			$cost[$ResType] = floor($pricelist[$Element][$ResType]);

		if ($withBonus)
		{
			if (in_array($Element, $reslist['build']))
				$cost[$ResType] = round($cost[$ResType] * $user->bonusValue('res_building'));
			elseif (in_array($Element, $reslist['tech']))
				$cost[$ResType] = round($cost[$ResType] * $user->bonusValue('res_research'));
			elseif (in_array($Element, $reslist['tech_f']))
				$cost[$ResType] = round($cost[$ResType] * $user->bonusValue('res_levelup'));
			elseif (in_array($Element, $reslist['fleet']))
				$cost[$ResType] = round($cost[$ResType] * $user->bonusValue('res_fleet'));
			elseif (in_array($Element, $reslist['defense']))
				$cost[$ResType] = round($cost[$ResType] * $user->bonusValue('res_defence'));
		}

		if ($ForDestroy)
			$cost[$ResType] = floor($cost[$ResType] / 2);
	}

	return $cost;
}

/**
 * @param int $Element
 * @param int $Level
 * @return string
 */
function GetNextProduction ($Element, $Level)
{
	$Res = array();

	$resFrom = app::$planetrow->getProductionLevel($Element, ($Level + 1));

	$Res['m'] = $resFrom['metal'];
	$Res['c'] = $resFrom['crystal'];
	$Res['d'] = $resFrom['deuterium'];
	$Res['e'] = $resFrom['energy'];

	$resTo = app::$planetrow->getProductionLevel($Element, $Level);

	$Res['m'] -= $resTo['metal'];
	$Res['c'] -= $resTo['crystal'];
	$Res['d'] -= $resTo['deuterium'];
	$Res['e'] -= $resTo['energy'];

	$text = '';

	if ($Res['m'] != 0)
		$text .= "<br>Металл: <span class=" . (($Res['m'] > 0) ? 'positive' : 'negative') . ">" . (($Res['m'] > 0) ? '+' : '') . $Res['m'] . "</span>";

	if ($Res['c'] != 0)
		$text .= "<br>Кристалл:  <span class=" . (($Res['c'] > 0) ? 'positive' : 'negative') . ">" . (($Res['c'] > 0) ? '+' : '') . $Res['c'] . "</span>";

	if ($Res['d'] != 0)
		$text .= "<br>Дейтерий:  <span class=" . (($Res['d'] > 0) ? 'positive' : 'negative') . ">" . (($Res['d'] > 0) ? '+' : '') . $Res['d'] . "</span>";

	if ($Res['e'] != 0)
		$text .= "<br>Энергия:  <span class=" . (($Res['e'] > 0) ? 'positive' : 'negative') . ">" . (($Res['e'] > 0) ? '+' : '') . $Res['e'] . "</span>";

	return $text;
}

function getFleetMissions ($fleetArray, $target = array(1, 1, 1, 1), $isYouPlanet = false, $isActivePlanet = false, $isAcs = false)
{
	$result = array();

	if ($target[2] == 16)
	{
		if (!(count($fleetArray) == 1 && isset($fleetArray[210])))
			$result[15] = _getText('type_mission', 15);
	}
	else
	{
		if ($target[3] == 2 && isset($fleetArray[209]))
			$result[8] = _getText('type_mission', 8); // Переработка
		elseif ($target[3] == 1 || $target[3] == 3 || $target[3] == 5)
		{
			if (isset($fleetArray[216]) && !$isActivePlanet && $target[3] == 1)
				$result[10] = _getText('type_mission', 10); // Создать базу

			if (isset($fleetArray[210]) && !$isYouPlanet)
				$result[6] = _getText('type_mission', 6); // Шпионаж

			if (isset($fleetArray[208]) && !$isActivePlanet)
				$result[7] = _getText('type_mission', 7); // Колонизировать

			if (!$isYouPlanet && $isActivePlanet)
				$result[1] = _getText('type_mission', 1); // Атаковать

			if ($isActivePlanet && !$isYouPlanet && !(count($fleetArray) == 1 && isset($fleetArray[210])))
				$result[5] = _getText('type_mission', 5); // Удерживать

			if (isset($fleetArray[202]) || isset($fleetArray[203]))
				$result[3] = _getText('type_mission', 3); // Транспорт

			if ($isYouPlanet)
				$result[4] = _getText('type_mission', 4); // Оставить

			if ($isAcs > 0 && $isActivePlanet)
				$result[2] = _getText('type_mission', 2); // Объединить

			if ($target[3] == 3 && isset($fleetArray[214]) && !$isYouPlanet && $isActivePlanet)
				$result[9] = _getText('type_mission', 9);
		}
	}

	return $result;
}

?>