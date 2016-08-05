<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * @var $page page
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if (!defined("INSIDE"))
	die("attemp hacking");

strings::includeLang('fleet');

$fleetid = intval($_POST['fleetid']);

if (!is_numeric($fleetid) || empty($fleetid))
{
	request::redirectTo("?set=overview");
}

$fleet = db::query("SELECT * FROM game_fleets WHERE fleet_id = '" . $fleetid . "' AND fleet_owner = " . user::get()->data['id'] . " AND fleet_mission = 1", true);

if (!isset($fleet['fleet_id']))
	$this->message('Этот флот не существует!', 'Ошибка');

$aks = db::query("SELECT * FROM game_aks WHERE id = '" . $fleet['fleet_group'] . "' LIMIT 1", true);

if ($fleet['fleet_start_time'] <= time() || $fleet['fleet_end_time'] < time() || $fleet['fleet_mess'] == 1)
	$this->message('Ваш флот возвращается на планету!', 'Ошибка');

if (!isset($_POST['send']))
{
	if (isset($_POST['action']) && $_POST['action'] == 'addaks')
	{
		if (empty($fleet['fleet_group']))
		{
			$rand = mt_rand(100000, 999999999);

			db::query("INSERT INTO game_aks SET
			`name` = '" . addslashes($_POST['groupname']) . "',
			`fleet_id` = " . $fleetid . ",
			`galaxy` = '" . $fleet['fleet_end_galaxy'] . "',
			`system` = '" . $fleet['fleet_end_system'] . "',
			`planet` = '" . $fleet['fleet_end_planet'] . "',
			`planet_type` = '" . $fleet['fleet_end_type'] . "',
			`user_id` = '" . user::get()->data['id'] . "'");

			$aksid = db::insert_id();

			if (!$aksid)
				$this->message('Невозможно получить идентификатор САБ атаки', 'Ошибка');

			$aks = db::query("SELECT * FROM game_aks WHERE id = '" . $aksid . "' LIMIT 1", true);

			$fleet['fleet_group'] = $aksid;
			db::query("UPDATE game_fleets SET fleet_group = '" . $fleet['fleet_group'] . "' WHERE fleet_id = '" . $fleetid . "'");
		}
		else
			$this->message('Для этого флота уже задана ассоциация!', 'Ошибка');
	}
	elseif (isset($_POST['action']) && $_POST['action'] == 'adduser')
	{
		if ($aks['fleet_id'] != $fleetid)
			$this->message("Вы не можете менять имя ассоциации", 'Ошибка');

		$addtogroup = db::escape_string($_POST['addtogroup']);

		$user_ = db::query("SELECT * FROM game_users WHERE username = '" . $addtogroup . "'");

		if (db::num_rows($user_) != 1)
			$this->message("Игрок не найден", 'Ошибка');

		$user_data = db::fetch($user_);
		$aks_user = db::query("SELECT * FROM game_aks_user WHERE aks_id = " . $aks['id'] . " AND user_id = " . $user_data['id'] . "");

		if (db::num_rows($aks_user) > 0)
			$this->message("Игрок уже приглашён для нападения", 'Ошибка');

		db::query("INSERT INTO game_aks_user VALUES (" . $aks['id'] . ", " . $user_data['id'] . ")");

		$planet_daten = db::query("SELECT `id_owner`, `name` FROM game_planets WHERE galaxy = '" . $aks['galaxy'] . "' AND system = '" . $aks['system'] . "' AND planet = '" . $aks['planet'] . "' AND planet_type = '" . $aks['planet_type'] . "'", true);
		$owner = db::query("SELECT username FROM game_users WHERE id = '" . $planet_daten['id_owner'] . "'", true);

		$message = "Игрок " . user::get()->data['username'] . " приглашает вас произвести совместное нападение на планету " . $planet_daten['name'] . " [" . $aks['galaxy'] . ":" . $aks['system'] . ":" . $aks['planet'] . "] игрока " . $owner['username'] . ". Имя ассоциации: " . $aks['name'] . ". Если вы отказываетесь, то просто проигнорируйте данной сообщение.";

		user::get()->sendMessage($user_data['id'], false, 0, 0, 'Флот', $message);
	}
	elseif (isset($_POST['action']) && $_POST['action'] == "changename")
	{
		if ($aks['fleet_id'] != $fleetid)
			$this->message("Вы не можете менять имя ассоциации", 'Ошибка');

		$name = $_POST['groupname'];

		if (mb_strlen($name, 'UTF-8') > 20)
			$this->message("Слишком длинное имя ассоциации", 'Ошибка');

		if (!preg_match("/^[a-zA-Zа-яА-Я0-9_\.\,\-\!\?\*\ ]+$/u", $name))
			$this->message("Имя ассоциации содержит запрещённые символы", _getText('error'));

		$name = db::escape_string(strip_tags($name));

		$x = db::query("SELECT * FROM game_aks WHERE name = '" . $name . "'");

		if (db::num_rows($x) >= 1)
			$this->message("Имя уже зарезервировано другим игроком", 'Ошибка');

		$aks['name'] = $name;

		db::query("UPDATE game_aks SET name = '" . $name . "' WHERE id = '" . $aks['id'] . "'");
	}

	$html = '<script type="text/javascript" src="/scripts/flotten.js"></script>
	<center>
	<table class="table">
	<tr height="20">
	<td colspan="9" class="c">Флоты в совместной атаке</td>
	</tr>
	<tr height="20">
	<th>ID</th>
	<th>Задание</th>
	<th> Кол-во</th>
	<th>Отправлен</th>
	<th>Прибытие (цель)</th>
	<th>Цель</th>
	<th>Прибытие (возврат)</th>
	<th>Прибудет через</th>
	<th>Планета старта</th>
	</tr>';

	if ($fleet['fleet_group'] == 0)
		$fq = db::query("SELECT * FROM game_fleets WHERE fleet_id = " . $fleetid . "");
	else
		$fq = db::query("SELECT * FROM game_fleets WHERE fleet_group = " . $fleet['fleet_group'] . "");

	$i = 0;
	while ($f = db::fetch($fq))
	{
		$i++;

		$html .= "<tr height=20><th>$i</th><th>";

		$html .= "<a title=\"\">"._getText('type_mission', $f['fleet_mission'])."</a>";
		if (($f['fleet_start_time'] + 1) == $f['fleet_end_time'])
			$html .= " <a title=\"R&uuml;ckweg\">(F)</a>";
		$html .= "</th><th><a title=\"";

		$fleets = explode(";", $f['fleet_array']);
		$fleets_count = 0;
		$e = 0;

		foreach ($fleets as $a => $b)
		{
			if ($b != '')
			{
				$e++;
				$a = explode(",", $b);
				$b = explode("!", $a[1]);

				$html .= _getText('tech', $a[0]).": {$b[0]}\n";
				if ($e > 1)
				{
					$html .= "\t";
				}

				$fleets_count += $b[0];
			}
		}

		$html .= "\">" . strings::pretty_number($fleets_count) . "</a></th>";
		$html .= "<th>".GetStartAdressLink($f)."</th>";
		$html .= "<th>" . datezone("d.m H:i:s", $f['fleet_start_time']) . "</th>";
		$html .= "<th>".GetTargetAdressLink($f)."</th>";
		$html .= "<th>" . datezone("d.m H:i:s", $f['fleet_end_time']) . "</th>";
		$html .= " </form>";

		$html .= "<th><font color=\"lime\"><div id=\"time_0\"><font>" . strings::pretty_time(floor($f['fleet_end_time'] + 1 - time())) . "</font></th><th>";
		$html .= $f['fleet_owner_name'] . "</th>";
		$html .= "</div></font></tr>";
	}

	if ($i == 0)
	{
		$html .= "<th colspan='9'>-</th>";
	}
	$html .= '</table></center>';

	if ($fleet['fleet_group'] == 0)
	{
		$html .= '<form action="?set=fleet&page=verband" method="POST">
			<input type="hidden" name="fleetid" value="' . $fleetid . '" />
			<input type="hidden" name="action" value="addaks" />
			<table width="100%" border="0" cellpadding="0" cellspacing="1">
			<tr height="20">
				<td class="c" colspan="2">Создание ассоциации флота</td>
			</tr>
			<tr>
				<th colspan="2"><input name="groupname" value="AKS' . mt_rand(100000, 999999999) . '" size=50 /> <br /> <input type="submit" value="Создать" /></th>
			</tr>
			</table>
		</form>';
	}
	elseif ($fleetid == $aks['fleet_id'])
	{
		$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="1">
		<tr height="20">
		<td class="c" colspan="2">Ассоциация флота ' . $aks['name'] . '</td>
		</tr>
		<tr>
			<th colspan="2">
				<form action="?set=fleet&page=verband" method="POST">
					<input type="hidden" name="fleetid" value="' . $fleetid . '" />
					<input type="hidden" name="action" value="changename" />
					<input name="groupname" value="' . $aks['name'] . '" size=50 /> <br /> <input type="submit" value="Изменить" />
				</form>
			</th>
		</tr>
		<tr>
		<th>
		<table width="100%" border="0" cellpadding="0" cellspacing="1">
		<tr height="20">
		<td class="c">Приглашенные участники</td>
		<td class="c">Пригласить участников</td>
		</tr>
		<tr>
		<th width="50%">
		<select size="5" style="width:100%;">';

		$query = db::query("SELECT game_users.username FROM game_users, game_aks_user WHERE game_users.id = game_aks_user.user_id AND game_aks_user.aks_id = " . $fleet['fleet_group'] . "", '');

		if (db::num_rows($query) == 0)
			$html .= "<option>нет участников</option>";

		while ($us = db::fetch_assoc($query))
		{
			$html .= "<option>" . $us['username'] . "</option>";
		}

		$html .= '</select>
		</th>

		<td>
		<form action="?set=fleet&page=verband" method="POST">
			<input type="hidden" name="fleetid" value="' . $fleetid . '" />
			<input type="hidden" name="action" value="adduser" />
			<input name="addtogroup" size="40" placeholder="Введите игровой ник" /><br><input type="submit" value="OK" />
		</form>
		</td>

		</tr>
		</table>
		</th>
		</tr><tr></tr>
		</table>';
	}
}

$this->display($html, "Совместная атака");

?>
