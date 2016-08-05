<?php

class MissionCaseSpy extends fleet_engine implements Mission
{
	function __construct($Fleet)
	{
			$this->_fleet = $Fleet;
	}

	public function TargetEvent()
	{
		global $resource, $reslist;

		$CurrentUser = db::query("SELECT `spy_tech`, `rpg_technocrate` FROM game_users WHERE `id` = '" . $this->_fleet['fleet_owner'] . "';", true);

		$TargetPlanet = new planet();
		$TargetPlanet->load_from_coords($this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet'], $this->_fleet['fleet_end_type']);

		if ($TargetPlanet->data['id_owner'] == 0)
		{
			$this->ReturnFleet();
			return false;
		}

		$TargetUser = new user;
		$TargetUser->load_from_id($TargetPlanet->data['id_owner']);

		if (!isset($TargetUser->data['id']))
		{
			$this->ReturnFleet();

			return false;
		}

		$TargetPlanet->load_user_info($TargetUser);

		$CurrentSpyLvl = $CurrentUser['spy_tech'];
		if ($CurrentUser['rpg_technocrate'] > time())
			$CurrentSpyLvl += 2;

		$TargetSpyLvl = $TargetUser->data['spy_tech'];
		if ($TargetUser->data['rpg_technocrate'] > time())
			$TargetSpyLvl += 2;

		// Обновление производства на планете
		// =============================================================================
		$TargetPlanet->PlanetResourceUpdate($this->_fleet['fleet_start_time']);
		// =============================================================================

		$LS = 0;

		$fleet = explode(";", $this->_fleet['fleet_array']);

		foreach ($fleet as $b)
		{
			if ($b != '')
			{
				$a = explode(",", $b);
				$l = explode("!", $a[1]);

				if ($a[0] == 210)
					$LS = $l[0];
			}
		}

		if ($LS > 0)
		{
			$def = db::query('SELECT fleet_array FROM game_fleets WHERE `fleet_end_galaxy` = ' . $this->_fleet['fleet_end_galaxy'] . ' AND `fleet_end_system` = ' . $this->_fleet['fleet_end_system'] . ' AND `fleet_end_type` = ' . $this->_fleet['fleet_end_type'] . ' AND `fleet_end_planet` = ' . $this->_fleet['fleet_end_planet'] . ' AND fleet_mess = 3');

			while ($defRow = db::fetch_assoc($def))
			{
				$defRowDef = explode(';', $defRow['fleet_array']);
				foreach ($defRowDef as $Element)
				{
					if ($Element != '')
					{
						$Element = explode(',', $Element);
						$Fleet = explode('!', $Element[1]);

						if ($Element[0] < 100)
							continue;

						$TargetPlanet->data[$resource[$Element[0]]] += $Fleet[0];
					}
				}
			}

			$ST = 0;

			$pT = ($TargetSpyLvl - $CurrentSpyLvl);
			$pW = ($CurrentSpyLvl - $TargetSpyLvl);

			if ($TargetSpyLvl > $CurrentSpyLvl)
				$ST = ($LS - pow($pT, 2));
			if ($CurrentSpyLvl > $TargetSpyLvl)
				$ST = ($LS + pow($pW, 2));
			if ($TargetSpyLvl == $CurrentSpyLvl)
				$ST = $CurrentSpyLvl;

			$MaterialsInfo = $this->SpyTarget($TargetPlanet->data, 0, _getText('sys_spy_maretials'));
			$SpyMessage = $MaterialsInfo['String'];

			$PlanetFleetInfo = $this->SpyTarget($TargetPlanet->data, 1, _getText('sys_spy_fleet'));

			if ($ST >= 2)
			{
				$SpyMessage .= $PlanetFleetInfo['String'];
			}
			if ($ST >= 3)
			{
				$PlanetDefenInfo = $this->SpyTarget($TargetPlanet->data, 2, _getText('sys_spy_defenses'));
				$SpyMessage .= $PlanetDefenInfo['String'];
			}
			if ($ST >= 5)
			{
				$PlanetBuildInfo = $this->SpyTarget($TargetPlanet->data, 3, _getText('tech', 0));
				$SpyMessage .= $PlanetBuildInfo['String'];
			}
			if ($ST >= 7)
			{
				$TargetTechnInfo = $this->SpyTarget($TargetUser->data, 4, _getText('tech', 100));
				$SpyMessage .= $TargetTechnInfo['String'];
			}
			if ($ST >= 8)
			{
				$TargetFleetLvlInfo = $this->SpyTarget($TargetUser->data, 5, _getText('tech', 300));
				$SpyMessage .= $TargetFleetLvlInfo['String'];
			}
			if ($ST >= 9)
			{
				$TargetOfficierLvlInfo = $this->SpyTarget($TargetUser->data, 6, _getText('tech', 600));
				$SpyMessage .= $TargetOfficierLvlInfo['String'];
			}

			$TargetForce = ($PlanetFleetInfo['Count'] * $LS) / 4;

			if ($TargetForce > 100)
				$TargetForce = 100;
			if ($TargetForce < 0)
				$TargetForce = 0;

			$TargetChances = rand(0, $TargetForce);
			$SpyerChances = rand(0, 100);

			if ($TargetChances <= $SpyerChances)
			{
				$DestProba = sprintf(_getText('sys_mess_spy_lostproba'), $TargetChances);
			}
			else
			{
				$DestProba = "<font color=\"red\">" . _getText('sys_mess_spy_destroyed') . "</font>";
			}

			$AttackLink = "<center>";
			$AttackLink .= "<a href=\"?set=fleet&galaxy=" . $this->_fleet['fleet_end_galaxy'] . "&system=" . $this->_fleet['fleet_end_system'] . "";
			$AttackLink .= "&planet=" . $this->_fleet['fleet_end_planet'] . "&planettype=" . $this->_fleet['fleet_end_type'] . "";
			$AttackLink .= "&target_mission=" . $this->_fleet['fleet_end_type'] . "";
			$AttackLink .= " \">" . _getText('type_mission', 1) . "";
			$AttackLink .= "</a></center>";

			$MessageEnd = "<center>" . $DestProba . "</center>";

			$fleet_link = '';

			if ($ST == 2)
				$res = $reslist['fleet'];
			elseif ($ST >= 3 && $ST <= 6)
				$res = array_merge($reslist['fleet'], $reslist['defense']);
			elseif ($ST >= 7)
				$res = array_merge($reslist['fleet'], $reslist['defense'], $reslist['tech']);
			else
				$res = array();

			foreach ($res AS $id)
			{
				if (isset($TargetPlanet->data[$resource[$id]]) && $TargetPlanet->data[$resource[$id]] > 0)
					$fleet_link .= $id . ',' . $TargetPlanet->data[$resource[$id]] . '!' . ((isset($TargetUser->data['fleet_' . $id]) && $ST >= 8) ? $TargetUser->data['fleet_' . $id] : 0) . ';';

				if (isset($TargetUser->data[$resource[$id]]) && $TargetUser->data[$resource[$id]] > 0)
					$fleet_link .= $id . ',' . $TargetUser->data[$resource[$id]] . '!0;';
			}

			$MessageEnd .= "<center><a href=\"/?set=sim&r=" . $fleet_link . "\" ".(core::getConfig('openRaportInNewWindow', 0) ? 'target="_blank"' : '').">Симуляция</a></center>";
			$MessageEnd .= "<center><a href=\"#\" onclick=\"raport_to_bb('sp" . $this->_fleet['fleet_start_time'] . "')\">BB-код</a></center>";

			$SpyMessage = "<div id=\"sp" . $this->_fleet['fleet_start_time'] . "\">" . $SpyMessage . "</div><br />" . $MessageEnd . $AttackLink;

			user::get()->sendMessage($this->_fleet['fleet_owner'], 0, $this->_fleet['fleet_start_time'], 0, _getText('sys_mess_qg'), $SpyMessage);

			$TargetMessage  = _getText('sys_mess_spy_ennemyfleet') . " " . $this->_fleet['fleet_owner_name'] ." ";
			$TargetMessage .= GetStartAdressLink($this->_fleet);
			$TargetMessage .= _getText('sys_mess_spy_seen_at') . " " . $TargetPlanet->data['name'];
			$TargetMessage .= " [" . $TargetPlanet->data["galaxy"] . ":" . $TargetPlanet->data["system"] . ":" . $TargetPlanet->data["planet"] . "]. ";
			$TargetMessage .= sprintf(_getText('sys_mess_spy_lostproba'), $TargetChances) . ".";

			user::get()->sendMessage($TargetPlanet->data['id_owner'], 0, $this->_fleet['fleet_start_time'], 0, _getText('sys_mess_spy_control'), $TargetMessage);

			if ($TargetChances > $SpyerChances)
			{
				if (!class_exists('MissionCaseAttack'))
					require_once(ROOT_DIR.APP_PATH.'class/interface/missions/MissionCaseAttack.php');

				$mission = new MissionCaseAttack($this->_fleet);
				$mission->TargetEvent();
			}
			else
				$this->ReturnFleet();
		}
		else
			$this->ReturnFleet();

		return true;
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