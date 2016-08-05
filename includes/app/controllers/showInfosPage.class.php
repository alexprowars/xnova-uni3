<?php

class showInfosPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();

		app::loadPlanet();
	}
	
	public function show ()
	{
		if (request::G('gid'))
			$html = $this->ShowBuildingInfoPage(user::get(), app::$planetrow->data, request::G('gid'));
		else
			$html = '';

		$this->display('', $html, false);
	}

	private function BuildFleetListRows ($CurrentPlanet)
	{
		global $resource, $reslist;

		$CurrIdx = 1;
		$Result = array();
		foreach ($reslist['fleet'] AS $Ship)
		{
			if (isset($resource[$Ship]) && $CurrentPlanet[$resource[$Ship]] > 0)
			{
				$bloc = array();
				$bloc['idx'] = $CurrIdx;
				$bloc['fleet_id'] = $Ship;
				$bloc['fleet_name'] = _getText('tech', $Ship);
				$bloc['fleet_max'] = strings::pretty_number($CurrentPlanet[$resource[$Ship]]);
				$Result[] = $bloc;
				$CurrIdx++;
			}
		}
		return $Result;
	}

	private function BuildJumpableMoonCombo ($CurrentUser, $CurrentPlanet)
	{
		global $resource;

		$MoonList = db::query("SELECT `id`, `name`, `system`, `galaxy`, `planet`, `sprungtor`, `last_jump_time` FROM game_planets WHERE (`planet_type` = '3' OR `planet_type` = '5') AND `id_owner` = '" . $CurrentUser['id'] . "';");

		$Combo = "";

		while ($CurMoon = db::fetch_assoc($MoonList))
		{
			if ($CurMoon['id'] != $CurrentPlanet['id'])
			{
				$RestString = GetNextJumpWaitTime($CurMoon);

				if ($CurMoon[$resource[43]] >= 1)
				{
					$Combo .= "<option value=\"" . $CurMoon['id'] . "\">[" . $CurMoon['galaxy'] . ":" . $CurMoon['system'] . ":" . $CurMoon['planet'] . "] " . $CurMoon['name'] . $RestString['string'] . "</option>\n";
				}
			}
		}
		return $Combo;
	}

	private function BuildFleetCombo ($CurrentUser, $CurrentPlanet)
	{
		$MoonList = db::query("SELECT * FROM game_fleets WHERE `fleet_end_galaxy` = " . $CurrentPlanet['galaxy'] . " AND `fleet_end_system` = " . $CurrentPlanet['system'] . " AND `fleet_end_planet` = " . $CurrentPlanet['planet'] . " AND `fleet_end_type` = " . $CurrentPlanet['planet_type'] . " AND `fleet_mess` = 3 AND `fleet_owner` = '" . $CurrentUser['id'] . "';");

		$Combo = "";

		while ($CurMoon = db::fetch($MoonList))
		{
			$Combo .= "<option value=\"" . $CurMoon['fleet_id'] . "\">[" . $CurMoon['fleet_start_galaxy'] . ":" . $CurMoon['fleet_start_system'] . ":" . $CurMoon['fleet_start_planet'] . "] " . $CurMoon['fleet_owner_name'] . "</option>\n";
		}

		return $Combo;
	}

	/**
	 * @param  $CurrentUser user
	 * @param  $CurrentPlanet
	 * @param  $BuildID
	 * @return array
	 */
	private function ShowProductionTable ($CurrentUser, $CurrentPlanet, $BuildID)
	{
		global $resource;

		$CurrentBuildtLvl = $CurrentPlanet[$resource[$BuildID]];

		if ($BuildID != 42 && !($BuildID >= 22 && $BuildID <= 24))
		{
			$BuildLevelFactor = $CurrentPlanet[$resource[$BuildID] . "_porcent"];
			$BuildLevel = ($CurrentBuildtLvl > 0) ? $CurrentBuildtLvl : 1;

			$res = app::$planetrow->getProductionLevel($BuildID, $BuildLevel, $BuildLevelFactor);

			$Prod[1] = $res['metal'];
			$Prod[2] = $res['crystal'];
			$Prod[3] = $res['deuterium'];
			$Prod[4] = $res['energy'];

			if ($BuildID != 12)
			{
				$ActualNeed = floor($Prod[4]);
				$ActualProd = floor($Prod[$BuildID]);
			}
			else
			{
				$ActualNeed = floor($Prod[3]);
				$ActualProd = floor($Prod[4]);
			}
		}

		$BuildStartLvl = $CurrentBuildtLvl - 2;

		if ($BuildStartLvl < 1)
			$BuildStartLvl = 1;

		$Table = array();

		$ProdFirst = 0;

		for ($BuildLevel = $BuildStartLvl; $BuildLevel < $BuildStartLvl + 10; $BuildLevel++)
		{
			if ($BuildID != 42 && !($BuildID >= 22 && $BuildID <= 24))
			{
				$res = app::$planetrow->getProductionLevel($BuildID, $BuildLevel);

				$Prod[1] = $res['metal'];
				$Prod[2] = $res['crystal'];
				$Prod[3] = $res['deuterium'];
				$Prod[4] = $res['energy'];

				$bloc['build_lvl'] = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">" . $BuildLevel . "</font>" : $BuildLevel;

				if ($BuildID != 12)
				{
					$bloc['build_prod'] = strings::pretty_number(floor($Prod[$BuildID]));
					$bloc['build_prod_diff'] = strings::colorNumber(strings::pretty_number(floor($Prod[$BuildID] - $ActualProd)));
					$bloc['build_need'] = strings::colorNumber(strings::pretty_number(floor($Prod[4])));
					$bloc['build_need_diff'] = strings::colorNumber(strings::pretty_number(floor($Prod[4] - $ActualNeed)));
				}
				else
				{
					$bloc['build_prod'] = strings::pretty_number(floor($Prod[4]));
					$bloc['build_prod_diff'] = strings::colorNumber(strings::pretty_number(floor($Prod[4] - $ActualProd)));
					$bloc['build_need'] = strings::colorNumber(strings::pretty_number(floor($Prod[3])));
					$bloc['build_need_diff'] = strings::colorNumber(strings::pretty_number(floor($Prod[3] - $ActualNeed)));
				}
				if ($ProdFirst == 0)
				{
					if ($BuildID != 12)
						$ProdFirst = floor($Prod[$BuildID]);
					else
						$ProdFirst = floor($Prod[4]);
				}
			}
			elseif ($BuildID >= 22 && $BuildID <= 24)
			{
				$bloc['build_lvl'] = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">" . $BuildLevel . "</font>" : $BuildLevel;
				$bloc['build_range'] = floor((BASE_STORAGE_SIZE + floor(50000 * round(pow(1.6, $BuildLevel)))) * $CurrentUser->bonusValue('storage')) / 1000;
			}
			else
			{
				$bloc['build_lvl'] = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">" . $BuildLevel . "</font>" : $BuildLevel;
				$bloc['build_range'] = ($BuildLevel * $BuildLevel) - 1;
			}

			$Table[] = $bloc;
		}

		return $Table;
	}

	/**
	 * @param $CurrentUser user
	 * @param $CurrentPlanet array
	 * @param $BuildID int
	 * @return array|string
	 */
	private function ShowBuildingInfoPage ($CurrentUser, $CurrentPlanet, $BuildID)
	{
		global $resource, $pricelist, $CombatCaps, $reslist;

		strings::includeLang('infos');

		$parse = array();

		if (!_getText('tech', $BuildID))
			$this->message('Мы не сможем дать вам эту информацию', 'Ошибка', '?set=overview', 2);

		$parse['name'] = _getText('tech', $BuildID);
		$parse['image'] = $BuildID;
		$parse['description'] = _getText('info', $BuildID);

		if (($BuildID >= 1 && $BuildID <= 4) || $BuildID == 12 || $BuildID == 42 || ($BuildID >= 22 && $BuildID <= 24))
		{
			$this->setTemplate('info/info_buildings_table');
			$parse['table_data'] = $this->ShowProductionTable($CurrentUser, $CurrentPlanet, $BuildID);
			$this->set('parse', $parse, 'info_buildings_table');
		}
		elseif (($BuildID >= 14 && $BuildID <= 34) || $BuildID == 6 || $BuildID == 43 || $BuildID == 44 || $BuildID == 41 || ($BuildID >= 106 && $BuildID <= 199))
		{
			$this->setTemplate('info/info_buildings');

			if ($BuildID == 34)
			{
				$parse['msg'] = '';

				if (isset($_POST['send']) && isset($_POST['jmpto']))
				{
					$flid = intval($_POST['jmpto']);

					$query = db::query("SELECT * FROM game_fleets WHERE fleet_id = '" . $flid . "' AND fleet_end_galaxy = " . $CurrentPlanet['galaxy'] . " AND fleet_end_system = " . $CurrentPlanet['system'] . " AND fleet_end_planet = " . $CurrentPlanet['planet'] . " AND fleet_end_type = " . $CurrentPlanet['planet_type'] . " AND `fleet_mess` = 3", true);

					if (!$query['fleet_id'])
						$parse['msg'] = "<font color=red>Флот отсутствует у планеты</font>";
					else
					{
						$tt = 0;
						$temp = explode(';', $query['fleet_array']);
						foreach ($temp as $temp2)
						{
							$temp2 = explode(',', $temp2);
							if ($temp2[0] > 100)
							{
								$tt += $pricelist[$temp2[0]]['stay'] * $temp2[1];
							}
						}
						$max = $CurrentPlanet[$resource[$BuildID]] * 10000;
						if ($max > $CurrentPlanet['deuterium'])
							$cur = $CurrentPlanet['deuterium'];
						else
							$cur = $max;

						$times = round(($cur / $tt) * 3600);
						$CurrentPlanet['deuterium'] -= $cur;
						db::query("UPDATE game_fleets SET fleet_end_stay = fleet_end_stay + " . $times . ", fleet_end_time = fleet_end_time + " . $times . " WHERE fleet_id = '" . $flid . "'");

						$parse['msg'] = "<font color=red>Ракета с дейтерием отправлена на орбиту вашей планете</font>";
					}
				}

				if ($CurrentPlanet[$resource[$BuildID]] > 0)
				{
					if (!$parse['msg'])
						$parse['msg'] = "Выберите флот для отправки дейтерия";

					$parse['fleet'] = $this->BuildFleetCombo($CurrentUser->data, $CurrentPlanet);
					$parse['need'] = ($CurrentPlanet[$resource[$BuildID]] * 10000);

					$this->setTemplate('info/info_buildings_ally');
					$this->set('parse', $parse);
				}
			}

			if ($BuildID == 43 && $CurrentPlanet[$resource[$BuildID]] > 0)
			{
				$RestString = GetNextJumpWaitTime($CurrentPlanet);
				$gate = array();
				$gate['gate_start_link'] = BuildPlanetAdressLink($CurrentPlanet);

				if ($RestString['value'] != 0)
				{
					$gate['gate_time_script'] = InsertJavaScriptChronoApplet("Gate", "1", $RestString['value'], true);
					$gate['gate_wait_time'] = "<div id=\"bxx" . "Gate" . "1" . "\"></div>";
					$gate['gate_script_go'] = InsertJavaScriptChronoApplet("Gate", "1", $RestString['value'], false);
				}
				else
				{
					$gate['gate_time_script'] = "";
					$gate['gate_wait_time'] = "";
					$gate['gate_script_go'] = "";
				}
				
				$gate['gate_dest_moons'] = $this->BuildJumpableMoonCombo($CurrentUser->data, $CurrentPlanet);
				$gate['gate_fleet_rows'] = $this->BuildFleetListRows($CurrentPlanet);

				$this->setTemplate('info/info_gate');
				$this->set('parse', $gate);
			}

			$this->setTemplateName('info/info_buildings');
			$this->set('parse', $parse);

		}
		elseif (in_array($BuildID, $reslist['fleet']))
		{
			$this->setTemplate('info/info_buildings_fleet');

			$parse['hull_pt']  = floor(($pricelist[$BuildID]['metal'] + $pricelist[$BuildID]['crystal']) / 10);
			$parse['~hull_pt'] = $parse['hull_pt'];
			$parse['hull_pt']  = strings::pretty_number($parse['hull_pt']) . ' (' . strings::pretty_number(round($parse['hull_pt'] * (1 + $CurrentUser->data['defence_tech'] * 0.05 + (($CombatCaps[$BuildID]['power_up'] * ((isset($CurrentUser->data['fleet_' . $BuildID])) ? $CurrentUser->data['fleet_' . $BuildID] : 0)) / 100)))) . ')';

			$attTech = 1 + (((isset($CurrentUser->data['fleet_' . $BuildID])) ? $CurrentUser->data['fleet_' . $BuildID] : 0) * ($CombatCaps[$BuildID]['power_up'] / 100)) + $CurrentUser->data['military_tech'] * 0.05;

			if ($CombatCaps[$BuildID]['type_gun'] == 1)
				$attTech += $CurrentUser->data['laser_tech'] * 0.05;
			elseif ($CombatCaps[$BuildID]['type_gun'] == 2)
				$attTech += $CurrentUser->data['ionic_tech'] * 0.05;
			elseif ($CombatCaps[$BuildID]['type_gun'] == 3)
				$attTech += $CurrentUser->data['buster_tech'] * 0.05;

			include_once(ROOT_DIR.APP_PATH.'functions/functions.php');
			// Устанавливаем обновлённые двигателя кораблей
			SetShipsEngine($CurrentUser->data);

			$parse['attack_pt'] = strings::pretty_number($CombatCaps[$BuildID]['attack']) . ' (' . strings::pretty_number(round($CombatCaps[$BuildID]['attack'] * $attTech)) . ')';
			$parse['capacity_pt'] = strings::pretty_number($CombatCaps[$BuildID]['capacity']);
			$parse['base_speed'] = strings::pretty_number($CombatCaps[$BuildID]['speed']) . ' (' . strings::pretty_number(GetFleetMaxSpeed('', $BuildID, $CurrentUser)) . ')';
			$parse['base_conso'] = strings::pretty_number($CombatCaps[$BuildID]['consumption']);
			$parse['block'] = $CombatCaps[$BuildID]['power_armour'];
			$parse['upgrade'] = $CombatCaps[$BuildID]['power_up'];
			$parse['met'] = strings::pretty_number($pricelist[$BuildID]['metal']) . ' (' . strings::pretty_number($pricelist[$BuildID]['metal'] * $CurrentUser->bonusValue('res_fleet')) . ')';
			$parse['cry'] = strings::pretty_number($pricelist[$BuildID]['crystal']) . ' (' . strings::pretty_number($pricelist[$BuildID]['crystal'] * $CurrentUser->bonusValue('res_fleet')) . ')';
			$parse['deu'] = strings::pretty_number($pricelist[$BuildID]['deuterium']) . ' (' . strings::pretty_number($pricelist[$BuildID]['deuterium'] * $CurrentUser->bonusValue('res_fleet')) . ')';

			$engine = array('', 'Ракетный', 'Импульсный', 'Гиперпространственный');
			$gun = array('', 'Лазерное', 'Ионное', 'Плазменное');
			$armour = array('', 'Легкая', 'Средняя', 'Тяжелая', 'Монолитная');

			$parse['base_engine'] = $engine[$CombatCaps[$BuildID]['type_engine']];
			$parse['gun'] = $gun[$CombatCaps[$BuildID]['type_gun']];
			$parse['armour'] = $armour[$CombatCaps[$BuildID]['type_armour']];

			$parse['speedBattle'] = array();

			$fMerge = array_merge($reslist['defense'], $reslist['fleet']);

			foreach ($fMerge AS $Type)
			{
				if (!isset($CombatCaps[$Type]))
					continue;

				$enemy_durability = ($pricelist[$Type]['metal'] + $pricelist[$Type]['crystal']) / 10;

				$rapid = $CombatCaps[$BuildID]['attack'] * (isset($CombatCaps[$BuildID]['amplify'][$Type]) ? $CombatCaps[$BuildID]['amplify'][$Type] : 1) / $enemy_durability;

				if ($rapid >= 1)
					$parse['speedBattle'][$Type]['TO'] = floor($rapid);

				$rapid = $CombatCaps[$Type]['attack'] * (isset($CombatCaps[$Type]['amplify'][$BuildID]) ? $CombatCaps[$Type]['amplify'][$BuildID] : 1) / $parse['~hull_pt'];

				if ($rapid >= 1)
					$parse['speedBattle'][$Type]['FROM'] = floor($rapid);
			}

			$this->set('parse', $parse);

		}
		elseif (in_array($BuildID, $reslist['defense']))
		{
			$this->setTemplate('info/info_buildings_defence');

			$parse['element_typ'] = _getText('tech', 400);
			$parse['hull_pt'] = floor(($pricelist[$BuildID]['metal'] + $pricelist[$BuildID]['crystal']) / 10);

			if (isset($CombatCaps[$BuildID]['shield']))
				$parse['shield_pt'] = strings::pretty_number($CombatCaps[$BuildID]['shield']);
			else
				$parse['shield_pt'] = '';

			$parse['attack_pt'] = strings::pretty_number($CombatCaps[$BuildID]['attack']);
			$parse['met'] = strings::pretty_number($pricelist[$BuildID]['metal']);
			$parse['cry'] = strings::pretty_number($pricelist[$BuildID]['crystal']);
			$parse['deu'] = strings::pretty_number($pricelist[$BuildID]['deuterium']);

			if ($BuildID >= 400 && $BuildID < 500)
			{
				$gun = array('', 'Лазерное', 'Ионное', 'Плазменное');
				$armour = array('', 'Легкая', 'Средняя', 'Тяжелая', 'Монолитная');

				$parse['gun'] = $gun[$CombatCaps[$BuildID]['type_gun']];
				$parse['armour'] = $armour[$CombatCaps[$BuildID]['type_armour']];

				$parse['speedBattle'] = array();

				foreach ($reslist['fleet'] AS $Type)
				{
					if (!isset($CombatCaps[$Type]))
						continue;

					$enemy_durability = ($pricelist[$Type]['metal'] + $pricelist[$Type]['crystal']) / 10;

					$rapid = $CombatCaps[$BuildID]['attack'] * (isset($CombatCaps[$BuildID]['amplify'][$Type]) ? $CombatCaps[$BuildID]['amplify'][$Type] : 1) / $enemy_durability;

					if ($rapid >= 1)
						$parse['speedBattle'][$Type]['TO'] = floor($rapid);

					$rapid = $CombatCaps[$Type]['attack'] * (isset($CombatCaps[$Type]['amplify'][$BuildID]) ? $CombatCaps[$Type]['amplify'][$BuildID] : 1) / $parse['hull_pt'];

					if ($rapid >= 1)
						$parse['speedBattle'][$Type]['FROM'] = floor($rapid);
				}
			}

			$this->set('parse', $parse);

			if ($BuildID >= 500 && $BuildID < 600)
			{
				if (isset($_POST['form']))
				{
					$_POST['502'] = abs(intval($_POST['502']));
					$_POST['503'] = abs(intval($_POST['503']));

					if ($_POST['502'] > $CurrentPlanet[$resource[502]])
					{
						$_POST['502'] = $CurrentPlanet[$resource[502]];
					}
					if ($_POST['503'] > $CurrentPlanet[$resource[503]])
					{
						$_POST['503'] = $CurrentPlanet[$resource[503]];
					}
					db::query("UPDATE game_planets SET `" . $resource[502] . "` = `" . $resource[502] . "` - " . $_POST['502'] . " , `" . $resource[503] . "` = `" . $resource[503] . "` - " . $_POST['503'] . " WHERE `id` = " . $CurrentPlanet['id'] . ";");
					$CurrentPlanet[$resource[502]] -= $_POST['502'];
					$CurrentPlanet[$resource[503]] -= $_POST['503'];
				}
				$pars = array();
				$pars['max_mis'] = $CurrentPlanet[$resource[44]] * 10;
				$pars['int_miss'] = _getText('tech', 502) . ': ' . $CurrentPlanet[$resource[502]];
				$pars['plant_miss'] = _getText('tech', 503) . ': ' . $CurrentPlanet[$resource[503]];

				$this->setTemplate('info/info_missile');
				$this->set('parse', $pars);
			}

		}
		elseif (in_array($BuildID, $reslist['officier']))
		{
			$this->setTemplate('info/info_officier');
			$this->set('parse', $parse);
		}
		elseif ($BuildID >= 701 && $BuildID <= 704)
		{

			$parse['image'] = $BuildID - 700;

			$this->setTemplate('info/info_race');
			$this->set('parse', $parse);
		}

		if ($BuildID <= 44 && $BuildID != 33 && $BuildID != 41 && !($BuildID >= 601 && $BuildID <= 615) && !($BuildID >= 502 && $BuildID <= 503))
		{
			if ($CurrentPlanet[$resource[$BuildID]] > 0)
			{
				$DestroyTime = GetBuildingTime($CurrentUser, $CurrentPlanet, $BuildID) / 2;

				if ($DestroyTime < 1)
					$DestroyTime = 1;

				$parse['levelvalue'] = $CurrentPlanet[$resource[$BuildID]];
				$parse['destroy'] = GetElementPrice(GetBuildingPrice($CurrentUser, $CurrentPlanet, $BuildID, true, true), $CurrentPlanet);
				$parse['destroytime'] = strings::pretty_time($DestroyTime);

				$this->setTemplate('info/info_buildings_destroy');
				$this->set('parse', $parse);
			}
		}

		return $parse['name'];
	}
}

?>