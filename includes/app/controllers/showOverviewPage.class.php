<?php

class showOverviewPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();

		strings::includeLang('overview');

		app::loadPlanet();
	}

	public function renameplanet ()
	{
		$parse = array();
		$parse['planet_id'] = app::$planetrow->data['id'];
		$parse['galaxy_galaxy'] = app::$planetrow->data['galaxy'];
		$parse['galaxy_system'] = app::$planetrow->data['system'];
		$parse['galaxy_planet'] = app::$planetrow->data['planet'];
		$parse['planet_name'] = app::$planetrow->data['name'];

		$parse['images'] = array
		(
			'trocken' => 20,
			'wuesten' => 4,
			'dschjungel' => 19,
			'normaltemp' => 15,
			'gas' => 16,
			'wasser' => 18,
			'eis' => 20
		);

		$parse['type'] = '';

		foreach ($parse['images'] AS $type => $max)
		{
			if (strpos(app::$planetrow->data['image'], $type) !== false)
				$parse['type'] = $type;
		}

		if (isset($_POST['action']) && $_POST['action'] == _getText('namer'))
		{
			$UserPlanet = $_POST['newname'];

			if (trim($UserPlanet) != "")
			{
				if (preg_match("/^[a-zA-Zа-яА-Я0-9_\.\,\-\!\?\*\ ]+$/u", $UserPlanet))
				{
					if (mb_strlen($UserPlanet, 'UTF-8') > 1 && mb_strlen($UserPlanet, 'UTF-8') < 20)
					{
						$newname = db::escape_string(strip_tags(trim($UserPlanet)));

						app::$planetrow->data['name'] = $newname;
						$parse['planet_name'] = app::$planetrow->data['name'];

						db::query("UPDATE game_planets SET `name` = '" . $newname . "' WHERE `id` = '" . app::$user->data['current_planet'] . "' LIMIT 1;");

						if (isset($_SESSION['fleet_shortcut']))
							unset($_SESSION['fleet_shortcut']);
					}
					else
						$this->message('Введённо слишком длинное или короткое имя планеты', 'Ошибка', '?set=overview&mode=renameplanet', 5);
				}
				else
					$this->message('Введённое имя содержит недопустимые символы', 'Ошибка', '?set=overview&mode=renameplanet', 5);
			}
		}
		elseif (isset($_POST['action']) && $_POST['action'] == _getText('colony_abandon'))
		{
			$parse['number_1'] 		= mt_rand(1, 100);
			$parse['number_2'] 		= mt_rand(1, 100);
			$parse['number_3'] 		= mt_rand(1, 100);
			$parse['number_check'] 	= $parse['number_1'] + $parse['number_2'] * $parse['number_3'];

			$this->setTemplate('planet_delete');
			$this->set('parse', $parse);

			$this->display('', 'Покинуть колонию', false);

		}
		elseif (isset($_POST['action']) && isset($_POST['image']))
		{
			if (app::$user->data['credits'] < 1)
				$this->message('Недостаточно кредитов', 'Ошибка', '?set=overview&mode=renameplanet');

			$image = intval($_POST['image']);

			if ($image > 0 && $image <= $parse['images'][$parse['type']])
			{
				sql::build()->update('game_planets')->setField('image', $parse['type'].'planet'.($image < 10 ? '0' : '').$image)->where('id', '=', app::$planetrow->data['id'])->execute();
				sql::build()->update('game_users')->setField('-credits', 1)->where('id', '=', app::$user->getId())->execute();

				request::redirectTo('?set=overview');
			}
			else
				$this->message('Недостаточно читерских навыков', 'Ошибка', '?set=overview&mode=renameplanet');
		}
		elseif (isset($_POST['kolonieloeschen']) && $_POST['deleteid'] == app::$user->data['current_planet'])
		{
			if (app::$user->data['id'] != app::$planetrow->data['id_owner'])
				$this->message("Удалить планету может только владелец", _getText('colony_abandon'), '?set=overview&mode=renameplanet');
			elseif (md5(trim($_POST['pw'])) == $_POST["password"] && app::$user->data['id_planet'] != app::$user->data['current_planet'])
			{
				$checkFleets = db::query("SELECT COUNT(*) AS num FROM game_fleets WHERE (fleet_start_galaxy = " . app::$planetrow->data['galaxy'] . " AND fleet_start_system = " . app::$planetrow->data['system'] . " AND fleet_start_planet = " . app::$planetrow->data['planet'] . " AND fleet_start_type = " . app::$planetrow->data['planet_type'] . ") OR (fleet_end_galaxy = " . app::$planetrow->data['galaxy'] . " AND fleet_end_system = " . app::$planetrow->data['system'] . " AND fleet_end_planet = " . app::$planetrow->data['planet'] . " AND fleet_end_type = " . app::$planetrow->data['planet_type'] . ")", true);

				if ($checkFleets['num'] > 0)
					$this->message('Нельзя удалять планету если с/на неё летит флот', _getText('colony_abandon'), '?set=overview&mode=renameplanet');
				else
				{
					$destruyed = time() + 60 * 60 * 24;

					db::query("UPDATE game_planets SET `destruyed` = '" . $destruyed . "', `id_owner` = '0' WHERE `id` = '" . app::$user->data['current_planet'] . "' LIMIT 1;");
					db::query("UPDATE game_users SET `current_planet` = `id_planet` WHERE `id` = '" . app::$user->data['id'] . "' LIMIT 1");

					if (app::$planetrow->data['parent_planet'] != 0)
						db::query("UPDATE game_planets SET `destruyed` = '" . $destruyed . "', `id_owner` = '0' WHERE `id` = '" . app::$planetrow->data['parent_planet'] . "' LIMIT 1;");

					if (isset($_SESSION['fleet_shortcut']))
						unset($_SESSION['fleet_shortcut']);

					$this->message(_getText('deletemessage_ok'), _getText('colony_abandon'), '?set=overview&mode=renameplanet');
				}

			}
			elseif (app::$user->data['id_planet'] == app::$user->data["current_planet"])
				$this->message(_getText('deletemessage_wrong'), _getText('colony_abandon'), '?set=overview&mode=renameplanet');
			else
				$this->message(_getText('deletemessage_fail'), _getText('colony_abandon'), '?set=overview&mode=renameplanet');
		}

		$this->setTemplate('planet_rename');
		$this->set('parse', $parse);

		$this->display('', 'Переименовать планету', false);
	}

	public function bonus ()
	{
		if (app::$user->data['bonus'] < time())
		{
			$multi = (app::$user->data['bonus_multi'] < 50) ? (app::$user->data['bonus_multi'] + 1) : 50;

			if (app::$user->data['bonus'] < (time() - 86400))
				$multi = 1;

			$add = $multi * 500 * system::getGameSpeed();

			db::query("UPDATE game_planets SET metal = metal + " . $add . ", crystal = crystal + " . $add . ", deuterium = deuterium + " . $add . " WHERE id = " . app::$user->data['current_planet'] . ";");

			$arUpdate = array
			(
				'bonus' => (time() + 86400),
				'bonus_multi' => $multi
			);

			if (app::$user->data['bonus_multi'] > 1)
				$arUpdate['+credits'] = 1;

			sql::build()->update('game_users')->set($arUpdate)->where('id', '=', app::$user->data['id'])->execute();

			$this->message('Спасибо за поддержку!<br>Вы получили в качестве бонуса по <b>' . $add . '</b> Металла, Кристаллов и Дейтерия'.(isset($arUpdate['+credits']) ? ', а также 1 кредит.' : '').'', 'Ежедневный бонус', '?set=overview', 2);
		}
		else
			$this->message('Ошибочка вышла, сорри :(');
	}

	public function show ()
	{
		global $resource, $reslist;

		$parse = array();

		$XpMinierUp = pow(app::$user->data['lvl_minier'], 3);
		$XpRaidUp = pow(app::$user->data['lvl_raid'], 2);
		$XpMinier = app::$user->data['xpminier'];
		$XPRaid = app::$user->data['xpraid'];

		$LvlUpMinier = app::$user->data['lvl_minier'] + 1;
		$LvlUpRaid = app::$user->data['lvl_raid'] + 1;

		$up = 0;
		$HaveNewLevel = "";

		if ($XpMinier >= $XpMinierUp && app::$user->data['lvl_minier'] < 100)
		{
			$up = 10;
			db::query("UPDATE game_users SET `lvl_minier` = '" . $LvlUpMinier . "', `credits` = `credits` + " . $up . ", `xpminier` = `xpminier` - " . $XpMinierUp . " WHERE `id` = '" . app::$user->data['id'] . "';");
			$HaveNewLevel .= "<tr><th colspan=4><a href=?set=officier>Получен новый промышленный уровень</a></th></tr>";
			app::$user->data['lvl_minier'] = $LvlUpMinier;
			app::$user->data['xpminier'] -= $XpMinierUp;
		}
		if ($XPRaid >= $XpRaidUp && app::$user->data['lvl_raid'] < 100)
		{
			$up = 10;
			db::query("UPDATE game_users SET `lvl_raid` = '" . $LvlUpRaid . "', `credits` = `credits` + " . $up . ", `xpraid` = `xpraid` - " . $XpRaidUp . " WHERE `id` = '" . app::$user->data['id'] . "';");
			$HaveNewLevel .= "<tr><th colspan=4><a href=?set=officier>Получен новый военный уровень</a></th></tr>";
			app::$user->data['lvl_raid'] = $LvlUpRaid;
			app::$user->data['xpraid'] -= $XpRaidUp;
		}

		if ($up != 0)
		{
			db::query("INSERT INTO game_log_credits (uid, time, credits, type) VALUES (" . app::$user->data['id'] . ", " . time() . ", " . $up . ", 4)");

			$ref_array = db::query("SELECT u_id FROM game_refs WHERE r_id = " . app::$user->data['id'] . "", true);

			if (isset($ref_array['u_id']))
			{
				db::query("UPDATE game_users SET credits = credits + " . round($up / 2) . " WHERE id = " . $ref_array['u_id'] . "");
				db::query("INSERT INTO game_log_credits (uid, time, credits, type) VALUES (" . $ref_array['u_id'] . ", " . time() . ", " . round($up / 2) . ", 3)");
			}
		}

		$fleets = array_merge
		(
			db::extractResult(db::query("SELECT * FROM game_fleets WHERE `fleet_owner` = " . app::$user->data['id'] . "")),
			db::extractResult(db::query("SELECT * FROM game_fleets WHERE `fleet_target_owner` = " . app::$user->data['id'] . ""))
		);
		
		$Record = 0;
		$fpage = array();
		$aks = array();

		foreach ($fleets AS $FleetRow)
		{
			$Record++;

			if ($FleetRow['fleet_owner'] == app::$user->data['id'])
			{
				$StartTime = $FleetRow['fleet_start_time'];
				$StayTime = $FleetRow['fleet_end_stay'];
				$EndTime = $FleetRow['fleet_end_time'];

				if ($StartTime > time())
				{
					$fpage[$StartTime][$FleetRow['fleet_id']] = BuildFleetEventTable($FleetRow, 0, true, "fs", $Record);
				}

				if ($StayTime > time())
				{
					$fpage[$StayTime][$FleetRow['fleet_id']] = BuildFleetEventTable($FleetRow, 1, true, "ft", $Record);
				}

				if (!($FleetRow['fleet_mission'] == 7 && $FleetRow['fleet_mess'] == 0))
				{
					if (($EndTime > time() AND $FleetRow['fleet_mission'] != 4) OR ($FleetRow['fleet_mess'] == 1 AND $FleetRow['fleet_mission'] == 4))
					{
						$fpage[$EndTime][$FleetRow['fleet_id']] = BuildFleetEventTable($FleetRow, 2, true, "fe", $Record);
					}
				}

				if ($FleetRow['fleet_group'] != 0 && !in_array($FleetRow['fleet_group'], $aks))
				{
					$AKSFleets = db::query("SELECT * FROM game_fleets WHERE fleet_group = " . $FleetRow['fleet_group'] . " AND `fleet_owner` != '" . app::$user->data['id'] . "' AND fleet_mess = 0;");

					while ($AKFleet = db::fetch_assoc($AKSFleets))
					{
						$Record++;
						$fpage[$FleetRow['fleet_start_time']][$AKFleet['fleet_id']] = BuildFleetEventTable($AKFleet, 0, false, "fs", $Record);
					}

					$aks[] = $FleetRow['fleet_group'];
				}

			}
			elseif ($FleetRow['fleet_mission'] != 8)
			{
				$Record++;
				$StartTime = $FleetRow['fleet_start_time'];
				$StayTime = $FleetRow['fleet_end_stay'];

				if ($StartTime > time())
				{
					$fpage[$StartTime][$FleetRow['fleet_id']] = BuildFleetEventTable($FleetRow, 0, false, "ofs", $Record);
				}
				if ($FleetRow['fleet_mission'] == 5 && $StayTime > time())
				{
					$fpage[$StayTime][$FleetRow['fleet_id']] = BuildFleetEventTable($FleetRow, 1, false, "oft", $Record);
				}
			}
		}
		
		/*if (user::get()->IsAdmin()) 
		{
			$FleetRow = array
			(
				'fleet_owner' => 1,
				'fleet_owner_name' => 'Призрак',
				'fleet_mission' => 9,
				'fleet_array' => '214,9999!0',
				'fleet_start_time' => mktime(23, 59, 59),
				'fleet_start_galaxy' => 0,
				'fleet_start_system' => 0,
				'fleet_start_planet' => 0,
				'fleet_start_type' => 1,
				'fleet_end_time' => mktime(23, 59, 59),
				'fleet_end_stay' => 0,
				'fleet_end_galaxy' 		=> app::$planetrow->data['galaxy'],
				'fleet_end_system' 		=> app::$planetrow->data['system'],
				'fleet_end_planet' 		=> app::$planetrow->data['planet'],
				'fleet_end_type' 		=> app::$planetrow->data['planet_type'],
				'fleet_resource_metal' 	=> 0,
				'fleet_resource_crystal' 	=> 0,
				'fleet_resource_deuterium' 	=> 0,
				'fleet_target_owner' 	=> app::$user->data['id'],
				'fleet_target_owner_name' 	=> app::$planetrow->data['name'],
				'fleet_group' 			=> 0,
				'raunds' 				=> 6,
				'start_time' 			=> time(),
				'fleet_time' 			=> 0
				
			);
		
			$fpage[mktime(23, 0, 0)][2] = BuildFleetEventTable($FleetRow, 0, false, "ofs", ($Record + 1));
		}*/

		if (app::$planetrow->data['parent_planet'] != 0 && app::$planetrow->data['planet_type'] != '3' && app::$planetrow->data['id'])
		{
			$lune = db::query("SELECT `id`, `name`, `image`, `destruyed` FROM game_planets WHERE galaxy = " . app::$planetrow->data['galaxy'] . " AND system = " . app::$planetrow->data['system'] . " AND planet = " . app::$planetrow->data['planet'] . " AND planet_type='3';", true);
			$parse['moon_img'] = "<a href=\"?set=overview&amp;cp=" . $lune['id'] . "&amp;re=0\" title=\"" . $lune['name'] . "\"><img src=\"" . DPATH . "planeten/" . $lune['image'] . ".jpg\" height=\"50\" width=\"50\"></a>";
			$parse['moon'] = ($lune['destruyed'] == 0) ? $lune['name'] : 'Фантом';
		}
		else
		{
			$parse['moon_img'] = "";
			$parse['moon'] = "";
		}

		if (core::getConfig('overviewListView', 0) == 0)
			$QryPlanets = app::$user->getPlanetListSortQuery();
		else
			$QryPlanets = '';

		$planets_query = db::query("SELECT * FROM game_planets WHERE id_owner='" . app::$user->data['id'] . "' AND `planet_type` != '3' AND id != " . app::$user->data["current_planet"] . " " . $QryPlanets . ";");

		$build_list = array();
		$build = new planet();
		$build->load_user_info(app::$user);

		$AllPlanets = array();

		while ($UserPlanet = db::fetch_assoc($planets_query))
		{
			if (core::getConfig('overviewListView', 0) == 0)
			{
				$AllPlanets[] = array
				(
					'id' => $UserPlanet['id'],
					'name' =>$UserPlanet['name'],
					'image' => $UserPlanet['image']
				);
			}

			if ($UserPlanet['b_building'] != 0)
			{
				$build->load_from_array($UserPlanet);
				$build->UpdatePlanetBatimentQueueList();

				if ($UserPlanet['b_building'] != 0)
				{
					$QueueArray = explode(";", $UserPlanet['b_building_id']);

					foreach ($QueueArray AS $Queue)
					{
						$CurrBuild = explode(",", $Queue);
						$build_list[$UserPlanet['b_building']][] = array($CurrBuild[3], "<a href=\"?set=buildings&amp;cp=" . $UserPlanet['id'] . "&amp;re=0\" style=\"color:#33ff33;\">" . $UserPlanet['name'] . "</a>: </span><span class=\"holding colony\"> " . _getText('tech', $CurrBuild[0]) . ' (' . ($CurrBuild[1] - 1) . ' -> ' . $CurrBuild[1] . ')');
					}
				}
			}

			if ($UserPlanet['b_tech'] != 0)
				$build_list[$UserPlanet['b_tech']][] = array($UserPlanet['b_tech'], "<a href=\"?set=buildings&amp;mode=research" . (($UserPlanet['b_tech_id'] > 300) ? '_fleet' : '') . "&amp;cp=" . $UserPlanet['id'] . "&amp;re=0\" style=\"color:#33ff33;\">" . $UserPlanet['name'] . "</a>: </span><span class=\"holding colony\"> " . _getText('tech', $UserPlanet['b_tech_id']) . ' ('.app::$user->data[$resource[$UserPlanet['b_tech_id']]].' -> ' . (app::$user->data[$resource[$UserPlanet['b_tech_id']]] + 1) . ')');

			if (app::$planetrow->data['id'] == $UserPlanet['parent_planet'])
			{
				$parse['moon_img'] = "<a href=\"?set=overview&amp;cp=" . $UserPlanet['id'] . "&amp;re=0\" title=\"" . $UserPlanet['name'] . "\"><img src=\"" . DPATH . "planeten/" . $UserPlanet['image'] . ".jpg\" height=\"50\" width=\"50\"></a>";
				$parse['moon'] = $UserPlanet['name'];
			}
		}

		$parse['planet_type'] = app::$planetrow->data['planet_type'];
		$parse['planet_name'] = app::$planetrow->data['name'];
		$parse['planet_diameter'] = app::$planetrow->data['diameter'];
		$parse['planet_field_current'] = app::$planetrow->data['field_current'];
		$parse['planet_field_max'] = CalculateMaxPlanetFields(app::$planetrow->data);
		$parse['planet_temp_min'] = app::$planetrow->data['temp_min'];
		$parse['planet_temp_max'] = app::$planetrow->data['temp_max'];
		$parse['galaxy_galaxy'] = app::$planetrow->data['galaxy'];
		$parse['galaxy_planet'] = app::$planetrow->data['planet'];
		$parse['galaxy_system'] = app::$planetrow->data['system'];

		$records = cache::get('app::records_'.user::get()->getId().'');

		if ($records === false)
		{
			$records = db::query("SELECT `build_points`, `tech_points`, `fleet_points`, `defs_points`, `total_points`, `total_old_rank`, `total_rank` FROM game_statpoints WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . user::get()->getId() . "';", true);

			if (!is_array($records))
				$records = array();

			cache::set('app::records_'.user::get()->getId().'', $records, 1800);
		}

		if (count($records))
		{
			$parse['user_points'] = $records['build_points'];
			$parse['player_points_tech'] = $records['tech_points'];
			$parse['total_points'] = $records['total_points'];
			$parse['user_fleet'] = $records['fleet_points'];
			$parse['user_defs'] = $records['defs_points'];

			$parse['user_rank'] = $records['total_rank'] + 0;

			if (!$records['total_old_rank'])
				$records['total_old_rank'] = $records['total_rank'];

			$ile = $records['total_old_rank'] - $records['total_rank'];

			if ($ile >= 1)
				$parse['ile'] = "<font color=lime>+" . $ile . "</font>";
			elseif ($ile < 0)
				$parse['ile'] = "<font color=red>" . $ile . "</font>";
			elseif ($ile == 0)
				$parse['ile'] = "<font color=lightblue>" . $ile . "</font>";
		}
		else
		{
			$parse['user_points'] = 0;
			$parse['player_points_tech'] = 0;
			$parse['total_points'] = 0;
			$parse['user_fleet'] = 0;
			$parse['user_defs'] = 0;

			$parse['user_rank'] = 0;
			$parse['ile'] = "<font color=lightblue>0</font>";
		}

		$parse['user_username'] = app::$user->data['username'];

		$flotten = array();

		if (count($fpage) > 0)
		{
			ksort($fpage);
			foreach ($fpage as $content)
			{
				foreach ($content AS $text)
				{
					$flotten[] = $text;
				}
			}
		}

		$parse['fleet_list'] = $flotten;

		$parse['Have_new_level'] = $HaveNewLevel;
		$parse['planet_image'] = app::$planetrow->data['image'];
		$parse['anothers_planets'] = $AllPlanets;
		$parse['max_users'] = core::getConfig('users_amount');

		$parse['metal_debris'] = app::$planetrow->data['debris_metal'];
		$parse['crystal_debris'] = app::$planetrow->data['debris_crystal'];

		if ((app::$planetrow->data['debris_metal'] != 0 || app::$planetrow->data['debris_crystal'] != 0) && app::$planetrow->data[$resource[209]] != 0)
		{
			$parse['get_link'] = " (<a href=\"#\" onclick=\"QuickFleet(8, " . app::$planetrow->data['galaxy'] . ", " . app::$planetrow->data['system'] . ", " . app::$planetrow->data['planet'] . ", 2)\">переработать</a>)";
		}
		else
		{
			$parse['get_link'] = '';
		}

		if (app::$planetrow->data['b_building'] != 0)
		{
			app::$planetrow->UpdatePlanetBatimentQueueList();

			if (app::$planetrow->data['b_building'] != 0)
			{
				$BuildQueue = explode(";", app::$planetrow->data['b_building_id']);

				foreach ($BuildQueue AS $Queue)
				{
					$CurrBuild = explode(",", $Queue);
					$build_list[app::$planetrow->data['b_building']][] = array($CurrBuild[3], app::$planetrow->data['name'] . ": </span><span class=\"holding colony\"> " . _getText('tech', $CurrBuild[0]) . ' (' . ($CurrBuild[1] - 1) . ' -> ' . ($CurrBuild[1]) . ')');
				}
			}
		}

		if (app::$planetrow->data['b_tech'] != 0)
			$build_list[app::$planetrow->data['b_tech']][] = array(app::$planetrow->data['b_tech'], app::$planetrow->data['name'] . ": </span><span class=\"holding colony\"> " . _getText('tech', app::$planetrow->data['b_tech_id']) . ' ('.app::$user->data[$resource[app::$planetrow->data['b_tech_id']]].' -> ' . (app::$user->data[$resource[app::$planetrow->data['b_tech_id']]] + 1) . ')');

		if (count($build_list) > 0)
		{
			$parse['build_list'] = array();
			ksort($build_list);

			foreach ($build_list as $time => $planet)
			{
				foreach ($planet AS $flid => $text)
				{
					$parse['build_list'][] = $text;
				}
			}
		}

		$parse['case_pourcentage'] = floor(app::$planetrow->data["field_current"] / CalculateMaxPlanetFields(app::$planetrow->data) * 100);

		if ($parse['case_pourcentage'] > 80)
			$parse['case_barre_barcolor'] = '#C00000';
		elseif ($parse['case_pourcentage'] > 60)
			$parse['case_barre_barcolor'] = '#C0C000';
		else
			$parse['case_barre_barcolor'] = '#00C000';

		$parse['race'] = _getText('race', app::$user->data['race']);

		$parse['xpminier'] = app::$user->data['xpminier'];
		$parse['xpraid'] = app::$user->data['xpraid'];
		$parse['lvl_minier'] = app::$user->data['lvl_minier'];
		$parse['lvl_raid'] = app::$user->data['lvl_raid'];
		$parse['user_id'] = app::$user->data['id'];
		$parse['links'] = app::$user->data['links'];

		$parse['raids_win'] = app::$user->data['raids_win'];
		$parse['raids_lose'] = app::$user->data['raids_lose'];
		$parse['raids'] = app::$user->data['raids'];

		$LvlMinier = app::$user->data['lvl_minier'];
		$LvlRaid = app::$user->data['lvl_raid'];
		$parse['lvl_up_minier'] = $XpMinierUp;
		$parse['lvl_up_raid'] = $XpRaidUp;

		$parse['bonus'] = (app::$user->data['bonus'] < time()) ? true : false;
		if ($parse['bonus'])
		{
			$parse['bonus_multi'] = app::$user->data['bonus_multi'] + 1;

			if (app::$user->data['bonus'] < (time() - 86400))
				$parse['bonus_multi'] = 1;
		}

		$parse['refers'] = app::$user->data['refers'];

		$parse['officiers'] = array();

		foreach ($reslist['officier'] AS $officier)
		{
			$parse['officiers'][$officier] = app::$user->data[$resource[$officier]];
		}

		if (!user::get()->getUserOption('gameactivity'))
			core::setConfig('gameActivityList', 0);

		if (core::getConfig('gameActivityList', 0))
		{
			$parse['activity'] = array('chat' => array(), 'forum' => array());

			$chat = json_decode(cache::get("game_chat"), true);

			if (is_array($chat) && count($chat))
			{
				$chat = array_reverse($chat);

				$i = 0;

				foreach ($chat AS $message)
				{
					if ($message[3] != '')
						continue;

					if ($i >= 5)
						break;

					$parse['activity']['chat'][] = array
					(
						'TIME' => $message[0],
						'MESS' => '<span class="title"><span class="to">'.$message[1].'</span> написал'.($message[2] != '' ? ' <span class="to">'.$message[2].'</span>' : '').'</span>: '.$message[4].''
					);

					$i++;
				}
			}

			$forum = cache::get('forum_activity');

			if (!$forum)
			{
				$forum = file_get_contents('http://forum.xnova.su/lastposts.php');

				cache::set('forum_activity', $forum, 60);
			}

			$forum = json_decode($forum, true);

			foreach ($forum AS $message)
			{
				$parse['activity']['forum'][] = array
				(
					'TIME' => $message['post_time'],
					'MESS' => '<span class="title"><span class="to">'.$message['username'].'</span> написал "<span class="to">'.$message['topic_title'].'</span>"</span>: '.strings::cutString(strip_tags($message['post_text']), 250).' <a href="http://forum.xnova.su/viewtopic.php?f='.$message['forum_id'].'&t='.$message['topic_id'].'&p='.$message['post_id'].'#p'.$message['post_id'].'" target="_blank">читать полностью</a>'
				);
			}

			//usort($parse['activity'], create_function('$a1,$a2', 'if ($a1["TIME"] == $a2["TIME"]) return 0; return ($a1["TIME"] < $a2["TIME"] ? 1 : -1);'));
		}

		$this->setTemplate('overview');
		$this->set('parse', $parse);

		$this->display('', 'Обзор');
	}
}

?>