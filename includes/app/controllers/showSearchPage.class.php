<?php

class showSearchPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();

		app::loadPlanet();
	}
	
	public function show ()
	{
		$parse = array();

		$searchtext = (isset($_POST['searchtext'])) ? db::escape_string(htmlspecialchars($_POST['searchtext'])) : '';
		$type = (isset($_POST['type'])) ? $_POST['type'] : '';

		if ($searchtext != '' && $type != '')
		{
			switch ($type)
			{
				case "playername":
					$search = db::query("SELECT u.id, u.username, u.race, p.name AS planet_name, u.ally_name, u.galaxy AS g, u.system AS s, u.planet AS p, s.total_rank FROM game_users u LEFT JOIN game_planets p ON p.id = u.id_planet LEFT JOIN game_statpoints s ON s.id_owner = u.id AND s.stat_type = 1 WHERE u.username LIKE '%" . $searchtext . "%' LIMIT 30;");
					break;
				case "planetname":
					$search = db::query("SELECT u.id, u.username, u.race, p.name AS planet_name, u.ally_name, p.galaxy AS g, p.system AS s, p.planet AS p, s.total_rank FROM game_planets p LEFT JOIN game_users u ON u.id = p.id_owner LEFT JOIN game_statpoints s ON s.id_owner = u.id AND s.stat_type = 1 WHERE p.name LIKE '%" . $searchtext . "%' LIMIT 30");
					break;
				case "allytag":
					$search = db::query("SELECT a.id, a.ally_name, a.ally_tag, a.ally_members, s.total_points FROM game_alliance a LEFT JOIN game_statpoints s ON s.id_owner = a.id AND s.stat_type = 2 WHERE a.ally_tag LIKE '%" . $searchtext . "%' LIMIT 30");
					break;
				case "allyname":
					$search = db::query("SELECT a.id, a.ally_name, a.ally_tag, a.ally_members, s.total_points FROM game_alliance a LEFT JOIN game_statpoints s ON s.id_owner = a.id AND s.stat_type = 2 WHERE a.ally_name LIKE '%" . $searchtext . "%' LIMIT 30");
			}

			$parse['result'] = array();

			if (isset($search))
			{
				while ($r = db::fetch_assoc($search))
				{
					if ($type == 'playername' || $type == 'planetname')
					{
						if (!$r['total_rank'])
							$r['total_rank'] = 0;
						if (!$r['ally_name'])
							$r['ally_name'] = '-';

						$parse['result'][] = $r;
					}
					elseif ($type == 'allytag' || $type == 'allyname')
					{
						$r['total_points'] = strings::pretty_number($r['total_points']);

						$parse['result'][] = $r;
					}
				}
			}
		}

		$parse['searchtext'] = $searchtext;
		$parse['type'] = $type;

		$this->setTemplate('search');
		$this->set('parse', $parse);

		$this->display('', 'Поиск');
	}
}

?>