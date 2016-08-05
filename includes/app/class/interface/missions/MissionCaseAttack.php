<?php

class MissionCaseAttack extends fleet_engine implements Mission
{
	function __construct($Fleet)
	{
			$this->_fleet = $Fleet;
	}

	public function TargetEvent()
	{
		global $resource, $CombatCaps;
		
		$TargetPlanet = new planet();
		$TargetPlanet->load_from_coords($this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet'], $this->_fleet['fleet_end_type']);

		if (!isset($TargetPlanet->data['id']) || !$TargetPlanet->data['id_owner'])
		{
			$this->ReturnFleet();

			return;
		}

		$CurrentUser = user::get()->getById($this->_fleet['fleet_owner'], Array('id', 'username', 'military_tech', 'defence_tech', 'shield_tech', 'laser_tech', 'ionic_tech', 'buster_tech', 'rpg_admiral', 'rpg_komandir'));

		if (!isset($CurrentUser['id']))
		{
			$this->ReturnFleet();

			return;
		}

		$TargetUser = new user;
		$TargetUser->load_from_id($TargetPlanet->data['id_owner']);

		if (!isset($TargetUser->data['id']))
		{
			$this->ReturnFleet();

			return;
		}

		$TargetPlanet->load_user_info($TargetUser);

		// =============================================================================
		$TargetPlanet->PlanetResourceUpdate($this->_fleet['fleet_start_time']);
		// =============================================================================

		$attackUsers = array();
		$attackFleets = array();

		if ($this->_fleet['fleet_group'] != 0)
		{
			$fleets = db::query('SELECT * FROM game_fleets WHERE fleet_group = ' . $this->_fleet['fleet_group']);

			while ($fleet = db::fetch_assoc($fleets))
			{
				$fleetData = unserializeFleet($fleet['fleet_array']);

				if (!count($fleetData) || (count($fleetData) == 1 && isset($fleetData[210])))
				{
					if ($fleet['fleet_mission'] == 1)
						$this->ReturnFleet(array(), $fleet['fleet_id']);

					continue;
				}

				$attackUsers[$fleet['fleet_id']]['fleet'] = array($fleet['fleet_start_galaxy'], $fleet['fleet_start_system'], $fleet['fleet_start_planet']);

				$a_user = db::query('SELECT `id`, `username`, `military_tech`, `defence_tech`, `shield_tech`, `laser_tech`, `ionic_tech`, `buster_tech`, `rpg_admiral`, `rpg_komandir` FROM game_users WHERE id = ' . $fleet['fleet_owner'], true);

				$attackUsers[$fleet['fleet_id']]['tech'] = $a_user;
				$attackUsers[$fleet['fleet_id']]['flvl'] = array();
				$attackUsers[$fleet['fleet_id']]['username'] = $a_user['username'];

				if ($a_user['rpg_komandir'] > time())
				{
					$attackUsers[$fleet['fleet_id']]['tech']['military_tech'] += 2;
					$attackUsers[$fleet['fleet_id']]['tech']['defence_tech'] += 2;
					$attackUsers[$fleet['fleet_id']]['tech']['shield_tech'] += 2;
				}

				$attackFleets[$fleet['fleet_id']] = array();

				foreach ($fleetData as $shipId => $shipArr)
				{
					if ($shipId < 100 || $shipId > 300)
						continue;

					$attackFleets[$fleet['fleet_id']][$shipId] = $shipArr['cnt'];
					$attackUsers[$fleet['fleet_id']]['flvl'][$shipId] = $shipArr['lvl'];
				}
			}
		}
		else
		{
			$attackUsers[$this->_fleet['fleet_id']]['fleet'] = array($this->_fleet['fleet_start_galaxy'], $this->_fleet['fleet_start_system'], $this->_fleet['fleet_start_planet']);
			$attackUsers[$this->_fleet['fleet_id']]['tech'] = $CurrentUser;
			$attackUsers[$this->_fleet['fleet_id']]['flvl'] = array();
			$attackUsers[$this->_fleet['fleet_id']]['username'] = $CurrentUser['username'];

			if ($CurrentUser['rpg_komandir'] > time())
			{
				$attackUsers[$this->_fleet['fleet_id']]['tech']['military_tech'] += 2;
				$attackUsers[$this->_fleet['fleet_id']]['tech']['defence_tech'] += 2;
				$attackUsers[$this->_fleet['fleet_id']]['tech']['shield_tech'] += 2;
			}

			$attackFleets[$this->_fleet['fleet_id']] = array();

			$fleetData = unserializeFleet($this->_fleet['fleet_array']);

			foreach ($fleetData as $shipId => $shipArr)
			{
				if ($shipId < 100 || $shipId > 300)
					continue;

				$attackFleets[$this->_fleet['fleet_id']][$shipId] = $shipArr['cnt'];
				$attackUsers[$this->_fleet['fleet_id']]['flvl'][$shipId] = $shipArr['lvl'];
			}
		}

		$defenseUsers = array();
		$defenseFleets = array();

		$def = db::query('SELECT * FROM game_fleets WHERE `fleet_end_galaxy` = ' . $this->_fleet['fleet_end_galaxy'] . ' AND `fleet_end_system` = ' . $this->_fleet['fleet_end_system'] . ' AND `fleet_end_type` = ' . $this->_fleet['fleet_end_type'] . ' AND `fleet_end_planet` = ' . $this->_fleet['fleet_end_planet'] . ' AND fleet_mess = 3');

		while ($defRow = db::fetch_assoc($def))
		{
			$fleetData = unserializeFleet($defRow['fleet_array']);

			if (!count($fleetData) || (count($fleetData) == 1 && isset($fleetData[210])))
			{
				if ($defRow['fleet_mission'] == 1)
					$this->ReturnFleet(array(), $defRow['fleet_id']);

				continue;
			}

			$defenseUsers[$defRow['fleet_id']]['fleet'] = array($this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet']);

			$a_user = db::query('SELECT `id`, `username`, `military_tech`, `defence_tech`, `shield_tech`, `laser_tech`, `ionic_tech`, `buster_tech`, `rpg_admiral`, `rpg_komandir` FROM game_users WHERE id = ' . $defRow['fleet_owner'], true);

			$defenseUsers[$defRow['fleet_id']]['tech'] = $a_user;
			$defenseUsers[$defRow['fleet_id']]['flvl'] = array();
			$defenseUsers[$defRow['fleet_id']]['username'] = $a_user['username'];

			if ($a_user['rpg_komandir'] > time())
			{
				$defenseUsers[$defRow['fleet_id']]['tech']['military_tech'] += 2;
				$defenseUsers[$defRow['fleet_id']]['tech']['defence_tech'] += 2;
				$defenseUsers[$defRow['fleet_id']]['tech']['shield_tech'] += 2;
			}

			$defenseFleets[$defRow['fleet_id']] = array();

			foreach ($fleetData as $shipId => $shipArr)
			{
				if ($shipId < 100 || $shipId > 300)
					continue;

				$defenseFleets[$defRow['fleet_id']][$shipId] = $shipArr['cnt'];
				$defenseUsers[$defRow['fleet_id']]['flvl'][$shipId] = $shipArr['lvl'];
			}
		}

		$defenseUsers[0]['fleet'] = array($this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet']);
		$defenseUsers[0]['flvl'] = array();
		$defenseUsers[0]['username'] = $TargetUser->data['username'];
		$defenseUsers[0]['tech'] = array('id' => $TargetUser->data['id'], 'military_tech' => $TargetUser->data['military_tech'], 'shield_tech' => $TargetUser->data['shield_tech'], 'defence_tech' => $TargetUser->data['defence_tech'], 'laser_tech' => $TargetUser->data['laser_tech'], 'ionic_tech' => $TargetUser->data['ionic_tech'], 'buster_tech' => $TargetUser->data['buster_tech']);

		if ($TargetUser->data['rpg_komandir'] > time())
		{
			$defenseUsers[0]['tech']['military_tech'] += 2;
			$defenseUsers[0]['tech']['defence_tech'] += 2;
			$defenseUsers[0]['tech']['shield_tech'] += 2;
		}

		for ($i = 200; $i < 500; $i++)
		{
			if (isset($resource[$i]) && isset($TargetPlanet->data[$resource[$i]]))
			{
				$defenseFleets[0][$i] = $TargetPlanet->data[$resource[$i]];

				if (isset($TargetUser->data['fleet_' . $i]) && $i < 300)
					$defenseUsers[0]['flvl'][$i] = $TargetUser->data['fleet_' . $i];
				else
					$defenseUsers[0]['flvl'][$i] = 0;
			}
		}

		include_once(ROOT_DIR.APP_PATH.'functions/calculateAttack.php');

		$result = calculateAttack($attackFleets, $defenseFleets, $attackUsers, $defenseUsers, $TargetUser->data['rpg_ingenieur'], $this->_fleet['raunds']);

		$steal = array('metal' => 0, 'crystal' => 0, 'deuterium' => 0);

		if ($result['won'] == 1)
		{
			$max_resources = 0;
			$max_fleet_res = array();

			foreach ($attackFleets AS $fleet => $arr)
			{
				$max_fleet_res[$fleet] = 0;

				foreach ($arr as $Element => $amount)
				{
					if ($Element == 210)
						continue;

					if (isset($attackUsers[$fleet]['flvl'][$Element]) && isset($CombatCaps[$Element]['power_consumption']) && $CombatCaps[$Element]['power_consumption'] > 0)
						$capacity = $CombatCaps[$Element]['capacity'] * $amount * (1 + $attackUsers[$fleet]['flvl'][$Element] * ($CombatCaps[$Element]['power_consumption'] / 100));
					else
						$capacity = $CombatCaps[$Element]['capacity'] * $amount;

					$max_resources += $capacity;
					$max_fleet_res[$fleet] += $capacity;
				}
			}

			$res_correction = $max_resources;
			$res_procent = array();

			if ($max_resources > 0)
			{
				$metal 		= $TargetPlanet->data['metal'] / 2;
				$crystal 	= $TargetPlanet->data['crystal'] / 2;
				$deuter 	= $TargetPlanet->data['deuterium'] / 2;

				$steal['metal'] 	= min($max_resources / 3, $metal);
				$max_resources -= $steal['metal'];

				$steal['crystal'] 	= min($max_resources / 2, $crystal);
				$max_resources -= $steal['crystal'];

				$steal['deuterium'] = min($max_resources, $deuter);
				$max_resources -= $steal['deuterium'];

				if ($max_resources > 0)
				{
					$oldStealMetal = $steal['metal'];

					$steal['metal'] += min(($max_resources / 2), ($metal - $steal['metal']));
					$max_resources -= $steal['metal'] - $oldStealMetal;

					$steal['crystal'] += min($max_resources, ($crystal - $steal['crystal']));
				}

				foreach ($max_fleet_res AS $id => $res)
				{
					$res_procent[$id] = $max_fleet_res[$id] / $res_correction;
				}
			}

			if ($steal['metal'] < 0)
				$steal['metal'] = 0;
			if ($steal['crystal'] < 0)
				$steal['crystal'] = 0;
			if ($steal['deuterium'] < 0)
				$steal['deuterium'] = 0;

			$steal = array_map('round', $steal);
		}

		$totalDebree = $result['debree']['def'][0] + $result['debree']['def'][1] + $result['debree']['att'][0] + $result['debree']['att'][1];

		if ($totalDebree > 0)
			db::query('UPDATE game_planets SET debris_metal = debris_metal + ' . ($result['debree']['att'][0] + $result['debree']['def'][0]) . ' , debris_crystal = debris_crystal + ' . ($result['debree']['att'][1] + $result['debree']['def'][1]) . ' WHERE galaxy = ' . $TargetPlanet->data['galaxy'] . ' AND system = ' . $TargetPlanet->data['system'] . ' AND planet = ' . $TargetPlanet->data['planet'] . ' AND planet_type != 3;');

		foreach ($attackFleets as $fleetID => $attacker)
		{
			$fleetArray = '';
			$totalCount = 0;

			foreach ($attacker as $element => $amount)
			{
				if ($amount)
					$fleetArray .= $element . ',' . $amount . '!0;';
				$totalCount += $amount;
			}

			if ($totalCount <= 0)
			{
				$this->KillFleet($fleetID);
			}
			else
			{
				$query = 'UPDATE game_fleets SET fleet_array="' . substr($fleetArray, 0, -1) . '", fleet_time = fleet_end_time, fleet_mess=1, fleet_group = 0, won=' . $result['won'] . '';

				if ($result['won'] == 1 && ($steal['metal'] > 0 || $steal['crystal'] > 0 || $steal['deuterium'] > 0))
				{
					if (isset($res_procent[$fleetID]))
					{
						$query .= ', `fleet_resource_metal` = `fleet_resource_metal` + ' . round($res_procent[$fleetID] * $steal['metal']) . ', ';
						$query .= '`fleet_resource_crystal` = `fleet_resource_crystal` +' . round($res_procent[$fleetID] * $steal['crystal']) . ', ';
						$query .= '`fleet_resource_deuterium` = `fleet_resource_deuterium` +' . round($res_procent[$fleetID] * $steal['deuterium']) . '';
					}
				}
				$query .= ' WHERE fleet_id=' . $fleetID;
				db::query($query);
			}
		}

		foreach ($defenseFleets as $fleetID => $defender)
		{
			if ($fleetID != 0)
			{
				$fleetArray = '';
				$totalCount = 0;

				foreach ($defender as $element => $amount)
				{
					if ($amount)
						$fleetArray .= $element . ',' . $amount . '!0;';
					$totalCount += $amount;
				}

				if ($totalCount <= 0)
					$this->KillFleet($fleetID);
				else
				{
					db::query('UPDATE game_fleets SET fleet_array="' . $fleetArray . '", fleet_time = fleet_end_time WHERE fleet_id=' . $fleetID);
				}
			}
			else
			{
				$fleetArray = '';
				for ($i = 200; $i < 500; $i++)
				{
					if (isset($resource[$i]) && isset($TargetPlanet->data[$resource[$i]]))
					{
						if (isset($defender[$i]))
							$fleetArray .= '`' . $resource[$i] . '` = ' . $defender[$i] . ', ';
						elseif ($TargetPlanet->data[$resource[$i]] != 0)
							$fleetArray .= '`' . $resource[$i] . '`= 0, ';
					}
				}
				db::query('UPDATE game_planets SET ' . $fleetArray . 'metal=metal-' . $steal['metal'] . ', crystal=crystal-' . $steal['crystal'] . ', deuterium=deuterium-' . $steal['deuterium'] . ' WHERE id=' . $TargetPlanet->data['id']);
			}
		}

		$FleetDebris = $result['debree']['att'][0] + $result['debree']['def'][0] + $result['debree']['att'][1] + $result['debree']['def'][1];

		$MoonChance = round($FleetDebris / 100000);

		$warPoints = $MoonChance;

		$MoonChance = min($MoonChance, core::getConfig('maxMoonChance', 20));

		if ($TargetPlanet->data['planet_type'] != 1)
			$MoonChance = 0;

		$UserChance = mt_rand(1, 100);

		if ($this->_fleet['fleet_end_type'] == 5)
			$UserChance = 0;

		if ($TargetPlanet->data['parent_planet'] == 0 && $UserChance && $UserChance <= $MoonChance)
		{
			$TargetPlanetName = system::CreateOneMoonRecord($this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet'], $TargetPlanet->data['id_owner'], $MoonChance);

			if ($TargetPlanetName)
				$GottenMoon = sprintf(_getText('sys_moonbuilt'), $this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet']);
			else
				$GottenMoon = 'Предпринята попытка образования луны, но данные координаты уже заняты другой луной';
		}
		else
			$GottenMoon = '';

		// Очки военного опыта
		$AddWarPoints = ($result['won'] != 2) ? ($warPoints * 4) : 0;
		// Сборка массива ID участников боя
		$FleetsUsers = array();
		$str = "";

		$tmp = array();

		foreach ($attackUsers AS $info)
		{
			if (!in_array($info['tech']['id'], $tmp))
			{
				$tmp[] = $info['tech']['id'];
			}
		}

		$realAttackersUsers = count($tmp);
		unset($tmp);

		foreach ($attackUsers AS $info)
		{
			if (!in_array($info['tech']['id'], $FleetsUsers))
			{
				$FleetsUsers[] = $info['tech']['id'];

				if ($this->_fleet['fleet_mission'] != 6)
				{
					if ($result['won'] == 1)
						$str = ", `raids_win` =  `raids_win` + 1";
					elseif ($result['won'] == 2)
						$str = ", `raids_lose` =  `raids_lose` + 1";

					if ($AddWarPoints > 0)
						$str .= ", `xpraid` = `xpraid` + " . ceil($AddWarPoints / $realAttackersUsers) . "";

					db::query("UPDATE game_users SET `raids` = `raids` + 1" . $str . " WHERE id = '" . $info['tech']['id'] . "';");
				}
			}
		}
		foreach ($defenseUsers AS $info)
		{
			if (!in_array($info['tech']['id'], $FleetsUsers))
				$FleetsUsers[] = $info['tech']['id'];
		}

		// Упаковка в строку
		$users = json_encode($FleetsUsers);
		// Сборка боевого доклада
		$results = array($result, $attackUsers, $defenseUsers, $steal, $MoonChance, $GottenMoon);
		// Упаковка в строку
		$raport = json_encode($results);
		// Уничтожен в первой волне
		if (count($result['rw']) <= 2 && $result['won'] == 2)
			$no_contact = 1;
		else
			$no_contact = 0;
		// Добавление в базу
		db::query("INSERT INTO game_rw SET `time` = " . time() . ", `id_users` = '" . $users . "', `no_contact` = '" . $no_contact . "', `raport` = '" . addslashes($raport) . "';");
		// Ключи авторизации доклада
		$ids = db::insert_id();
		$key = md5('xnovasuka' . $ids);

		if ($this->_fleet['fleet_group'] != 0)
		{
			db::query("DELETE FROM game_aks WHERE id = " . $this->_fleet['fleet_group'] . ";");
			db::query("DELETE FROM game_aks_user WHERE aks_id = " . $this->_fleet['fleet_group'] . ";");
		}

		$lost = $result['lost']['att'] + $result['lost']['def'];

		if ($lost >= core::getConfig('hallPoints', 1000000))
		{
			$title_1 = '';
			$title_2 = '';

			$sab = 0;

			$UserList = array();

			foreach ($attackUsers AS $info)
			{
				if (!in_array($info['username'], $UserList))
					$UserList[] = $info['username'];
			}

			if (count($UserList) > 1)
				$sab = 1;

			foreach ($UserList AS $info)
			{
				if ($title_1 != '')
					$title_1 .= ',';

				$title_1 .= $info;
			}

			$UserList = array();

			foreach ($defenseUsers AS $info)
			{
				if (!in_array($info['username'], $UserList))
					$UserList[] = $info['username'];
			}

			if (count($UserList) > 1)
				$sab = 1;

			foreach ($UserList AS $info)
			{
				if ($title_2 != '')
					$title_2 .= ',';

				$title_2 .= $info;
			}

			$title = '' . $title_1 . ' vs ' . $title_2 . ' (П: ' . strings::pretty_number($lost) . ')';

			db::query("INSERT INTO game_savelog (`user`, `title`, `log`) VALUES (0, '" . $title . "', '" . addslashes($raport) . "')");
			$id = db::insert_id();
			db::query("INSERT INTO game_hall (title, debris, time, won, sab, log) VALUES ('" . $title . "', " . floor($lost / 1000) . ", " . time() . ", " . $result['won'] . ", " . $sab . ", " . $id . ")");
		}

		$raport = "<center><a ".(core::getConfig('openRaportInNewWindow', 0) == 1 ? 'target="_blank"' : '')." href=\"?set=rw&r=" . $ids . "&amp;k=" . $key . "\">";

		if ($result['won'] == 1)
			$raport .= "<font color=\"green\">";
		elseif ($result['won'] == 0)
			$raport .= "<font color=\"orange\">";
		elseif ($result['won'] == 2)
			$raport .= "<font color=\"red\">";

		$raport .= _getText('sys_mess_attack_report') . " [" . $this->_fleet['fleet_end_galaxy'] . ":" . $this->_fleet['fleet_end_system'] . ":" . $this->_fleet['fleet_end_planet'] . "]</font></a>";

		$raport2 = $raport . '<br><br><font color=\'red\'>' . _getText('sys_perte_attaquant') . ': ' . strings::pretty_number($result['lost']['att']) . '</font><font color=\'green\'>   ' . _getText('sys_perte_defenseur') . ': ' . strings::pretty_number($result['lost']['def']) . '</font><br>';
		$raport2 .= _getText('sys_gain') . ' м: <font color=\'#adaead\'>' . strings::pretty_number($steal['metal']) . '</font>, к: <font color=\'#ef51ef\'>' . strings::pretty_number($steal['crystal']) . '</font>, д: <font color=\'#f77542\'>' . strings::pretty_number($steal['deuterium']) . '</font><br>';
		$raport2 .= _getText('sys_debris') . ' м: <font color=\'#adaead\'>' . strings::pretty_number($result['debree']['att'][0] + $result['debree']['def'][0]) . '</font>, к: <font color=\'#ef51ef\'>' . strings::pretty_number($result['debree']['att'][1] + $result['debree']['def'][1]) . '</font></center>';

		$UserList = array();

		foreach ($attackUsers AS $info)
		{
			if (!in_array($info['tech']['id'], $UserList))
				$UserList[] = $info['tech']['id'];
		}

		foreach ($UserList AS $info)
		{
			user::get()->sendMessage($info, 0, time(), 3, 'Боевой доклад', $raport2);
		}

		$UserList = array();

		foreach ($defenseUsers AS $info)
		{
			if (!in_array($info['tech']['id'], $UserList))
				$UserList[] = $info['tech']['id'];
		}

		foreach ($UserList AS $info)
		{
			user::get()->sendMessage($info, 0, time(), 3, 'Боевой доклад', $raport);
		}

		db::query("INSERT INTO game_log_attack (uid, time, planet_start, planet_end, fleet, battle_log) VALUES (" . $this->_fleet['fleet_owner'] . ", " . time() . ", 0, " . $TargetPlanet->data['id'] . ", '" . $this->_fleet['fleet_array'] . "', " . $ids . ")");
	}

	public function EndStayEvent()
	{
		return;
	}

	public function ReturnEvent()
	{
		$this->RestoreFleetToPlanet();
		$this->KillFleet();
	}
}

?>