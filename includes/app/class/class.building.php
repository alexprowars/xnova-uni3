<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class building
{
	/**
	 * @var $user user
	 */
	public $user;
	/**
	 * @var $planet planet
	 */
	public $planet;

	public function pageBuilding ()
	{
		global $resource, $reslist;

		$parse = array();

		$Allowed['1'] = array(1, 2, 3, 4, 6, 12, 14, 15, 21, 22, 23, 24, 31, 33, 34, 44);
		$Allowed['3'] = array(14, 21, 34, 41, 42, 43);
		$Allowed['5'] = array(14, 34, 43, 44);

		if ($this->planet->data['id_ally'] > 0 && $this->planet->data['id_ally'] == $this->user->data['ally_id'])
			$Allowed['5'] = array(14, 21, 34, 44);

		if (isset($_GET['cmd']))
		{
			$Command 	= request::G('cmd', '');
			$Element 	= request::G('building', 0, VALUE_INT);
			$ListID 	= request::G('listid', 0, VALUE_INT);

			if (in_array($Element, $Allowed[$this->planet->data['planet_type']]) || ($ListID != 0 && ($Command == 'cancel' || $Command == 'remove')))
			{
				switch ($Command)
				{
					case 'cancel':
						$this->CancelBuildingFromQueue();
						break;
					case 'remove':
						$this->RemoveBuildingFromQueue($ListID);
						break;
					case 'insert':
					case 'destroy':
						$this->AddBuildingToQueue($Element, ($Command == 'insert'));
						$this->planet->SetNextQueueElementOnTop();
						break;
				}

				request::redirectTo("?set=buildings");
			}
		}

		$this->planet->SetNextQueueElementOnTop();

		$Queue = $this->ShowBuildingQueue($this->planet, $this->user);

		$MaxBuidSize = MAX_BUILDING_QUEUE_SIZE;
		if ($this->user->data['rpg_constructeur'] > time())
			$MaxBuidSize += 2;

		if ($Queue['lenght'] < $MaxBuidSize)
		{
			$CanBuildElement = true;
		}
		else
		{
			$CanBuildElement = false;
		}

		$CurrentMaxFields = CalculateMaxPlanetFields($this->planet->data);
		$RoomIsOk = ($this->planet->data["field_current"] < ($CurrentMaxFields - $Queue['lenght']));

		$oldStyle = user::get()->getUserOption('only_available');

		$parse['BuildingsList'] = array();

		foreach ($reslist['build'] as $Element)
		{
			if (!in_array($Element, $Allowed[$this->planet->data['planet_type']]))
				continue;

			$isAccess = IsTechnologieAccessible($this->user->data, $this->planet->data, $Element);

			if (!$isAccess && $oldStyle)
				continue;

			if (!checkTechnologyRace($this->user->data, $Element))
				continue;

			$HaveRessources 	= IsElementBuyable($this->user, $this->planet->data, $Element, true, false);
			$BuildingLevel 		= $this->planet->data[$resource[$Element]];

			$row = array();

			$row['access']= $isAccess;
			$row['i'] 	= $Element;
			$row['count'] = $BuildingLevel;
			$row['price'] = GetElementPrice(GetBuildingPrice($this->user, $this->planet->data, $Element), $this->planet->data);

			if ($isAccess)
			{
				$row['time'] 	= GetBuildingTime($this->user, $this->planet->data, $Element);
				$row['add'] 	= GetNextProduction($Element, $BuildingLevel);
				$row['click'] = '';

				if ($Element == 31)
				{
					if ($this->user->data["b_tech_planet"] != 0)
						$row['click'] = "<span class=\"resNo\">" . _getText('in_working') . "</span>";
				}

				if (!$row['click'])
				{
					if ($RoomIsOk && $CanBuildElement)
					{
						if ($Queue['lenght'] == 0)
						{
							if ($HaveRessources == true)
								$row['click'] = "<a href=\"?set=buildings&cmd=insert&building=" . $Element . "\"><span class=\"resYes\">".((!$this->planet->data[$resource[$Element]]) ? 'Построить' : 'Улучшить')."</span></a>";
							else
								$row['click'] = "<span class=\"resNo\">нет ресурсов</span>";
						}
						else
							$row['click'] = "<a href=\"?set=buildings&cmd=insert&building=" . $Element . "\"><span class=\"resYes\">В очередь</span></a>";
					}
					elseif ($RoomIsOk && !$CanBuildElement)
						$row['click'] = "<span class=\"resNo\">".((!$this->planet->data[$resource[$Element]]) ? 'Построить' : 'Улучшить')."</span>";
					else
						$row['click'] = "<font color=#FF0000>нет места</font>";
				}
			}

			$parse['BuildingsList'][] = $row;
		}

		$parse['BuildList'] 			= $Queue['buildlist'];
		$parse['planet_field_current'] 	= $this->planet->data["field_current"];
		$parse['planet_field_max'] 		= $CurrentMaxFields;
		$parse['field_libre'] 			= $parse['planet_field_max'] - $this->planet->data['field_current'];

		return $parse;
	}

	public function pageResearch ($mode = '')
	{
		global $resource, $reslist, $pricelist, $CombatCaps;

		$TechHandle = $this->HandleTechnologieBuild($this->planet, $this->user);

		$NoResearchMessage = "";
		$bContinue = true;

		if (!CheckLabSettingsInQueue($this->planet->data))
		{
			$NoResearchMessage = _getText('labo_on_update');
			$bContinue = false;
		}

		$spaceLabs = array();

		if ($this->user->data[$resource[123]] > 0)
		{
			$spaceLabs = $this->planet->getNetworkLevel();
		}

		$this->planet->data['spaceLabs'] = $spaceLabs;

		if ($mode == 'fleet')
			$res_array = $reslist['tech_f'];
		else
			$res_array = $reslist['tech'];

		$PageParse['mode'] = $_GET['mode'];

		if (isset($_GET['cmd']) AND $bContinue != false)
		{
			$TheCommand = $_GET['cmd'];
			$Techno = intval($_GET['tech']);

			if ($Techno > 0 && in_array($Techno, $res_array))
			{
				if (is_array($TechHandle['WorkOn']))
					$WorkingPlanet = $TechHandle['WorkOn'];
				else
					$WorkingPlanet = $this->planet->data;

				$WorkingPlanet['spaceLabs'] = $spaceLabs;

				switch ($TheCommand)
				{
					case 'cancel':

						if ($TechHandle['OnWork'] && $TechHandle['WorkOn']['b_tech_id'] == $Techno)
						{
							$nedeed = GetBuildingPrice($this->user, $WorkingPlanet, $Techno);

							if ($TechHandle['WorkOn']['id'] == $this->planet->data['id'])
							{
								$this->planet->data['metal'] += $nedeed['metal'];
								$this->planet->data['crystal'] += $nedeed['crystal'];
								$this->planet->data['deuterium'] += $nedeed['deuterium'];
							}

							$WorkingPlanet['metal'] += $nedeed['metal'];
							$WorkingPlanet['crystal'] += $nedeed['crystal'];
							$WorkingPlanet['deuterium'] += $nedeed['deuterium'];
							$WorkingPlanet['b_tech_id'] = 0;
							$WorkingPlanet["b_tech"] = 0;
							$this->user->data['b_tech_planet'] = $WorkingPlanet["id"];
							$UpdateData = 1;
							$TechHandle['OnWork'] = false;

							// SPY SYSTEM
							sql::build()->insert('game_log_history')->set(array
							(
								'user_id' 			=> $this->user->data['id'],
								'time' 				=> time(),
								'operation' 		=> 6,
								'planet' 			=> $WorkingPlanet['id'],
								'from_metal' 		=> $WorkingPlanet['metal'] - $nedeed['metal'],
								'from_crystal' 		=> $WorkingPlanet['crystal'] - $nedeed['crystal'],
								'from_deuterium' 	=> $WorkingPlanet['deuterium'] - $nedeed['deuterium'],
								'to_metal' 			=> $WorkingPlanet['metal'],
								'to_crystal' 		=> $WorkingPlanet['crystal'],
								'to_deuterium' 		=> $WorkingPlanet['deuterium'],
								'build_id' 			=> $Techno,
								'level' 			=> ($this->user->data[$resource[$Techno]] + 1)
							))->execute();
							//
						}

						break;

					case 'search':

						if (IsTechnologieAccessible($this->user->data, $WorkingPlanet, $Techno) && IsElementBuyable($this->user, $WorkingPlanet, $Techno) && $WorkingPlanet['b_tech_id'] == 0 && !(isset($pricelist[$Techno]['max']) && $this->user->data[$resource[$Techno]] >= $pricelist[$Techno]['max']))
						{
							$costs = GetBuildingPrice($this->user, $WorkingPlanet, $Techno);
							$WorkingPlanet['metal'] -= $costs['metal'];
							$WorkingPlanet['crystal'] -= $costs['crystal'];
							$WorkingPlanet['deuterium'] -= $costs['deuterium'];
							$WorkingPlanet["b_tech_id"] = $Techno;
							$WorkingPlanet["b_tech"] = time() + GetBuildingTime($this->user, $WorkingPlanet, $Techno);
							$this->user->data["b_tech_planet"] = $WorkingPlanet["id"];
							$UpdateData = 1;
							$TechHandle['OnWork'] = true;

							// SPY SYSTEM
							sql::build()->insert('game_log_history')->set(array
							(
								'user_id' 			=> $this->user->data['id'],
								'time' 				=> time(),
								'operation' 		=> 5,
								'planet' 			=> $WorkingPlanet['id'],
								'from_metal' 		=> $WorkingPlanet['metal'] + $costs['metal'],
								'from_crystal' 		=> $WorkingPlanet['crystal'] + $costs['crystal'],
								'from_deuterium' 	=> $WorkingPlanet['deuterium'] + $costs['deuterium'],
								'to_metal' 			=> $WorkingPlanet['metal'],
								'to_crystal' 		=> $WorkingPlanet['crystal'],
								'to_deuterium' 		=> $WorkingPlanet['deuterium'],
								'build_id' 			=> $Techno,
								'level' 			=> ($this->user->data[$resource[$Techno]] + 1)
							))->execute();
							//
						}
						else
							$TechHandle['OnWork'] = 0;

						break;
				}

				if (isset($UpdateData) && $UpdateData == 1)
				{
					sql::build()->update('game_planets')->set(Array
					(
						'b_tech_id'	=> $WorkingPlanet['b_tech_id'],
						'b_tech'	=> $WorkingPlanet['b_tech'],
						'metal'		=> $WorkingPlanet['metal'],
						'crystal'	=> $WorkingPlanet['crystal'],
						'deuterium'	=> $WorkingPlanet['deuterium']
					))
					->where('id', '=', $WorkingPlanet['id'])->execute();

					sql::build()->update('game_users')->setField('b_tech_planet', $this->user->data['b_tech_planet'])->where('id', '=', $this->user->data['id'])->execute();
				}

				if (is_array($TechHandle['WorkOn']))
				{
					$TechHandle['WorkOn'] = $WorkingPlanet;
				}
				else
				{
					$this->planet->data = $WorkingPlanet;
					if ($TheCommand == 'search')
					{
						$TechHandle['WorkOn'] = $this->planet->data;
					}
				}
			}
		}

		$oldStyle = user::get()->getUserOption('only_available');

		$PageParse['technolist'] = array();

		foreach ($res_array AS $Tech)
		{
			$isAccess = IsTechnologieAccessible($this->user->data, $this->planet->data, $Tech);

			if (!$isAccess && $oldStyle)
				continue;

			if (!checkTechnologyRace($this->user->data, $Tech))
				continue;

			$row = array();
			$row['access'] = $isAccess;
			$row['i'] = $Tech;

			$building_level = $this->user->data[$resource[$Tech]];

			$row['tech_level'] = ($building_level == 0) ? "<font color=#FF0000>" . $building_level . "</font>" : "<font color=#00FF00>" . $building_level . "</font>";

			if (isset($pricelist[$Tech]['max']))
				$row['tech_level'] .= ' из <font color=yellow>' . $pricelist[$Tech]['max'] . '</font>';

			$row['tech_price'] = GetElementPrice(GetBuildingPrice($this->user, $this->planet->data, $Tech), $this->planet->data);

			if ($isAccess)
			{
				if ($Tech > 300 && $Tech < 400)
				{
					if ($CombatCaps[$Tech - 100]['power_up'] > 0)
					{
						$row['add'] = '+' . ($CombatCaps[$Tech - 100]['power_up'] * $building_level) . '% атака<br>';
						$row['add'] .= '+' . ($CombatCaps[$Tech - 100]['power_up'] * $building_level) . '% прочность<br>';
					}
					if ($CombatCaps[$Tech - 100]['power_consumption'] > 0)
						$row['add'] = '+' . ($CombatCaps[$Tech - 100]['power_consumption'] * $building_level) . '% вместимость<br>';
				}
				elseif ($Tech >= 120 && $Tech <= 122)
				{
					$row['add'] = '+' . (5 * $building_level) . '% атака<br>';
				}
				elseif ($Tech == 115)
				{
					$row['add'] = '+' . (10 * $building_level) . '% скорости РД<br>';
				}
				elseif ($Tech == 117)
				{
					$row['add'] = '+' . (20 * $building_level) . '% скорости ИД<br>';
				}
				elseif ($Tech == 118)
				{
					$row['add'] = '+' . (30 * $building_level) . '% скорости ГД<br>';
				}
				elseif ($Tech == 108)
				{
					$row['add'] = '+' . ($building_level + 1) . ' слотов флота<br>';
				}
				elseif ($Tech == 109)
				{
					$row['add'] = '+' . (5 * $building_level) . '% атаки<br>';
				}
				elseif ($Tech == 110)
				{
					$row['add'] = '+' . (3 * $building_level) . '% защиты<br>';
				}
				elseif ($Tech == 111)
				{
					$row['add'] = '+' . (5 * $building_level) . '% прочности<br>';
				}
				elseif ($Tech == 123)
				{
					$row['add'] = '+' . ($building_level) . '% лабораторий<br>';
				}

				$SearchTime = GetBuildingTime($this->user, $this->planet->data, $Tech);
				$row['search_time'] = $SearchTime;
				$CanBeDone = IsElementBuyable($this->user, $this->planet->data, $Tech);

				if (!$TechHandle['OnWork'])
				{
					$LevelToDo = 1 + $this->user->data[$resource[$Tech]];
					if (isset($pricelist[$Tech]['max']) && $this->user->data[$resource[$Tech]] >= $pricelist[$Tech]['max'])
					{
						$TechnoLink = '<font color=#FF0000>максимальный уровень</font>';
					}
					elseif ($CanBeDone)
					{
						if (!CheckLabSettingsInQueue($this->planet->data))
						{
							if ($LevelToDo == 1)
								$TechnoLink = "<font color=#FF0000>Исследовать</font>";
							else
								$TechnoLink = "<font color=#FF0000>Исследовать уровень " . $LevelToDo . "</font>";
						}
						else
						{
							$TechnoLink = "<a href=\"?set=buildings&mode=" . $_GET['mode'] . "&cmd=search&tech=" . $Tech . "\">";

							if ($LevelToDo == 1)
								$TechnoLink .= "<font color=#00FF00>Исследовать</font>";
							else
								$TechnoLink .= "<font color=#00FF00>Исследовать уровень " . $LevelToDo . "</font>";

							$TechnoLink .= "</a>";
						}
					}
					else
						$TechnoLink = '<font color=#FF0000>нет ресурсов</font>';
				}
				else
				{

					if ($TechHandle['WorkOn']["b_tech_id"] == $Tech)
					{
						$bloc = array();
						if ($TechHandle['WorkOn']['id'] != $this->planet->data['id'])
						{
							$bloc['tech_time'] = $TechHandle['WorkOn']["b_tech"] - time();
							$bloc['tech_name'] = ' на ' . $TechHandle['WorkOn']["name"];
							$bloc['tech_home'] = $TechHandle['WorkOn']["id"];
							$bloc['tech_id'] = $TechHandle['WorkOn']["b_tech_id"];
						}
						else
						{
							$bloc['tech_time'] = $this->planet->data["b_tech"] - time();
							$bloc['tech_name'] = "";
							$bloc['tech_home'] = $this->planet->data["id"];
							$bloc['tech_id'] = $this->planet->data["b_tech_id"];
						}
						$TechnoLink = $bloc;
					}
					else
						$TechnoLink = "<center>-</center>";
				}
				$row['tech_link'] = $TechnoLink;
			}

			$PageParse['technolist'][] = $row;
		}

		$PageParse['noresearch'] = $NoResearchMessage;

		return $PageParse;
	}

	public function pageShipyard ($mode = 'fleet')
	{
		global $resource, $reslist, $pricelist;

		$BuildArray = $this->extractHangarQueue($this->planet->data['b_hangar_id']);

		if($mode == 'defense')
			$elementIDs     = $reslist['defense'];
		else
			$elementIDs     = $reslist['fleet'];

		if (isset($_POST['fmenge']))
		{
			$Missiles[502] = $this->planet->data[$resource[502]];
			$Missiles[503] = $this->planet->data[$resource[503]];

			$MaxMissiles = $this->planet->data[$resource[44]] * 10;

			foreach ($BuildArray AS $Element => $Count)
			{
				if (($Element == 502 || $Element == 503) && $Count != 0)
					$Missiles[$Element] += $Count;
			}

			foreach ($_POST['fmenge'] as $Element => $Count)
			{
				$Element 	= intval($Element);
				$Count 		= abs(intval($Count));

				if (!in_array($Element, $elementIDs))
					continue;

				if (!IsTechnologieAccessible($this->user->data, $this->planet->data, $Element))
					continue;

				if (isset($pricelist[$Element]['max']))
				{
					$total = $this->planet->data[$resource[$Element]];

					if (isset($BuildArray[$Element]))
						$total += $BuildArray[$Element];

					$Count = min($Count, max(($pricelist[$Element]['max'] - $total), 0));
				}

				if ($Element == 502 || $Element == 503)
				{
					$ActuMissiles = $Missiles[502] + (2 * $Missiles[503]);
					$MissilesSpace = $MaxMissiles - $ActuMissiles;

					if ($MissilesSpace > 0)
					{
						if ($Element == 502)
							$Count = min($Count, $MissilesSpace);
						else
							$Count = min($Count, floor($MissilesSpace / 2));
					}
					else
						$Count = 0;
				}

				if (!$Count)
					continue;

				$Count = min($Count, $this->GetMaxConstructibleElements($Element, $this->planet->data));

				if ($Count > 0)
				{
					$Ressource = $this->GetElementRessources($Element, $Count);

					$this->planet->data['metal'] 		-= $Ressource['metal'];
					$this->planet->data['crystal'] 		-= $Ressource['crystal'];
					$this->planet->data['deuterium'] 	-= $Ressource['deuterium'];
					$this->planet->data['b_hangar_id'] 	.= $Element . "," . $Count . ";";

					sql::build()->update('game_planets')->set(Array
					(
						'metal' 		=> $this->planet->data['metal'],
						'crystal' 		=> $this->planet->data['crystal'],
						'deuterium' 	=> $this->planet->data['deuterium'],
						'b_hangar_id' 	=> $this->planet->data['b_hangar_id']
					))
					->where('id', '=', $this->planet->data['id'])->execute();

					// SPY SYSTEM
					sql::build()->insert('game_log_history')->set(array
					(
						'user_id' 			=> $this->user->data['id'],
						'time' 				=> time(),
						'operation' 		=> 7,
						'planet' 			=> $this->planet->data['id'],
						'from_metal' 		=> $this->planet->data['metal'] + $Ressource['metal'],
						'from_crystal' 		=> $this->planet->data['crystal'] + $Ressource['crystal'],
						'from_deuterium' 	=> $this->planet->data['deuterium'] + $Ressource['deuterium'],
						'to_metal' 			=> $this->planet->data['metal'],
						'to_crystal' 		=> $this->planet->data['crystal'],
						'to_deuterium' 		=> $this->planet->data['deuterium'],
						'build_id' 			=> $Element,
						'count' 			=> $Count
					))->execute();
					//
				}
			}
		}

		$oldStyle = user::get()->getUserOption('only_available');

		$parse = array();
		$parse['buildlist'] = array();

		foreach ($elementIDs AS $Element)
		{
			$isAccess = IsTechnologieAccessible($this->user->data, $this->planet->data, $Element);

			if (!$isAccess && $oldStyle)
				continue;

			if (!checkTechnologyRace($this->user->data, $Element))
				continue;

			$row = array();

			$row['access']	= $isAccess;
			$row['i'] 		= $Element;
			$row['count'] 	= $this->planet->data[$resource[$Element]];
			$row['price'] 	= GetElementPrice(GetBuildingPrice($this->user, $this->planet->data, $Element, false), $this->planet->data);


			if ($isAccess)
			{
				$row['time'] 		= GetBuildingTime($this->user, $this->planet->data, $Element);
				$row['can_build'] = IsElementBuyable($this->user, $this->planet->data, $Element, false);

				if ($row['can_build'])
				{
					$row['maximum'] = false;

					if (isset($pricelist[$Element]['max']))
					{
						$total = $this->planet->data[$resource[$Element]];

						if (isset($BuildArray[$Element]))
							$total += $BuildArray[$Element];

						if ($total >= $pricelist[$Element]['max'])
							$row['maximum'] = true;
					}

					$row['max'] = $this->GetMaxConstructibleElements($Element, $this->planet->data);
				}

				$row['add'] 	= GetNextProduction($Element, 0);
			}

			$parse['buildlist'][] = $row;
		}

		return $parse;
	}

	private function extractHangarQueue ($queue = '')
	{
		$result = array();

		if ($queue != '')
		{
			$elements = explode(';', $queue);

			foreach ($elements AS $element)
			{
				if (!$element)
					continue;

				$t = explode(',', $element);

				$result[$t[0]] = $t[1];
			}
		}

		return $result;
	}

	private function HandleTechnologieBuild ()
	{
		global $resource;

		if ($this->user->data['b_tech_planet'] != 0)
		{
			if ($this->user->data['b_tech_planet'] != $this->planet->data['id'])
				$WorkingPlanet = db::query("SELECT * FROM game_planets WHERE `id` = '" . $this->user->data['b_tech_planet'] . "';", true);

			if (isset($WorkingPlanet))
			{
				$ThePlanet = $WorkingPlanet;
			}
			else
			{
				$ThePlanet = $this->planet->data;
			}

			if ($ThePlanet['b_tech'] <= time() && $ThePlanet['b_tech_id'] != 0)
			{
				$this->user->data[$resource[$ThePlanet['b_tech_id']]]++;
				db::query("UPDATE game_planets SET `b_tech` = '0', `b_tech_id` = '0' WHERE `id` = '" . $ThePlanet['id'] . "';");
				db::query("UPDATE game_users SET `" . $resource[$ThePlanet['b_tech_id']] . "` = '" . $this->user->data[$resource[$ThePlanet['b_tech_id']]] . "', `b_tech_planet` = '0' WHERE `id` = '" . $this->user->data['id'] . "';");

				$ThePlanet["b_tech_id"] = 0;
				if (!isset($WorkingPlanet))
					$this->planet->data = $ThePlanet;

				$Result['WorkOn'] = "";
				$Result['OnWork'] = false;

			}
			elseif ($ThePlanet["b_tech_id"] == 0)
			{
				db::query("UPDATE game_users SET `b_tech_planet` = '0'  WHERE `id` = '" . $this->user->data['id'] . "';");
				$Result['WorkOn'] = "";
				$Result['OnWork'] = false;
			}
			else
			{
				$Result['WorkOn'] = $ThePlanet;
				$Result['OnWork'] = true;
			}
		}
		else
		{
			$Result['WorkOn'] = "";
			$Result['OnWork'] = false;
		}

		return $Result;
	}

	private function BuildingSavePlanetRecord ()
	{
		sql::build()->update('game_planets')->set(Array
		(
			'b_building_id' => $this->planet->data['b_building_id'],
			'b_building' 	=> $this->planet->data['b_building']
		))
		->where('id', '=', $this->planet->data['id'])->execute();
	}

	private function ShowBuildingQueue ()
	{
		$CurrentQueue = $this->planet->data['b_building_id'];

		if ($CurrentQueue != 0)
		{
			$QueueArray = explode(";", $CurrentQueue);
			$ActualCount = count($QueueArray);
		}
		else
		{
			$QueueArray = "0";
			$ActualCount = 0;
		}

		$ListIDRow = array();

		if ($ActualCount != 0)
		{
			$PlanetID = $this->planet->data['id'];

			for ($QueueID = 0; $QueueID < $ActualCount; $QueueID++)
			{
				$BuildArray 	= explode(",", $QueueArray[$QueueID]);
				$BuildEndTime 	= floor($BuildArray[3]);
				$CurrentTime 	= floor(time());

				if ($BuildEndTime >= $CurrentTime)
				{
					$ListIDRow[] = Array
					(
						'ListID' 		=> ($QueueID + 1),
						'ElementTitle' 	=> _getText('tech', $BuildArray[0]),
						'BuildLevel' 	=> $BuildArray[1],
						'BuildMode' 	=> $BuildArray[4],
						'BuildTime' 	=> ($BuildEndTime - time()),
						'PlanetID' 		=> $PlanetID,
						'BuildEndTime' 	=> $BuildEndTime
					);
				}
			}
		}

		$RetValue['lenght'] = $ActualCount;
		$RetValue['buildlist'] = $ListIDRow;

		return $RetValue;
	}

	public function AddBuildingToQueue ($Element, $AddMode = true)
	{
		global $resource;

		$CurrentQueue = $this->planet->data['b_building_id'];
		if ($CurrentQueue != 0)
		{
			$QueueArray = explode(";", $CurrentQueue);
			$ActualCount = count($QueueArray);
		}
		else
		{
			$QueueArray = "";
			$ActualCount = 0;
		}

		$BuildMode = $AddMode ? 'build' : 'destroy';

		$MaxBuidSize = MAX_BUILDING_QUEUE_SIZE;
		if ($this->user->data['rpg_constructeur'] > time())
			$MaxBuidSize += 2;

		if ($ActualCount < $MaxBuidSize)
		{
			$QueueID = $ActualCount + 1;
		}
		else
		{
			$QueueID = false;
		}

		$CurrentMaxFields = CalculateMaxPlanetFields($this->planet->data);
		if ($this->planet->data["field_current"] < ($CurrentMaxFields - $ActualCount) || $BuildMode == 'destroy')
		{
			$RoomIsOk = true;
		}
		else
		{
			$RoomIsOk = false;
		}

		if ($QueueID != false && $RoomIsOk)
		{
			if ($QueueID > 1)
			{
				$InArray = 0;
				for ($QueueElement = 0; $QueueElement < $ActualCount; $QueueElement++)
				{
					$QueueSubArray = explode(",", $QueueArray[$QueueElement]);
					if ($QueueSubArray[0] == $Element)
					{
						$InArray++;
					}
				}
			}
			else
			{
				$InArray = 0;
			}

			if ($InArray != 0)
			{
				$ActualLevel = $this->planet->data[$resource[$Element]];
				if ($AddMode == true)
				{
					$BuildLevel = $ActualLevel + 1 + $InArray;
					$this->planet->data[$resource[$Element]] += $InArray;
					$BuildTime = GetBuildingTime($this->user, $this->planet->data, $Element);
					$this->planet->data[$resource[$Element]] -= $InArray;
				}
				else
				{
					$BuildLevel = $ActualLevel - 1 + $InArray;
					$this->planet->data[$resource[$Element]] -= $InArray;
					$BuildTime = GetBuildingTime($this->user, $this->planet->data, $Element) / 2;
					$this->planet->data[$resource[$Element]] += $InArray;
				}
			}
			else
			{
				$ActualLevel = $this->planet->data[$resource[$Element]];
				if ($AddMode == true)
				{
					$BuildLevel = $ActualLevel + 1;
					$BuildTime = GetBuildingTime($this->user, $this->planet->data, $Element);
				}
				else
				{
					$BuildLevel = $ActualLevel - 1;
					$BuildTime = GetBuildingTime($this->user, $this->planet->data, $Element) / 2;
				}
			}

			if ($QueueID == 1)
			{
				$BuildEndTime = time() + $BuildTime;
			}
			else
			{
				$PrevBuild = explode(",", $QueueArray[$ActualCount - 1]);
				$BuildEndTime = $PrevBuild[3] + $BuildTime;
			}
			$QueueArray[$ActualCount] = $Element . "," . $BuildLevel . "," . $BuildTime . "," . $BuildEndTime . "," . $BuildMode;
			$NewQueue = implode(";", $QueueArray);
			$this->planet->data['b_building_id'] = $NewQueue;
		}

		$this->BuildingSavePlanetRecord($this->planet->data);

		if (!defined('IS_CRON'))
			$_SESSION['LAST_ACTION_TIME'] = time();
	}

	private function CancelBuildingFromQueue ()
	{
		if (isset($_SESSION['LAST_ACTION_TIME']) && $_SESSION['LAST_ACTION_TIME'] > time() - 5)
			return;

		if ($this->planet->data['b_building_id'] != '')
		{
			$QueueArray = explode(";", $this->planet->data['b_building_id']);
			$ActualCount = count($QueueArray);

			$CanceledIDArray = explode(",", $QueueArray[0]);
			$Element = $CanceledIDArray[0];
			$BuildMode = $CanceledIDArray[4];

			if ($ActualCount > 1)
			{
				array_shift($QueueArray);
				$NewCount = count($QueueArray);

				$BuildEndTime = time();

				for ($ID = 0; $ID < $NewCount; $ID++)
				{
					$ListIDArray = explode(",", $QueueArray[$ID]);

					$ListIDArray[2] = GetBuildingTime($this->user, $this->planet->data, $ListIDArray[0]);

					if ($ListIDArray[4] == 'destroy')
						$ListIDArray[2] = ceil($ListIDArray[2] / 2);

					$BuildEndTime += $ListIDArray[2];
					$ListIDArray[3] = $BuildEndTime;
					$QueueArray[$ID] = implode(",", $ListIDArray);
				}

				$NewQueue = implode(";", $QueueArray);
				$BuildEndTime = '0';
			}
			else
			{
				$NewQueue = '';
				$BuildEndTime = 0;
			}

			$ForDestroy = ($BuildMode == 'destroy') ? true : false;

			if ($Element)
			{
				$cost = GetBuildingPrice($this->user, $this->planet->data, $Element, true, $ForDestroy);

				db::query("LOCK TABLES game_planets WRITE");

				sql::build()->update('game_planets')->set(array
				(
					'+metal' 		=> $cost['metal'],
					'+crystal' 		=> $cost['crystal'],
					'+deuterium' 	=> $cost['deuterium'],
				))
				->where('id', '=', $this->planet->data['id'])->execute();

				db::query("UNLOCK TABLES");

				global $resource;

				// SPY SYSTEM
				sql::build()->insert('game_log_history')->set(array
				(
					'user_id' 			=> $this->user->data['id'],
					'time' 				=> time(),
					'operation' 		=> ($ForDestroy ? 4 : 3),
					'planet' 			=> $this->planet->data['id'],
					'from_metal' 		=> $this->planet->data['metal'],
					'from_crystal' 		=> $this->planet->data['crystal'],
					'from_deuterium' 	=> $this->planet->data['deuterium'],
					'to_metal' 			=> $this->planet->data['metal'] + $cost['metal'],
					'to_crystal' 		=> $this->planet->data['crystal'] + $cost['crystal'],
					'to_deuterium' 		=> $this->planet->data['deuterium'] + $cost['deuterium'],
					'build_id' 			=> $Element,
					'level' 			=> ($this->planet->data[$resource[$Element]] + 1)
				))->execute();
				//
			}
		}
		else
		{
			$NewQueue = '';
			$BuildEndTime = 0;
		}

		$this->planet->data['b_building_id'] = $NewQueue;
		$this->planet->data['b_building'] = $BuildEndTime;

		$this->BuildingSavePlanetRecord($this->planet->data);

		if (!defined('IS_CRON'))
			$_SESSION['LAST_ACTION_TIME'] = time();
	}

	private function RemoveBuildingFromQueue ($QueueID)
	{
		if (empty($this->planet->data['b_building_id']))
			return;

		$CurrentQueue = $this->planet->data['b_building_id'];

		$QueueArray = explode(";", $CurrentQueue);
		$ActualCount = count($QueueArray);

		if ($ActualCount < $QueueID)
			return;

		if ($ActualCount <= 1 || $QueueID <= 1)
			$this->CancelBuildingFromQueue();

		unset($QueueArray[$QueueID - 1]);

		$ListIDArray = explode(",", $QueueArray[0]);
		$BuildEndTime = $ListIDArray[3];

		foreach ($QueueArray as $ID => $QueueInfo)
		{
			if (!$ID)
				continue;

			$ListIDArray = explode(",", $QueueInfo);
			$ListIDArray[2] = GetBuildingTime($this->user, $this->planet->data, $ListIDArray[0]);

			if ($ListIDArray[4] == 'destroy')
				$ListIDArray[2] = ceil($ListIDArray[2] / 2);

			$BuildEndTime += $ListIDArray[2];

			$ListIDArray[3] = $BuildEndTime;
			$QueueArray[$ID] = implode(",", $ListIDArray);
		}

		$NewQueue = implode(";", $QueueArray);

		$this->planet->data['b_building_id'] = $NewQueue;

		$this->BuildingSavePlanetRecord($this->planet->data);
	}

	public function ElementBuildListBox ()
	{
		$ElementQueue = explode(';', $this->planet->data['b_hangar_id']);
		$NbrePerType = "";
		$NamePerType = "";
		$TimePerType = "";
		$QueueTime = 0;

		foreach ($ElementQueue as $Element)
		{
			if ($Element != '')
			{
				$Element = explode(',', $Element);
				$ElementTime = GetBuildingTime($this->user, $this->planet->data, $Element[0]);
				$QueueTime += $ElementTime * $Element[1];
				$TimePerType .= "" . $ElementTime . ",";
				$NamePerType .= "'" . html_entity_decode(_getText('tech', $Element[0])) . "',";
				$NbrePerType .= "" . $Element[1] . ",";
			}
		}

		$parse = array();
		$parse['a'] = $NbrePerType;
		$parse['b'] = $NamePerType;
		$parse['c'] = $TimePerType;
		$parse['b_hangar_id_plus'] = $this->planet->data['b_hangar'];

		$parse['time'] = strings::pretty_time($QueueTime - $this->planet->data['b_hangar']);

		return $parse;
	}

	public function GetElementRessources ($Element, $Count)
	{
		global $pricelist, $reslist;

		$ResType['metal'] 		= ($pricelist[$Element]['metal'] * $Count);
		$ResType['crystal'] 	= ($pricelist[$Element]['crystal'] * $Count);
		$ResType['deuterium'] 	= ($pricelist[$Element]['deuterium'] * $Count);

		foreach ($ResType AS &$cost)
		{
			if (in_array($Element, $reslist['fleet']))
				$cost = round($cost * $this->user->bonusValue('res_fleet'));
			elseif (in_array($Element, $reslist['defense']))
				$cost = round($cost * $this->user->bonusValue('res_defence'));
		}

		return $ResType;
	}

	public function GetMaxConstructibleElements ($Element, $Ressources)
	{
		global $pricelist, $reslist;

		$MaxElements = -1;

		foreach ($pricelist[$Element] AS $need_res => $need_count)
		{
			if (in_array($need_res, array('metal', 'crystal', 'deuterium', 'energy_max')) && $need_count != 0)
			{
				$count = 0;

				if (in_array($Element, $reslist['fleet']))
					$count = round($need_count * $this->user->bonusValue('res_fleet'));
				elseif (in_array($Element, $reslist['defense']))
					$count = round($need_count * $this->user->bonusValue('res_defence'));

				$count = floor($Ressources[$need_res] / $count);

				if ($MaxElements == -1)
					$MaxElements = $count;
				elseif ($MaxElements > $count)
					$MaxElements = $count;
			}
		}

		if (isset($pricelist[$Element]['max']) && $MaxElements > $pricelist[$Element]['max'])
			$MaxElements = $pricelist[$Element]['max'];

		return $MaxElements;
	}
}

?>