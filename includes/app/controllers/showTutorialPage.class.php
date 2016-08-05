<?php

class showTutorialPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();

		app::loadPlanet();
	}
	
	public function show ()
	{
		global $resource, $reslist;

		$parse = array();
		
		$requer = 0;
		
		$_GET['p'] = (isset($_GET['p'])) ? $_GET['p'] : '';
		
		if (!$_GET['p'] && user::get()->data['tutorial'] > 0)
			$_GET['p'] = user::get()->data['tutorial'] + 1;
		
		if (is_numeric($_GET['p']) && $_GET['p'] > user::get()->data['tutorial'] + 1)
			$_GET['p'] = user::get()->data['tutorial'] + 1;
		
		$stage = intval($_GET['p']);
		
		if (isset($_GET['new']))
		{
			strings::includeLang('tutorial');
		
			$parse['info'] = _getText('tutorial', $stage);
			$parse['task'] = array();
			$parse['rewd'] = array();
		
			$errors = 0;
		
			foreach ($parse['info']['TASK'] AS $taskKey => $taskVal)
			{
				$check = false;
		
				if ($taskKey == 'BUILD')
				{
					foreach ($taskVal AS $element => $level)
					{
						$check = (app::$planetrow->data[$resource[$element]] >= $level);
		
						if (in_array($element, array_merge($reslist['tech'], $reslist['tech_f'])))
							$parse['task'][] = array('Исследовать <b>'._getText('tech', $element).'</b> '.$level.' уровня', $check);
						elseif (in_array($element, $reslist['fleet']))
							$parse['task'][] = array('Постороить '.$level.' ед. флота типа <b>'._getText('tech', $element).'</b>', $check);
						elseif (in_array($element, $reslist['defense']))
							$parse['task'][] = array('Постороить '.$level.' ед. обороны типа <b>'._getText('tech', $element).'</b>', $check);
						else
							$parse['task'][] = array('Построить <b>'._getText('tech', $element).'</b> '.$level.' уровня', $check);
					}
				}
		
				$errors += !$check ? 1 : 0;
			}
		
			if (isset($_GET['continue']) && !$errors)
			{
				//db::query("UPDATE game_planets SET `" . $resource[401] . "` = `" . $resource[401] . "` + 3 WHERE `id` = '" . app::$planetrow->data['id'] . "';");
		
				app::$planetrow->PlanetResourceUpdate();
		
				sql::build()->setField('tutorial', $stage)->where('id', '=', user::get()->getId());
		
				user::get()->data['tutorial'] = $stage;
		
				request::redirectTo('?set=tutorial&p='.(user::get()->data['tutorial'] + 1));
			}
		
			foreach ($parse['info']['REWARD'] AS $rewardKey => $rewardVal)
			{
				if ($rewardKey == 'metal')
					$parse['rewd'][] = strings::pretty_number($rewardVal).' ед. '._getText('Metal').'а';
				elseif ($rewardKey == 'crystal')
					$parse['rewd'][] = strings::pretty_number($rewardVal).' ед. '._getText('Crystal').'а';
				elseif ($rewardKey == 'deuterium')
					$parse['rewd'][] = strings::pretty_number($rewardVal).' ед. '._getText('Deuterium').'';
				elseif ($rewardKey == 'BUILD')
				{
					foreach ($rewardVal AS $element => $level)
					{
						if (in_array($element, array_merge($reslist['tech'], $reslist['tech_f'])))
							$parse['rewd'][] = 'Исследование <b>'._getText('tech', $element).'</b> '.$level.' уровня';
						elseif (in_array($element, $reslist['fleet']))
							$parse['rewd'][] = $level.' ед. флота типа <b>'._getText('tech', $element).'</b>';
						elseif (in_array($element, $reslist['defense']))
							$parse['rewd'][] = $level.' ед. обороны типа <b>'._getText('tech', $element).'</b>';
						elseif (in_array($element, $reslist['officier']))
							$parse['rewd'][] = 'Офицер <b>'._getText('tech', $element).'</b> на '.round($level / 3600 / 24, 1).' суток';
						else
							$parse['rewd'][] = 'Постройка <b>'._getText('tech', $element).'</b> '.$level.' уровня';
					}
				}
			}
		
			$total = count(_getText('tutorial'));
		
			for ($e = 1; $e <= $total; $e++)
			{
				if (user::get()->data['tutorial'] >= $e)
					$parse['quest_' . $e] = true;
				else
					$parse['quest_' . $e] = false;
			}
		
			$this->setTemplate('tutorial_new');
		
			$this->set('stage', $stage);
			$this->set('total', $total);
			$this->set('errors', $errors);
		
			$this->set('parse', $parse);
		
			$this->display('', 'Обучение', false);
		}
		
		switch ($_GET['p'])
		{
			case 'exit':
				db::query("UPDATE game_users SET tutorial = 10, tutorial_value = 0 WHERE id = " . user::get()->data['id'] . "");
				user::get()->data['tutorial'] = 10;
				$this->message('Вы отказались от прохождения обучения. Данное действие необратимо.', 'Обучение', '?set=overview');
				break;
		
			case 'finish':
				$this->message('Вы завершили обучение. Удачной игры!', 'Обучение', '?set=overview');
				break;
		
			case 1:
				if (app::$planetrow->data[$resource[1]] >= 4)
				{
					$parse['met_4'] = 'check';
					$requer++;
				}
				else
				{
					$parse['met_4'] = 'none';
				}
				if (app::$planetrow->data[$resource[2]] >= 2)
				{
					$parse['cris_2'] = 'check';
					$requer++;
				}
				else
				{
					$parse['cris_2'] = 'none';
				}
				if (app::$planetrow->data[$resource[4]] >= 4)
				{
					$parse['sol_4'] = 'check';
					$requer++;
				}
				else
				{
					$parse['sol_4'] = 'none';
				}
				if (isset($_GET['continue']) and $requer == 3 and user::get()->data['tutorial'] == 0)
				{
					app::$planetrow->data['metal'] 	+= 1000;
					app::$planetrow->data['crystal'] += 500;
					app::$planetrow->PlanetResourceUpdate();
		
					user::get()->data['tutorial'] = 1;
					db::query("UPDATE game_users SET `tutorial` = '1' WHERE `id` = '" . user::get()->data['id'] . "';");
		
					$this->message('<div align="left"><ul><li>Следите чтобы ваши шахты были снабжены достаточным количеством энергии. Если её не будет, то они не будут работать в полную силу.</li><li>В начале игры солнечная электростанция является основным источником энергии.</li><li>В начале игры важно развивать шахты металла и кристалла. Шахта дейтерия вам понадобиться позже, когда вы построите исследовательскую лабораторию и приступите к изучению вселенной.</li></ul></div>', '<p style="color:lime;">Поздравляем! Вы добились успеха в снабжении вашей планеты необходимыми ресурсами.</p>', '?set=tutorial&p=2', 20);
				}
		
				if ($requer == 3 and user::get()->data['tutorial'] == 0)
				{
					$parse['button'] = '<input type="button" class="end" onclick="load(\'?set=tutorial&p=1&continue=1\')" value="Закончить">';
				}
		
				break;
		
			case 2:
				if (app::$planetrow->data[$resource[3]] >= 2)
				{
					$parse['deu_4'] = 'check';
					$requer++;
				}
				else
				{
					$parse['deu_4'] = 'none';
				}
				if (app::$planetrow->data[$resource[14]] >= 2)
				{
					$parse['robot_2'] = 'check';
					$requer++;
				}
				else
				{
					$parse['robot_2'] = 'none';
				}
				if (app::$planetrow->data[$resource[21]] >= 1)
				{
					$parse['han_1'] = 'check';
					$requer++;
				}
				else
				{
					$parse['han_1'] = 'none';
				}
				if (app::$planetrow->data[$resource[401]] >= 1)
				{
					$parse['lanz_1'] = 'check';
					$requer++;
				}
				else
				{
					$parse['lanz_1'] = 'none';
				}
				if (isset($_GET['continue']) and $requer == 4 and user::get()->data['tutorial'] == 1)
				{
					db::query("UPDATE game_planets SET `" . $resource[401] . "` = `" . $resource[401] . "` + 3 WHERE `id` = '" . app::$planetrow->data['id'] . "';");
					db::query("UPDATE game_users SET `tutorial` = '2' WHERE `id` = '" . user::get()->data['id'] . "';");
					user::get()->data['tutorial'] = 2;
		
					request::redirectTo('?set=tutorial&p=3');
				}
				if ($requer == 4 and user::get()->data['tutorial'] == 1)
				{
					$parse['button'] = '<input type="button" class="end" onclick="load(\'?set=tutorial&p=2&continue=1\')" value="Закончить">';
				}
		
				break;
		
			case 3:
				if (app::$planetrow->data[$resource[1]] >= 10)
				{
					$parse['met_10'] = 'check';
					$requer++;
				}
				else
				{
					$parse['met_10'] = 'none';
				}
				if (app::$planetrow->data[$resource[2]] >= 7)
				{
					$parse['cris_7'] = 'check';
					$requer++;
				}
				else
				{
					$parse['cris_7'] = 'none';
				}
				if (app::$planetrow->data[$resource[3]] >= 5)
				{
					$parse['deut_5'] = 'check';
					$requer++;
				}
				else
				{
					$parse['deut_5'] = 'none';
				}
				if (isset($_GET['continue']) and $requer == 3 and user::get()->data['tutorial'] == 2)
				{
					app::$planetrow->data['metal'] += 5000;
					app::$planetrow->data['crystal'] += 2500;
					db::query("UPDATE game_users SET `tutorial` = '3' WHERE `id` = '" . user::get()->data['id'] . "';");
					app::$planetrow->PlanetResourceUpdate();
					user::get()->data['tutorial'] = 3;
		
					request::redirectTo('?set=tutorial&p=4');
				}
				if ($requer == 3 and user::get()->data['tutorial'] == 2)
				{
					$parse['button'] = '<input type="button" class="end" onclick="load(\'?set=tutorial&p=3&continue=1\')" value="Закончить">';
				}
		
				break;
		
			case 4:
				if (app::$planetrow->data[$resource[31]] >= 1)
				{
					$parse['inv_1'] = 'check';
					$requer++;
				}
				else
				{
					$parse['inv_1'] = 'none';
				}
				if (user::get()->data[$resource[115]] >= 2)
				{
					$parse['comb_2'] = 'check';
					$requer++;
				}
				else
				{
					$parse['comb_2'] = 'none';
				}
				if (app::$planetrow->data[$resource[202]] >= 1)
				{
					$parse['navp_1'] = 'check';
					$requer++;
				}
				else
				{
					$parse['navp_1'] = 'none';
				}
				if (isset($_GET['continue']) and $requer == 3 and user::get()->data['tutorial'] == 3)
				{
					app::$planetrow->data['deuterium'] += 2000;
					user::get()->data['credits'] += 10;
					db::query("UPDATE game_users SET `tutorial` = '4', credits = credits + 10 WHERE `id` = '" . user::get()->data['id'] . "';");
					app::$planetrow->PlanetResourceUpdate();
					user::get()->data['tutorial'] = 4;
		
					request::redirectTo('?set=tutorial&p=5');
				}
				if ($requer == 3 and user::get()->data['tutorial'] == 3)
				{
					$parse['button'] = '<input type="button" class="end" onclick="load(\'?set=tutorial&p=4&continue=1\')" value="Закончить">';
				}
		
				break;
		
			case 5:
				if (app::$planetrow->data['name'] != 'Главная планета' and app::$planetrow->data['name'] != 'Колония')
				{
					$parse['planet'] = 'check';
					$requer++;
				}
				else
				{
					$parse['planet'] = 'none';
				}
				$buddyrow = db::query("SELECT count(*) AS `total` FROM game_buddy WHERE (`sender` = '" . user::get()->data["id"] . "' OR `owner` = '" . user::get()->data["id"] . "');", true);
				if ($buddyrow['total'] >= 1)
				{
					$parse['buddy'] = 'check';
					$requer++;
				}
				else
				{
					$parse['buddy'] = 'none';
				}
				if (user::get()->data['ally_id'] != 0)
				{
					$parse['ally'] = 'check';
					$requer++;
				}
				else
				{
					$parse['ally'] = 'none';
				}
				if (isset($_GET['continue']) and $requer == 3 and user::get()->data['tutorial'] == 4)
				{
					user::get()->data['credits'] += 10;
					db::query("UPDATE game_users SET `tutorial` = '5', tutorial_value = 0, `credits` = credits + 10 WHERE `id` = '" . user::get()->data['id'] . "';");
					user::get()->data['tutorial'] = 5;
		
					request::redirectTo('?set=tutorial&p=6');
				}
				if ($requer == 3 and user::get()->data['tutorial'] == 4)
				{
					$parse['button'] = '<input type="button" class="end" onclick="load(\'?set=tutorial&p=5&continue=1\')" value="Закончить">';
				}
		
				break;
		
			case 6:
				if (app::$planetrow->data[$resource[22]] >= 1 or app::$planetrow->data[$resource[23]] >= 1 or app::$planetrow->data[$resource[24]] >= 1)
				{
					$parse['alm'] = 'check';
					$requer++;
				}
				else
				{
					$parse['alm'] = 'none';
				}
				if (user::get()->data['tutorial_value'] > 0)
				{
					$parse['mer'] = 'check';
					$requer++;
				}
				else
				{
					$parse['mer'] = 'none';
				}
				if (isset($_GET['continue']) and $requer == 2 and user::get()->data['tutorial'] == 5)
				{
					$rand = mt_rand(22, 24);
					app::$planetrow->data[$resource[$rand]] += 1;
					db::query("UPDATE game_planets SET `" . $resource[$rand] . "` = '" . app::$planetrow->data[$resource[$rand]] . "' WHERE `id` = '" . app::$planetrow->data['id'] . "';");
					db::query("UPDATE game_users SET `tutorial` = '6', tutorial_value = 0 WHERE `id` = '" . user::get()->data['id'] . "';");
					user::get()->data['tutorial'] = 6;
		
					request::redirectTo('?set=tutorial&p=7');
				}
				if ($requer == 2 and user::get()->data['tutorial'] == 5)
				{
					$parse['button'] = '<input type="button" class="end" onclick="load(\'?set=tutorial&p=6&continue=1\')" value="Закончить">';
				}
		
				break;
		
			case 7:
				if (app::$planetrow->data[$resource[210]] >= 1)
				{
					$parse['sond'] = 'check';
					$requer++;
				}
				else
				{
					$parse['sond'] = 'none';
				}
				if (user::get()->data['tutorial_value'] >= 1)
				{
					$parse['esp'] = 'check';
					$requer++;
				}
				else
				{
					$parse['esp'] = 'none';
				}
				if (isset($_GET['continue']) and $requer == 2 and user::get()->data['tutorial'] == 6)
				{
					db::query("UPDATE game_planets SET `" . $resource[210] . "` = `" . $resource[210] . "` + 5 WHERE `id` = '" . app::$planetrow->data['id'] . "';");
					db::query("UPDATE game_users SET `tutorial` = '7', tutorial_value = 0 WHERE `id` = '" . user::get()->data['id'] . "';");
					user::get()->data['tutorial'] = 1;
		
					request::redirectTo('?set=tutorial&p=8');
				}
				if ($requer == 2 and user::get()->data['tutorial'] == 6)
				{
					$parse['button'] = '<input type="button" class="end" onclick="load(\'?set=tutorial&p=7&continue=1\')" value="Закончить">';
				}
		
				break;
		
			case 8:
		
				if (user::get()->data['tutorial_value'] >= 1)
				{
					$parse['exp'] = 'check';
					$requer++;
				}
				else
				{
					$parse['exp'] = 'none';
				}
				if (isset($_GET['continue']) and $requer == 1 and user::get()->data['tutorial'] == 7)
				{
					db::query("UPDATE game_planets SET `" . $resource[202] . "` = `" . $resource[202] . "` + 5 , `" . $resource[205] . "` = `" . $resource[205] . "` + 3 WHERE `id` = '" . app::$planetrow->data['id'] . "';");
					db::query("UPDATE game_users SET `tutorial` = '8', tutorial_value = 0 WHERE `id` = '" . user::get()->data['id'] . "';");
					user::get()->data['tutorial'] = 8;
		
					request::redirectTo('?set=tutorial&p=9');
				}
				if ($requer == 1 and user::get()->data['tutorial'] == 7)
				{
					$parse['button'] = '<input type="button" class="end" onclick="load(\'?set=tutorial&p=8&continue=1\')" value="Закончить">';
				}
		
				break;
		
			case 9:
				$planets = db::query("SELECT count(*) AS `total` FROM game_planets WHERE `id_owner` = '" . user::get()->data["id"] . "';", true);
				if ($planets['total'] >= 2)
				{
					$parse['colonia'] = 'check';
					$requer++;
				}
				else
				{
					$parse['colonia'] = 'none';
				}
				if (isset($_GET['continue']) and $requer == 1 and user::get()->data['tutorial'] == 8)
				{
					if (user::get()->data['rpg_constructeur'] > time())
						user::get()->data['rpg_constructeur'] += 259200;
					else
						user::get()->data['rpg_constructeur'] = time() + 259200;
		
					db::query("UPDATE game_users SET `tutorial` = '9', rpg_constructeur = " . user::get()->data['rpg_constructeur'] . " WHERE `id` = '" . user::get()->data['id'] . "';");
					user::get()->data['tutorial'] = 9;
		
					request::redirectTo('?set=tutorial&p=10');
				}
				if ($requer == 1 and user::get()->data['tutorial'] == 8)
				{
					$parse['button'] = '<input type="button" class="end" onclick="load(\'?set=tutorial&p=9&continue=1\')" value="Закончить">';
				}
		
				break;
		
		
			case 10:
				if (user::get()->data['tutorial_value'] >= 1)
				{
					$parse['rec'] = 'check';
					$requer++;
				}
				else
				{
					$parse['rec'] = 'none';
				}
				if (isset($_GET['continue']) and $requer == 1 and user::get()->data['tutorial'] == 9)
				{
					db::query("UPDATE game_planets SET `" . $resource[209] . "` = `" . $resource[209] . "` + 3 WHERE `id` = '" . app::$planetrow->data['id'] . "';");
					db::query("UPDATE game_users SET `tutorial` = '10', tutorial_value = 0 WHERE `id` = '" . user::get()->data['id'] . "';");
					user::get()->data['tutorial'] = 10;
		
					request::redirectTo('?set=tutorial&p=finish');
				}
				if ($requer == 1 and user::get()->data['tutorial'] == 9)
				{
					$parse['button'] = '<input type="button" class="end" onclick="load(\'?set=tutorial&p=10&continue=1\')" value="Закончить">';
				}
		
				break;
		}
		
		for ($e = 1; $e <= 10; $e++)
		{
			if (user::get()->data['tutorial'] >= $e)
			{
				$parse['tut_' . $e] = 'check';
			}
			else
			{
				$parse['tut_' . $e] = 'none';
			}
		}
		
		$parse['p'] = $_GET['p'];
		$parse['t'] = user::get()->data['tutorial'];
		
		$this->setTemplate('tutorial');
		$this->set('parse', $parse);
		
		$this->display('', 'Обучение', false);
	}
}

?>