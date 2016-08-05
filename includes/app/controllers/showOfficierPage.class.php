<?php

class showOfficierPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
		
		if (user::get()->data['urlaubs_modus_time'] > 0)
			$this->message("Нет доступа!");

		strings::includeLang('officier');
	}
	
	public function show ()
	{
		global $resource, $reslist;
		
		$now = time();
		
		if (isset($_POST['buy']))
		{
		
			$need_c = 0;
			$times = 0;
			if (isset($_POST['week']) && $_POST['week'] != "")
			{
				$need_c = 20;
				$times = 604800;
			}
			elseif (isset($_POST['2week']) && $_POST['2week'] != "")
			{
				$need_c = 40;
				$times = 1209600;
			}
			elseif (isset($_POST['month']) && $_POST['month'] != "")
			{
				$need_c = 80;
				$times = 2592000;
			}
		
			if ($need_c > 0 && $times > 0 && user::get()->data['credits'] >= $need_c)
			{
				$Selected = intval($_POST['buy']);

				if (in_array($Selected, $reslist['officier']))
				{
					if (user::get()->data[$resource[$Selected]] > $now)
					{
						user::get()->data[$resource[$Selected]] = user::get()->data[$resource[$Selected]] + $times;
					}
					else
					{
						user::get()->data[$resource[$Selected]] = $now + $times;
					}
					user::get()->data['credits'] -= $need_c;
		
					$QryUpdateUser = "UPDATE game_users SET ";
					$QryUpdateUser .= "`credits` = '" . user::get()->data['credits'] . "', ";
					$QryUpdateUser .= "`" . $resource[$Selected] . "` = '" . user::get()->data[$resource[$Selected]] . "' ";
					$QryUpdateUser .= "WHERE ";
					$QryUpdateUser .= "`id` = '" . user::get()->data['id'] . "';";
					db::query($QryUpdateUser);
		
					db::query("INSERT INTO game_log_credits (uid, time, credits, type) VALUES (" . user::get()->data['id'] . ", " . time() . ", " . ($need_c * (-1)) . ", 5)");
		
					$Message = _getText('OffiRecrute');
				}
				else
					$Message = "НУ ТЫ И ЧИТАК!!!!!!";
			}
			else
				$Message = _getText('NoPoints');
		
			$this->message($Message, _getText('Officier'), '?set=officier', 2);
		}
		else
		{
			$parse['off_points'] = _getText('off_points');
			$parse['alv_points'] = strings::pretty_number(user::get()->data['credits']);
			$parse['list'] = array();
		
			for ($Officier = 601; $Officier <= 607; $Officier++)
			{
				$bloc['off_id'] = $Officier;
				$bloc['off_tx_lvl'] = _getText('ttle', $Officier);
				if (user::get()->data[$resource[$Officier]] > time())
				{
					$bloc['off_lvl'] = "<font color=\"#00ff00\">Нанят до : " . datezone("d.m.Y H:i", user::get()->data[$resource[$Officier]]) . "</font>";
					$bloc['off_link'] = "<font color=\"red\">Продлить</font>";
				}
				else
				{
					$bloc['off_lvl'] = "<font color=\"#ff0000\">Не оплачено</font>";
					$bloc['off_link'] = "<font color=\"red\">Нанять</font>";
				}
				$bloc['off_desc'] = _getText('Desc', $Officier);
		
				$bloc['off_link'] .= "<br><br><input type=\"hidden\" name=\"buy\" value=\"" . $Officier . "\"><input type=\"submit\" name=\"week\" value=\"на неделю\"><br>Стоимость:&nbsp;<font color=\"lime\">20</font>&nbsp;кр.<div class=\"separator\"></div><input type=\"submit\" name=\"2week\" value=\"на 2 недели\"><br>Стоимость:&nbsp;<font color=\"lime\">40</font>&nbsp;кр.<div class=\"separator\"></div><input type=\"submit\" name=\"month\" value=\"на месяц\"><br>Стоимость:&nbsp;<font color=\"lime\">80</font>&nbsp;кр.<div class=\"separator\"></div>";
				$parse['list'][] = $bloc;
			}
		
			$this->setTemplate('officier');
			$this->set('parse', $parse);
		}
		
		
		$this->display('', 'Офицеры', false);
	}
}

?>