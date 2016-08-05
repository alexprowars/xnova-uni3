<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class planet
{
	/**
	 * @var user $user
	 */
	private $user;
	public $data;

	function __construct($planet = null)
	{
		if (!is_null($planet))
		{
			if (is_numeric($planet))
				$this->load_from_id($planet);
			elseif (is_array($planet))
				$this->load_from_array($planet);
		}
	}

	public function __get($key)
	{
		return $this->__isset($key) ? $this->data[$key] : null;
	}

	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	public function getById ($planetId)
	{
		$data = db::query("SELECT * FROM game_planets WHERE `id` = ".intval($planetId), true);

		if (isset($data['id']))
			return $data;
		else
			return false;
	}

	public function getByCoords ($galaxy, $system, $planet, $type = 1)
	{
		$data = db::query("SELECT *
							FROM
								game_planets
							WHERE
								`galaxy` = '" . intval($galaxy) . "' AND
								`system` = '" . intval($system) . "' AND
								`planet` = '" . intval($planet) . "' AND
								`planet_type` = '" . intval($type) . "'", true);

		if (isset($data['id']))
			return $data;
		else
			return false;
	}

	public function load_from_id ($planet_id)
	{
		$this->data = $this->getById($planet_id);

		$this->copyTempParams();
	}

	public function load_from_coords ($galaxy, $system, $planet, $type)
	{
		$this->data = $this->getByCoords($galaxy, $system, $planet, $type);

		$this->copyTempParams();
	}

	public function load_from_array ($array)
	{
		$this->data = $array;

		$this->copyTempParams();
	}

	public function load_user_info ($array)
	{
		$this->user = $array;
	}

	public function copyTempParams ()
	{
		foreach ($this->data AS $key => $value)
		{
			$this->data['~'.$key] = $value;
		}

		$this->data['energy_max']	= 0;
	}

	public function checkOwnerPlanet ()
	{
		if ($this->data['id_owner'] != $this->user->data['id'] && $this->data['id_ally'] > 0 && ($this->data['id_ally'] != $this->user->data['ally_id'] || !$this->user->data['ally']['rights']['planet']))
		{
			sql::build()->update('game_users')->setField('current_planet', $this->user->data['id_planet'])->where('id', '=', $this->user->data['id'])->execute();

			$this->data['current_planet'] = $this->user->data['id_planet'];

			$this->load_from_id($this->user->data['id_planet']);

			return false;
		}

		return true;
	}

	public function getProductionLevel ($Element, $BuildLevel, $BuildLevelFactor = 10)
	{
		global $ProdGrid, $reslist;

		$return = array('energy' => 0);

		foreach ($reslist['res'] AS $res)
			$return[$res] = 0;

		if (isset($ProdGrid[$Element]))
		{
			$energy_tech 	= $this->user->data['energy_tech'];
			$BuildTemp		= $this->data['temp_max'];

			foreach ($reslist['res'] AS $res)
				$return[$res] = floor(eval($ProdGrid[$Element][$res]) * core::getConfig('resource_multiplier') * $this->user->bonusValue($res));

			$energy = floor(eval($ProdGrid[$Element]['energy']));

			if ($Element < 4)
				$return['energy'] = $energy;
			elseif ($Element == 4 || $Element == 12)
				$return['energy'] = floor($energy * $this->user->bonusValue('energy'));
			elseif ($Element == 212)
				$return['energy'] = floor($energy * $this->user->bonusValue('solar'));
		}

		return $return;
	}

	public function getProductions ()
	{
		global $resource, $reslist;

		$Caps = array();

		foreach ($reslist['res'] AS $res)
			$Caps[$res.'_perhour'] = 0;

		$Caps['energy_used'] 	= 0;
		$Caps['energy_max'] 	= 0;

		foreach ($reslist['prod'] AS $ProdID)
		{
			$BuildLevelFactor = $this->data[$resource[$ProdID] . '_porcent'];
			$BuildLevel = $this->data[$resource[$ProdID]];

			if ($ProdID == 12 && $this->data['deuterium'] < 100)
				$BuildLevelFactor = 0;

			$result = $this->getProductionLevel($ProdID, $BuildLevel, $BuildLevelFactor);

			foreach ($reslist['res'] AS $res)
				$Caps[$res.'_perhour'] += $result[$res];

			if ($ProdID < 4)
				$Caps['energy_used'] 	+= $result['energy'];
			else
				$Caps['energy_max'] 	+= $result['energy'];
		}

		if ($this->data['planet_type'] == 3 || $this->data['planet_type'] == 5)
		{
			foreach ($reslist['res'] AS $res)
			{
				core::setConfig($res.'_basic_income', 0);
				$this->data[$res.'_perhour'] = 0;
			}

			$this->data['energy_used'] 	= 0;
			$this->data['energy_max'] 	= 0;
		}
		else
		{
			foreach ($reslist['res'] AS $res)
				$this->data[$res.'_perhour'] = $Caps[$res.'_perhour'];

			$this->data['energy_used'] 	= $Caps['energy_used'];
			$this->data['energy_max'] 	= $Caps['energy_max'];
		}
	}

	public function PlanetResourceUpdate ($UpdateTime = 0, $Simul = false)
	{
		global $resource, $reslist;

		if ($this->user->data['urlaubs_modus_time'] != 0)
			$Simul = true;

		if (!$UpdateTime)
			$UpdateTime = time();

		if ($UpdateTime < $this->data['last_update'])
			return false;

		foreach ($reslist['res'] AS $res)
		{
			$this->data[$res.'_max']  = floor((BASE_STORAGE_SIZE + floor(50000 * round(pow(1.6, $this->data[$res.'_store'])))) * $this->user->bonusValue('storage'));
			$this->data[$res.'_max'] *= MAX_OVERFLOW;
		}

		$MaxMetalStorage = $this->data['metal_max'] * MAX_OVERFLOW;
		$MaxCristalStorage = $this->data['crystal_max'] * MAX_OVERFLOW;
		$MaxDeuteriumStorage = $this->data['deuterium_max'] * MAX_OVERFLOW;
		$MaxEnergyStorage = floor(5000 * $this->data['ak_station']);

		$this->getProductions();

		$ProductionTime = ($UpdateTime - $this->data['last_update']);
		$this->data['last_update'] = $UpdateTime;

		$value_energy_ak = $this->data['energy_ak'];

		if ($this->data['energy_max'] == 0)
		{
			foreach ($reslist['res'] AS $res)
				$this->data[$res.'_perhour'] = core::getConfig($res.'_basic_income');

			$production_level = 0;
		}
		elseif ($this->data['energy_max'] >= abs($this->data['energy_used']))
		{
			$production_level = 100;
			$akk_add = round(($this->data['energy_max'] - abs($this->data['energy_used'])) * ($ProductionTime / 3600), 2);

			if ($MaxEnergyStorage > ($this->data['energy_ak'] + $akk_add))
				$this->data['energy_ak'] += $akk_add;
			else
				$this->data['energy_ak'] = $MaxEnergyStorage;
		}
		else
		{
			if ($this->data['energy_ak'] > 0)
			{
				$need_en = ((abs($this->data['energy_used']) - $this->data['energy_max']) / 3600) * $ProductionTime;

				if ($this->data['energy_ak'] > $need_en)
				{
					$production_level = 100;
					$this->data['energy_ak'] -= round($need_en, 2);
				}
				else
				{
					$production_level = round((($this->data['energy_max'] + $this->data['energy_ak'] * 3600) / abs($this->data['energy_used'])) * 100, 1);
					$this->data['energy_ak'] = 0;
				}
			}
			else
				$production_level = round(($this->data['energy_max'] / abs($this->data['energy_used'])) * 100, 1);
		}

		$production_level = min(max($production_level, 0), 100);

		$this->data['production_level'] = $production_level;

		foreach ($reslist['res'] AS $res)
		{
			$this->data[$res.'_production'] = 0;

			if ($this->data[$res] <= $this->data[$res.'_max'])
			{
				$this->data[$res.'_production'] = (($ProductionTime * ($this->data[$res.'_perhour'] / 3600))) * (0.01 * $production_level);
				$this->data[$res.'_base'] 		= (($ProductionTime * (core::getConfig($res.'_basic_income', 0) / 3600)) * core::getConfig('resource_multiplier', 1));

				$this->data[$res.'_production'] = $this->data[$res.'_production'] + $this->data[$res.'_base'];

				if (($this->data[$res] + $this->data[$res.'_production']) > $this->data[$res.'_max'])
					$this->data[$res.'_production'] = $this->data[$res.'_max'] - $this->data[$res];
			}

			$this->data[$res.'_perhour'] = round($this->data[$res.'_perhour'] * (0.01 * $production_level));
			$this->data[$res] += $this->data[$res.'_production'];

			if ($this->data[$res] < 0)
				$this->data[$res] = 0;
		}

		if ($Simul)
		{
			$Builded = $this->HandleElementBuildingQueue($ProductionTime);

			$check = false;

			if (is_array($Builded))
			{
				foreach ($Builded AS $count)
				{
					if ($count > 0)
					{
						$check = true;
						break;
					}
				}
			}

			if ($check)
				$Simul = false;
		}

		if (!$Simul)
		{
			if (!isset($Builded))
				$Builded = $this->HandleElementBuildingQueue($ProductionTime);

			$arFields = array();

			if ($this->data['planet_type'] == 1)
			{
				foreach ($reslist['res'] AS $res)
				{
					if ($this->data[$res] != $this->data['~'.$res])
						$arFields[$res] = $this->data[$res];
				}

				if ($value_energy_ak != $this->data['energy_ak'])
					$arFields['energy_ak'] = $this->data['energy_ak'];
			}

			$arFields['last_update'] = $this->data['last_update'];

			if ($this->data['b_hangar_id'] != $this->data['~b_hangar_id'])
				$arFields['b_hangar_id'] = $this->data['b_hangar_id'];

			if ($Builded != '')
			{
				foreach ($Builded as $Element => $Count)
					if ($Element <> '' && $this->data[$resource[$Element]] != $this->data['~'.$resource[$Element]])
						$arFields[$resource[$Element]] = $this->data[$resource[$Element]];
			}

			if ($this->data['b_hangar'] != $this->data['~b_hangar'])
				$arFields['b_hangar'] = $this->data['b_hangar'];

			$this->saveData($arFields);
		}

		return true;
	}

	public function CheckPlanetUsedFields ()
	{
		global $resource;

		$cfc = $this->data[$resource[1]] + $this->data[$resource[2]] + $this->data[$resource[3]];
		$cfc += $this->data[$resource[4]] + $this->data[$resource[6]] + $this->data[$resource[12]] + $this->data[$resource[14]];
		$cfc += $this->data[$resource[15]] + $this->data[$resource[21]] + $this->data[$resource[22]];
		$cfc += $this->data[$resource[23]] + $this->data[$resource[24]] + $this->data[$resource[31]];
		$cfc += $this->data[$resource[33]] + $this->data[$resource[34]] + $this->data[$resource[44]];

		if ($this->data['planet_type'] == '3' || $this->data['planet_type'] == '5')
		{
			$cfc += $this->data[$resource[41]] + $this->data[$resource[42]] + $this->data[$resource[43]];
		}

		if ($this->data['field_current'] != $cfc)
		{
			$this->data['field_current'] = $cfc;

			$this->saveData(Array('field_current' => $this->data['field_current']));
		}
	}

	private function HandleElementBuildingQueue ($ProductionTime)
	{
		global $resource;

		if ($this->data['b_hangar_id'])
		{
			$this->data['b_hangar'] += $ProductionTime;
			$BuildQueue = explode(';', $this->data['b_hangar_id']);

			$MissilesSpace = ($this->data[$resource[44]] * 10) - ($this->data['interceptor_misil'] + (2 * $this->data['interplanetary_misil']));
			$Shield_1 = $this->data['small_protection_shield'];
			$Shield_2 = $this->data['big_protection_shield'];

			$BuildArray = array();
			$Builded = array();

			foreach ($BuildQueue as $Node => $Array)
			{
				if ($Array != '')
				{
					$Item = explode(',', $Array);

					if ($Item[0] == 502 || $Item[0] == 503)
					{
						if ($Item[0] == 502)
						{
							if ($Item[1] > $MissilesSpace)
								$Item[1] = $MissilesSpace;
							else
								$MissilesSpace -= $Item[1];
						}
						else
						{
							if ($Item[1] > floor($MissilesSpace / 2))
								$Item[1] = floor($MissilesSpace / 2);
							else
								$MissilesSpace -= $Item[1];
						}
					}

					if ($Item[0] == 407 || $Item[0] == 408)
					{
						if ($Item[1] > 1)
							$Item[1] = 1;

						if ($Item[0] == 407)
						{
							if ($Shield_1 == 1)
								$Item[1] = 0;
							else
								$Shield_1 = 1;
						}
						else
						{
							if ($Shield_2 == 1)
								$Item[1] = 0;
							else
								$Shield_2 = 1;
						}
					}

					$BuildArray[$Node] = array($Item[0], $Item[1], GetBuildingTime($this->user, $this->data, $Item[0]));
				}
			}

			$this->data['b_hangar_id'] = '';

			$UnFinished = false;

			foreach ($BuildArray as $Item)
			{
				if (!isset($resource[$Item[0]]))
					continue;

				$Element = $Item[0];
				$Count = $Item[1];
				$BuildTime = $Item[2];

				if (!isset($Builded[$Element]))
					$Builded[$Element] = 0;

				while ($this->data['b_hangar'] >= $BuildTime && !$UnFinished)
				{
					$this->data['b_hangar'] -= $BuildTime;
					$Builded[$Element]++;
					$this->data[$resource[$Element]]++;
					$Count--;

					if ($Count == 0)
					{
						break;
					}
					elseif ($this->data['b_hangar'] < $BuildTime)
					{
						$UnFinished = true;
					}
				}

				if ($Count > 0)
				{
					$UnFinished = true;
					$this->data['b_hangar_id'] .= $Element . "," . $Count . ";";
				}
			}
		}
		else
		{
			$Builded = '';
			$this->data['b_hangar'] = 0;
		}

		return $Builded;
	}

	public function UpdatePlanetBatimentQueueList ()
	{
		$RetValue = false;

		if ($this->data['b_building_id'])
		{
			$build_count = explode(';', $this->data['b_building_id']);
			$build_count = count($build_count);

			for ($i = 0; $i < $build_count; $i++)
			{
				if ($this->data['b_building'] <= time())
				{
					if ($this->CheckPlanetBuildingQueue())
					{
						$this->SetNextQueueElementOnTop();
						$RetValue = true;
					}
					else
						break;
				}
				else
					break;
			}
		}

		if ($this->checkTechnologieBuild())
			$RetValue = true;

		return $RetValue;
	}

	private function checkTechnologieBuild ()
	{
		global $resource;

		if ($this->user->data['b_tech_planet'] != 0)
		{
			if ($this->user->data['b_tech_planet'] != $this->data['id'])
				$WorkingPlanet = db::query("SELECT id, b_tech, b_tech_id FROM game_planets WHERE `id` = '" . $this->user->data['b_tech_planet'] . "';", true);

			if (isset($WorkingPlanet))
				$ThePlanet = $WorkingPlanet;
			else
				$ThePlanet = $this->data;

			if ($ThePlanet['b_tech'] <= time() && $ThePlanet['b_tech_id'] != 0)
			{
				$this->user->data[$resource[$ThePlanet['b_tech_id']]]++;

				sql::build()->update('game_planets')->set(Array
				(
					'b_tech'	=> 0,
					'b_tech_id'	=> 0
				))
				->where('id', '=', $ThePlanet['id'])->execute();

				sql::build()->update('game_users')->set(Array
				(
					$resource[$ThePlanet['b_tech_id']]	=> $this->user->data[$resource[$ThePlanet['b_tech_id']]],
					'b_tech_planet'						=> 0
				))
				->where('id', '=', $this->user->data['id'])->execute();

				if (!isset($WorkingPlanet))
				{
					$this->data['b_tech'] 	 = 0;
					$this->data['b_tech_id'] = 0;
				}
			}
			elseif ($ThePlanet["b_tech_id"] == 0)
			{
				sql::build()->update('game_users')->setField('b_tech_planet', 0)->where('id', '=', $this->user->data['id'])->execute();

				return true;
			}
			else
				return true;
		}

		return false;
	}

	private function CheckPlanetBuildingQueue ()
	{
		global $resource;

		$RetValue = false;

		if ($this->data['b_building_id'])
		{
			$QueueArray = explode(";", $this->data['b_building_id']);

			$BuildArray = explode(",", $QueueArray[0]);
			$Element = $BuildArray[0];

			array_shift($QueueArray);

			$ForDestroy = ($BuildArray[4] == 'destroy') ? true : false;

			if ($BuildArray[3] <= time())
			{
				$Needed = GetBuildingPrice($this->user, $this->data, $Element, true, $ForDestroy);
				$Units = $Needed['metal'] + $Needed['crystal'] + $Needed['deuterium'];

				// Мирный опыт за строения
				$XPBuildings = array(1, 2, 3, 5, 22, 23, 24, 25);
				$XP = 0;

				if (in_array($Element, $XPBuildings))
				{
					if (!$ForDestroy)
						$XP += floor($Units / 1500);
					else
						$XP -= floor($Units / 1500);
				}

				if (!$ForDestroy)
				{
					$this->data['field_current']++;
					$this->data[$resource[$Element]]++;
				}
				else
				{
					$this->data['field_current']--;
					$this->data[$resource[$Element]]--;
				}

				$NewQueue = (count($QueueArray) == 0) ? '' : implode(";", $QueueArray);

				$this->data['b_building'] = 0;
				$this->data['b_building_id'] = $NewQueue;
				$this->data['b_building_end'] = $BuildArray[3];

				sql::build()->update('game_planets')->set(Array
				(
					$resource[$Element]		=> $this->data[$resource[$Element]],
					'b_building'		=> 0,
					'b_building_id'		=> $this->data['b_building_id'],
					'field_current'		=> $this->data['field_current']
				))
				->where('id', '=', $this->data['id'])->execute();

				if ($XP != 0 && $this->user->data['lvl_minier'] < 100)
				{
					$this->user->data['xpminier'] += $XP;

					sql::build()->update('game_users')->set(Array
					(
						'xpminier' => $this->user->data['xpminier']
					))
					->where('id', '=', $this->user->data['id'])->execute();
				}

				$RetValue = true;
			}
			else
			{
				$RetValue = false;
			}
		}

		return $RetValue;
	}

	public function SetNextQueueElementOnTop ()
	{
		global $resource;

		if ($this->data['b_building'] == 0)
		{
			$BuildEndTime = 0;
			$NewQueue = '';

			if ($this->data['b_building_id'])
			{
				$QueueArray = explode(";", $this->data['b_building_id']);

				if (isset($this->data['b_building_end']))
				{
					$BuildEndTime = $this->data['b_building_end'];
					foreach ($QueueArray as $ID => $QueueInfo)
					{
						$ListIDArray = explode(",", $QueueInfo);

						$ListIDArray[2] = GetBuildingTime($this->user, $this->data, $ListIDArray[0]);
						if ($ListIDArray[4] == 'destroy')
							$ListIDArray[2] = ceil($ListIDArray[2] / 2);

						$BuildEndTime += $ListIDArray[2];
						$ListIDArray[3] = $BuildEndTime;
						$QueueArray[$ID] = implode(",", $ListIDArray);
					}
				}

				$Loop = true;

				while ($Loop)
				{
					$ListIDArray = explode(",", $QueueArray[0]);
					$Element = $ListIDArray[0];
					$BuildEndTime = $ListIDArray[3];
					$BuildMode = $ListIDArray[4];
					$HaveNoMoreLevel = false;

					$ForDestroy = ($BuildMode == 'destroy') ? true : false;

					if ($ForDestroy && $this->data[$resource[$Element]] == 0)
					{
						$HaveRessources = false;
						$HaveNoMoreLevel = true;
					}
					else
						$HaveRessources = IsElementBuyable($this->user, $this->data, $Element, true, $ForDestroy);

					if ($HaveRessources && IsTechnologieAccessible($this->user->data, $this->data, $Element))
					{
						$Needed = GetBuildingPrice($this->user, $this->data, $Element, true, $ForDestroy);

						$this->data['metal'] -= $Needed['metal'];
						$this->data['crystal'] -= $Needed['crystal'];
						$this->data['deuterium'] -= $Needed['deuterium'];

						$NewQueue = implode(";", $QueueArray);

						$Loop = false;

						// SPY SYSTEM
						sql::build()->insert('game_log_history')->set(array
						(
							'user_id' 			=> $this->user->data['id'],
							'time' 				=> time(),
							'operation' 		=> ($ForDestroy ? 2 : 1),
							'planet' 			=> $this->data['id'],
							'from_metal' 		=> $this->data['metal'] + $Needed['metal'],
							'from_crystal' 		=> $this->data['crystal'] + $Needed['crystal'],
							'from_deuterium' 	=> $this->data['deuterium'] + $Needed['deuterium'],
							'to_metal' 			=> $this->data['metal'],
							'to_crystal' 		=> $this->data['crystal'],
							'to_deuterium' 		=> $this->data['deuterium'],
							'build_id' 			=> $Element,
							'level' 			=> ($this->data[$resource[$Element]] + 1)
						))->execute();
						//
					}
					else
					{
						if ($HaveNoMoreLevel)
							$Message = sprintf(_getText('sys_nomore_level'), _getText('tech', $Element));
						elseif (!$HaveRessources)
						{
							$Needed = GetBuildingPrice($this->user, $this->data, $Element, true, $ForDestroy);

							$Message = 'У вас недостаточно ресурсов чтобы начать строительство здания ' . _getText('tech', $Element) . '.<br>Вам необходимо ещё: <br>';
							if ($Needed['metal'] > $this->data['metal'])
								$Message .= strings::pretty_number($Needed['metal'] - $this->data['metal']) . ' металла<br>';
							if ($Needed['crystal'] > $this->data['crystal'])
								$Message .= strings::pretty_number($Needed['crystal'] - $this->data['crystal']) . ' кристалла<br>';
							if ($Needed['deuterium'] > $this->data['deuterium'])
								$Message .= strings::pretty_number($Needed['deuterium'] - $this->data['deuterium']) . ' дейтерия<br>';
							if (isset($Needed['energy_max']) && isset($this->data['energy_max']) && $Needed['energy_max'] > $this->data['energy_max'])
								$Message .= strings::pretty_number($Needed['energy_max'] - $this->data['energy_max']) . ' энергии<br>';
						}

						if (isset($Message))
							user::get()->sendMessage($this->user->data['id'], 0, 0, 99, _getText('sys_buildlist'), $Message);

						array_shift($QueueArray);

						if (count($QueueArray) == 0)
						{
							$BuildEndTime = 0;
							$NewQueue = '';
							$Loop = false;
						}
					}
				}
			}

			if ($this->data['b_building'] != $BuildEndTime || $this->data['b_building_id'] != $NewQueue)
			{
				$this->data['b_building'] 		= $BuildEndTime;
				$this->data['b_building_id'] 	= $NewQueue;

				db::query("LOCK TABLES game_planets WRITE");

				sql::build()->update('game_planets')->set(Array
				(
					'metal'			=> $this->data['metal'],
					'crystal'		=> $this->data['crystal'],
					'deuterium'		=> $this->data['deuterium'],
					'b_building'	=> $this->data['b_building'],
					'b_building_id'	=> $this->data['b_building_id']
				))
				->where('id', '=', $this->data['id'])->execute();

				db::query("UNLOCK TABLES");
			}
		}
	}

	function checkAbandonMoonState (&$lunarow)
	{
		if ($lunarow['luna_destruyed'] <= time())
		{
			db::query("DELETE FROM game_planets WHERE `id` = " . $lunarow['luna_id'] . "");

			sql::build()->update('game_planets')->setField('parent_planet', 0)->where('parent_planet', '=', $lunarow['luna_id'])->execute();

			$lunarow['id_luna'] = 0;
		}
	}

	function checkAbandonPlanetState (&$planet)
	{
		if ($planet['destruyed'] <= time())
		{
			db::query("DELETE FROM game_planets WHERE id = " . $planet['id_planet'] . ";");
			if ($planet['parent_planet'] != 0)
				db::query("DELETE FROM game_planets WHERE id = " . $planet['parent_planet'] . ";");
		}
	}

	public function getNetworkLevel()
	{
		global $resource;

		$researchLevelList = array($this->data[$resource[31]]);

		if ($this->user->data[$resource[123]] > 0)
		{
			$researchResult = db::query("SELECT ".$resource[31]." FROM game_planets WHERE id_owner='" . $this->user->data['id'] . "' AND id != '" . $this->data['id'] . "' AND ".$resource[31]." > 0 AND destruyed = 0 AND planet_type = 1 ORDER BY ".$resource[31]." DESC LIMIT ".$this->user->data[$resource[123]]."");

			while ($researchRow = db::fetch($researchResult))
			{
				$researchLevelList[] = $researchRow[$resource[31]];
			}
		}

		return $researchLevelList;
	}

	public function saveData ($fields)
	{
		sql::build()->update('game_planets')->set($fields)->where('id', '=', $this->data['id'])->execute();
	}
}

?>