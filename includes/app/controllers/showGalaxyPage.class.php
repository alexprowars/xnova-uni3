<?php

class showGalaxyPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();

		app::loadPlanet();
		
		strings::includeLang('galaxy');
	}
	
	public function show()
	{
		$fleetmax = user::get()->data['computer_tech'] + 1;
		
		if (user::get()->data['rpg_admiral'] > time())
			$fleetmax += 2;
		
		$maxfleet_count = db::first(db::query("SELECT COUNT(*) AS num FROM game_fleets WHERE `fleet_owner` = '" . user::get()->data['id'] . "';", true));

		$records = cache::get('app::records_'.user::get()->getId().'');

		if ($records === false)
		{
			$records = db::query("SELECT `build_points`, `tech_points`, `fleet_points`, `defs_points`, `total_points`, `total_old_rank`, `total_rank` FROM game_statpoints WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . user::get()->getId() . "';", true);

			if (!is_array($records))
				$records = array();

			cache::set('app::records_'.user::get()->getId().'', $records, 1800);
		}

		$mode = isset($_GET['r']) ? intval($_GET['r']) : 0;
		
		$check_center 	= md5(user::get()->getId() . app::$planetrow->data['id'] . 'C');
		$check_left 	= md5(user::get()->getId() . app::$planetrow->data['id'] . 'L');
		$check_right 	= md5(user::get()->getId() . app::$planetrow->data['id'] . 'R');
		
		$galaxy = 1;
		$system = 1;
		$planet = 1;
		
		if ($mode == 0)
		{
			$galaxy = app::$planetrow->data['galaxy'];
			$system = app::$planetrow->data['system'];
			$planet = app::$planetrow->data['planet'];
		}
		elseif ($mode == 1)
		{
			if (isset($_POST["galaxyLeft"]))
				$galaxy = intval($_POST["galaxy"]) - 1;
			elseif (isset($_POST["galaxyRight"]))
				$galaxy = intval($_POST["galaxy"]) + 1;
			elseif (isset($_POST["galaxy"]))
				$galaxy = intval($_POST["galaxy"]);
			else
				$galaxy = app::$planetrow->data['galaxy'];
		
			if (isset($_POST["systemLeft"]))
				$system = intval($_POST["system"]) - 1;
			elseif (isset($_POST["systemRight"]))
				$system = intval($_POST["system"]) + 1;
			elseif (isset($_POST["system"]))
				$system = intval($_POST["system"]);
			else
				$system = app::$planetrow->data['system'];
		}
		elseif ($mode == 2)
		{
			$galaxy = intval($_GET['galaxy']);
			$system = intval($_GET['system']);
			$planet = intval($_GET['planet']);
		}
		elseif ($mode == 3)
		{
			$galaxy = intval($_GET['galaxy']);
			$system = intval($_GET['system']);
		}
		
		$galaxy = min(max($galaxy, 1), MAX_GALAXY_IN_WORLD);
		$system = min(max($system, 1), MAX_SYSTEM_IN_GALAXY);
		$planet = min(max($planet, 1), MAX_PLANET_IN_SYSTEM);
		
		if ((isset($_POST["galaxyLeft"]) || isset($_POST["systemLeft"])) && (!isset($_POST['left']) || $_POST['left'] != $check_left))
			$this->message('Режим бога включен! Приятной игры!', 'Ошибка');
		
		if ((isset($_POST["galaxyRight"]) || isset($_POST["systemRight"])) && (!isset($_POST['right']) || $_POST['right'] != $check_right))
			$this->message('Режим бога включен! Приятной игры!', 'Ошибка');
		
		if (!isset($_SESSION['fleet_shortcut']))
		{
			$array = user::get()->getUserPlanets(user::get()->getId(), false, user::get()->data['ally_id']);
			$j = array();
		
			foreach ($array AS $a)
			{
				$j[] = array(base64_encode($a['name']), $a['galaxy'], $a['system'], $a['planet']);
			}
		
			$shortcuts = db::first(db::query("SELECT fleet_shortcut FROM game_users_inf WHERE id = " . user::get()->data['id'] . ";", true));
		
			if (isset($shortcuts))
			{
				$scarray = explode("\r\n", $shortcuts);
		
				foreach ($scarray as $a => $b)
				{
					if ($b != "")
					{
						$c = explode(',', $b);
						$j[] = array(base64_encode($c[0]), intval($c[1]), intval($c[2]), intval($c[3]));
					}
				}
			}
		
			$_SESSION['fleet_shortcut'] = json_encode($j);
		}
		
		$Phalanx = 0;
		
		if (app::$planetrow->data['phalanx'] <> 0)
		{
			$Range = system::GetPhalanxRange(app::$planetrow->data['phalanx']);
			$SystemLimitMin = app::$planetrow->data['system'] - $Range;
			if ($SystemLimitMin < 1)
				$SystemLimitMin = 1;
			$SystemLimitMax = app::$planetrow->data['system'] + $Range;
		
			if ($system <= $SystemLimitMax && $system >= $SystemLimitMin)
				$Phalanx = 1;
		}
		
		if (app::$planetrow->data['interplanetary_misil'] <> 0)
		{
			if ($galaxy == app::$planetrow->data['galaxy'])
			{
				$Range = system::GetMissileRange();
				$SystemLimitMin = app::$planetrow->data['system'] - $Range;

				if ($SystemLimitMin < 1)
					$SystemLimitMin = 1;

				$SystemLimitMax = app::$planetrow->data['system'] + $Range;

				if ($system <= $SystemLimitMax)
				{
					if ($system >= $SystemLimitMin)
						$MissileBtn = 1;
					else
						$MissileBtn = 0;
				}
				else
					$MissileBtn = 0;
			}
			else
				$MissileBtn = 0;
		}
		else
			$MissileBtn = 0;

		$Destroy = 0;
		
		if (app::$planetrow->data['dearth_star'] > 0)
			$Destroy = 1;
		
		$html = '';
		
		if ($mode == 2)
		{
			$planetrowID = intval($_GET['current']);
			$html .= $this->ShowGalaxyMISelector($galaxy, $system, $planet, app::$planetrow->data['id'], app::$planetrow->data['interplanetary_misil']);
		}

		$html .= "<div id='galaxy'></div>";
		$html .= "<script>var Deuterium = '0';var time = " . time() . "; var dpath = '" . DPATH . "'; var user = {id:" . user::get()->data['id'] . ", phalanx:" . $Phalanx . ", destroy:" . $Destroy . ", missile:" . $MissileBtn . ", total_points:" . (isset($records['total_points']) ? $records['total_points'] : 0) . ", ally_id:" . user::get()->data['ally_id'] . ", current_planet:" . user::get()->data['current_planet'] . ", colonizer:" . app::$planetrow->data['colonizer'] . ", spy_sonde:" . app::$planetrow->data['spy_sonde'] . ", spy:".intval(user::get()->data['spy']).", recycler:" . app::$planetrow->data['recycler'] . ", interplanetary_misil:" . app::$planetrow->data['interplanetary_misil'] . ", fleets: " . $maxfleet_count . ", max_fleets: " . $fleetmax . "}; var galaxy = " . $galaxy . "; var system = " . $system . "; var row = new Array(); ";
		
		$html .= " var fleet_shortcut = new Array(); ";
		$array = json_decode($_SESSION['fleet_shortcut'], true);
		
		foreach ($array AS $id => $a)
		{
			$html .= " fleet_shortcut[" . $id . "] = new Array('" . base64_decode($a[0]) . "', " . $a[1] . ", " . $a[2] . ", " . $a[3] . ", " . (($a[1] == $galaxy && $a[2] == $system) ? 1 : 0) . "); ";
		}
		
		$html .= "$('#galaxy').append(PrintSelector(fleet_shortcut, '" . $check_right . "', '" . $check_left . "', '" . $check_center . "')); ";
		
		$galaxyRow = '';
		
		$GalaxyRow = db::query("SELECT
								p.planet, p.id AS id_planet, p.id_ally AS ally_planet, p.debris_metal AS metal, p.debris_crystal AS crystal, p.name, p.planet_type, p.destruyed, p.image, p.last_update, p.parent_planet,
								p2.id AS luna_id, p2.name AS luna_name, p2.destruyed AS luna_destruyed, p2.last_update AS luna_update, p2.diameter AS luna_diameter, p2.temp_min AS luna_temp,
								u.id AS user_id, u.username, u.race, u.ally_id, u.authlevel, u.onlinetime, u.urlaubs_modus_time, u.banaday, u.avatar,
								a.ally_name, a.ally_members, a.ally_web, a.ally_tag,
								ad.type,
								s.total_rank, s.total_points,
								ui.ok_photo AS photo
				FROM game_planets p 
				LEFT JOIN game_planets p2 ON (p.parent_planet = p2.id AND p.parent_planet != 0) 
				LEFT JOIN game_users u ON (u.id = p.id_owner AND p.id_owner != 0) 
				LEFT JOIN game_users_inf ui ON (ui.id = u.id)
				LEFT JOIN game_alliance a ON (a.id = u.ally_id AND u.ally_id != 0)
				LEFT JOIN game_alliance_diplomacy ad ON ((ad.a_id = u.ally_id AND ad.d_id = " . user::get()->data['ally_id'] . ") AND ad.status = 1 AND u.ally_id != 0)
				LEFT JOIN game_statpoints s ON (s.id_owner = u.id AND s.stat_type = '1' AND s.stat_code = '1') 
				WHERE p.planet_type <> 3 AND p.`galaxy` = '" . $galaxy . "' AND p.`system` = '" . $system . "';", '');
		
		$rows = array();
		
		while ($row = db::fetch_assoc($GalaxyRow))
		{
			if ($row['luna_update'] != "" && $row['luna_update'] > $row['last_update'])
				$row['last_update'] = $row['luna_update'];
		
			unset($row['luna_update']);
		
			if ($row['destruyed'] != 0 && $row["id_planet"] != '')
				app::$planetrow->checkAbandonPlanetState($row);
		
			if ($row["luna_id"] != "" && $row["luna_destruyed"] != 0)
				app::$planetrow->checkAbandonMoonState($row);

			$online = $row['onlinetime'];
		
			if ($online < (time() - 60 * 60 * 24 * 7) && $online > (time() - 60 * 60 * 24 * 28))
				$row['onlinetime'] = 1;
			elseif ($online < (time() - 60 * 60 * 24 * 28))
				$row['onlinetime'] = 2;
			else
				$row['onlinetime'] = 0;
		
			if ($row['urlaubs_modus_time'] > 0)
				$row['urlaubs_modus_time'] = 1;
		
			if ($row['last_update'] > (time() - 59 * 60))
				$row['last_update'] = floor((time() - $row['last_update']) / 60);
			else
				$row['last_update'] = 60;
		
			foreach ($row AS &$v)
				if (is_numeric($v))
					$v = intval($v);

			unset($v);
		
			$rows[] = $row;
		}
		
		shuffle($rows);
		
		foreach ($rows AS $row)
			$galaxyRow .= 'row[' . $row['planet'] . '] = '.json_encode($row, true).';';
		
		$packer = new JavaScriptPacker($galaxyRow, (mt_rand(1, 2) == 1 ? 10 : 62), false, true);
		$packed = $packer->pack();
		
		$html .= $packed;
		
		$html .= "$('#galaxy').append(PrintRow());</script>";
		
		$this->display($html, 'Галактика', false);
	}

	private function ShowGalaxyMISelector ($Galaxy, $System, $Planet, $Current, $MICount)
	{
		$Result = "<form action=\"?set=raketenangriff&c=" . $Current . "&mode=2&galaxy=" . $Galaxy . "&system=" . $System . "&planet=" . $Planet . "\" method=\"POST\">";
		$Result .= "<table border=\"0\">";
		$Result .= "<tr>";
		$Result .= "<td class=\"c\" colspan=\"3\">";
		$Result .= _getText('gm_launch') . " [" . $Galaxy . ":" . $System . ":" . $Planet . "]";
		$Result .= "</td>";
		$Result .= "</tr>";
		$Result .= "<tr>";
		$String = sprintf(_getText('gm_restmi'), $MICount);
		$Result .= "<td class=\"c\">" . $String . " <input type=\"text\" name=\"SendMI\" size=\"2\" maxlength=\"7\" /></td>";
		$Result .= "<td class=\"c\">" . _getText('gm_target') . " <select name=\"Target\">";
		$Result .= "<option value=\"all\" selected>" . _getText('gm_all') . "</option>";
		$Result .= "<option value=\"0\">" . _getText('tech', 401) . "</option>";
		$Result .= "<option value=\"1\">" . _getText('tech', 402) . "</option>";
		$Result .= "<option value=\"2\">" . _getText('tech', 403) . "</option>";
		$Result .= "<option value=\"3\">" . _getText('tech', 404) . "</option>";
		$Result .= "<option value=\"4\">" . _getText('tech', 405) . "</option>";
		$Result .= "<option value=\"5\">" . _getText('tech', 406) . "</option>";
		$Result .= "<option value=\"6\">" . _getText('tech', 407) . "</option>";
		$Result .= "<option value=\"7\">" . _getText('tech', 408) . "</option>";
		$Result .= "</select>";
		$Result .= "</td>";
		$Result .= "</tr>";
		$Result .= "<tr>";
		$Result .= "<td class=\"c\" colspan=\"2\"><input type=\"submit\" name=\"aktion\" value=\"" . _getText('gm_send') . "\"></td>";
		$Result .= "</tr>";
		$Result .= "</table>";
		$Result .= "</form>";

		return $Result;
	}
}