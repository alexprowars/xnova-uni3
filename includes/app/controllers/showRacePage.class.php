<?php

class showRacePage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}
	
	public function show ()
	{
		global $reslist, $resource;
		
		$ui = db::query('SELECT free_race_change FROM game_users_inf WHERE id = ' . user::get()->data['id'] . ';', true);
		
		if (isset($_GET['sel']) && user::get()->data['race'] == 0)
		{
			$r = intval($_GET['sel']);
			$r = ($r < 1 || $r > 4) ? 0 : $r;
		
			if ($r != 0)
			{
				//db::query("UPDATE game_users SET race = " . $r . " WHERE id = " . user::get()->data['id'] . ";");
		
				$QryUpdateUser = "UPDATE game_users SET ";
				$QryUpdateUser .= "race = ".$r.", ";
				$QryUpdateUser .= "bonus = ".(time() + 86400)." ";
		
				foreach ($reslist['officier'] AS $oId)
					$QryUpdateUser .= ", `" . $resource[$oId] . "` = '" . (time() + 86400) . "' ";
		
				$QryUpdateUser .= "WHERE ";
				$QryUpdateUser .= "`id` = '" . user::get()->data['id'] . "';";
				db::query($QryUpdateUser);
		
				request::redirectTo("?set=tutorial");
			}
		}
		
		if (isset($_GET['mode']) && isset($_POST['race']) && user::get()->data['race'] != 0 && $ui['free_race_change'] > 0)
		{
			$r = intval($_POST['race']);
			$r = ($r < 1 || $r > 4) ? 0 : $r;
		
			if ($r != 0)
			{
				$BuildOnPlanet = db::query("SELECT `id` FROM game_planets WHERE (`b_building` != 0 OR `b_tech` != 0 OR `b_hangar_id` != '') AND `id_owner` = '" . user::get()->data['id'] . "'");
				$UserFlyingFleets = db::query("SELECT `fleet_id` FROM game_fleets WHERE `fleet_owner` = '" . user::get()->data['id'] . "'");

				if (db::num_rows($BuildOnPlanet) > 0)
					$this->message('Для смены фракции y вac нe дoлжнo идти cтpoитeльcтвo или иccлeдoвaниe нa плaнeтe.', "Oшибкa", "?set=race", 5);
				elseif (db::num_rows($UserFlyingFleets) > 0)
					$this->message('Для смены фракции y вac нe дoлжeн нaxoдитьcя флoт в пoлeтe.', "Oшибкa", "?set=race", 5);
				else
				{
					db::query("UPDATE game_users SET race = " . $r . " WHERE id = " . user::get()->data['id'] . ";");
					db::query("UPDATE game_users_inf SET free_race_change = 0 WHERE id = " . user::get()->data['id'] . ";");
					db::query("UPDATE game_planets SET corvete = 0, interceptor = 0, dreadnought = 0, corsair = 0 WHERE id_owner = " . user::get()->data['id'] . ";");
		
					request::redirectTo("?set=overview");
				}
			}
		}
		
		$this->setTemplate('race');
		
		$this->set('race', user::get()->data['race'], 'race');
		$this->set('free_race_change', $ui['free_race_change']);
		
		$this->display('', 'Фракции', false, !(user::get()->data['race'] == 0));
	}
}

?>