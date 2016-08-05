<?php

class showPlayersPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}
	
	public function show ()
	{
		global $session;
		
		$parse = array();
		
		$playerid = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];
		if (!isset($playerid))
		{
			$playerid = 0;
		}
		$ownid = ($session->isAuthorized()) ? user::get()->data['id'] : 0;
		
		$PlayerCard = db::query("SELECT u.*, ui.about, ui.ok_uid, ui.ok_photo FROM game_users u LEFT JOIN game_users_inf ui ON ui.id = u.id WHERE u.id = '" . intval($playerid) . "';");
		
		if ($daten = db::fetch($PlayerCard))
		{
			if ($daten['avatar'] != 0)
			{
				if ($daten['avatar'] != 99)
				{
					$parse['avatar'] = "/images/avatars/" . $daten['avatar'] . ".jpg";
				}
				else
				{
					$parse['avatar'] = "/images/avatars/upload/upload_" . $daten['id'] . ".jpg";
				}
			}
			elseif ($daten['ok_photo'])
			{
				$parse['avatar'] = $daten['ok_photo'];
			}

			$gesamtkaempfe = $daten['raids_win'] + $daten['raids_lose'];

			if ($gesamtkaempfe == 0)
			{
				$siegprozent = 0;
				$loosprozent = 0;
			}
			else
			{
				$siegprozent = 100 / $gesamtkaempfe * $daten['raids_win'];
				$loosprozent = 100 / $gesamtkaempfe * $daten['raids_lose'];
			}
		
			$planets = db::query("SELECT * FROM game_planets WHERE `galaxy` = '" . $daten['galaxy'] . "' and `system` = '" . $daten['system'] . "' and `planet_type` = '1' and `planet` = '" . $daten['planet'] . "';", true);
			$parse['userplanet'] = $planets['name'];
		
			$points = db::query("SELECT * FROM game_statpoints WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . $daten['id'] . "';", true);
			$parse['tech_rank'] = strings::pretty_number($points['tech_rank']);
			$parse['tech_points'] = strings::pretty_number($points['tech_points']);
			$parse['build_rank'] = strings::pretty_number($points['build_rank']);
			$parse['build_points'] = strings::pretty_number($points['build_points']);
			$parse['fleet_rank'] = strings::pretty_number($points['fleet_rank']);
			$parse['fleet_points'] = strings::pretty_number($points['fleet_points']);
			$parse['defs_rank'] = strings::pretty_number($points['defs_rank']);
			$parse['defs_points'] = strings::pretty_number($points['defs_points']);
			$parse['total_rank'] = strings::pretty_number($points['total_rank']);
			$parse['total_points'] = strings::pretty_number($points['total_points']);
		
			if ($ownid != 0)
				$parse['player_buddy'] = "<a href=\"?set=buddy&a=2&amp;u=" . $playerid . "\" title=\"Добавить в друзья\">Добавить в друзья</a>";
			else
				$parse['player_buddy'] = "";
		
			if ($ownid != 0)
				$parse['player_mes'] = "<a href=\"?set=messages&mode=write&id=" . $playerid . "\">Написать сообщение</a>";
			else
				$parse['player_mes'] = "";
		
			if ($daten['sex'] == 2)
				$parse['sex'] = "Женский";
			else
				$parse['sex'] = "Мужской";
		
			$parse['ingame'] = ($ownid != 0) ? true : false;
			$parse['id'] = $daten['id'];
			$parse['username'] = $daten['username'];
			$parse['race'] = $daten['race'];
			$parse['galaxy'] = $daten['galaxy'];
			$parse['system'] = $daten['system'];
			$parse['planet'] = $daten['planet'];
			$parse['ally_id'] = $daten['ally_id'];
			$parse['ally_name'] = $daten['ally_name'];
			$parse['about'] = $daten['about'];
			$parse['wons'] = strings::pretty_number($daten['raids_win']);
			$parse['loos'] = strings::pretty_number($daten['raids_lose']);
			$parse['siegprozent'] = round($siegprozent, 2);
			$parse['loosprozent'] = round($loosprozent, 2);
			$parse['total'] = $daten['raids'];
			$parse['totalprozent'] = 100;
			$parse['m'] = user::get()->getRankId($daten['lvl_minier']);
			$parse['f'] = user::get()->getRankId($daten['lvl_raid']);
		}
		else
			$this->message('Параметр задан неверно', 'Ошибка');
		
		$this->setTemplate('player');
		$this->set('parse', $parse);
		
		$this->display('', "Информация о игроке", false, $session->isAuthorized());
	}
}

?>