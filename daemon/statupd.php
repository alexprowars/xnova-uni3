<?php

if (!extension_loaded('memcache'))
{
	dl('memcache.so');
}

$updateTime = time();

$maxinfos = array();

function SetMaxInfo ($ID, $Count, $Data)
{
	global $maxinfos;

	if ($Data['authlevel'] == 3 || $Data['banaday'] != 0)
		return;

	if (!isset($maxinfos[$ID]))
		$maxinfos[$ID] = array('maxlvl' => 0, 'username' => '');

	if ($maxinfos[$ID]['maxlvl'] < $Count)
		$maxinfos[$ID] = array('maxlvl' => $Count, 'username' => $Data['username']);
}

if (!$_SERVER['DOCUMENT_ROOT'])
	$_SERVER['DOCUMENT_ROOT'] = '/var/www/xnova/data/www/uni3.xnova.su';

define('INSIDE', true);

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/class.core.php');

core::init('UNI3');
core::loadConfig();

include(ROOT_DIR.APP_PATH.'functions/functions.php');
include(ROOT_DIR.APP_PATH.'varsGlobal.php');
include(ROOT_DIR.APP_PATH."functions/statfunctions.php");

$StatDate = time();

$Message = "";

$StatRace = array(
	1 => array('count' => 0, 'total' => 0, 'fleet' => 0, 'tech' => 0, 'defs' => 0, 'build' => 0),
	2 => array('count' => 0, 'total' => 0, 'fleet' => 0, 'tech' => 0, 'defs' => 0, 'build' => 0),
	3 => array('count' => 0, 'total' => 0, 'fleet' => 0, 'tech' => 0, 'defs' => 0, 'build' => 0),
	4 => array('count' => 0, 'total' => 0, 'fleet' => 0, 'tech' => 0, 'defs' => 0, 'build' => 0),
);

// Включение режима удаления у неактивных игроков
$Del_TimeS = time() + 86400 * 7; // 7 дней на удаление аккаунта
$Time_Online = time() - 60 * 60 * 24 * 21; // удалять если не активен 21 день
// Удалять если не забанен и не в режиме отпуска
$Spr_Online = db::query("SELECT * FROM game_users WHERE `onlinetime` < '{$Time_Online}' AND `onlinetime` > '0' AND (`urlaubs_modus_time` = '0' OR (urlaubs_modus_time < " . time() . " - 15184000 AND urlaubs_modus_time > 1)) AND `banaday` = '0' AND `deltime` = '0' ORDER BY onlinetime LIMIT 75");
while ($OnlineS = db::fetch($Spr_Online))
{
	db::query("UPDATE game_users SET `deltime` = '" . $Del_TimeS . "' WHERE `id` = '" . $OnlineS['id'] . "'");
	$Message .= "Включение удаления у " . $OnlineS['username'] . ": ОК<br>";
}

// Выбираем кандидатов на удаление
$Del_Time = time();
$Spr_Del = db::query("SELECT * FROM game_users WHERE `deltime` < '{$Del_Time}' AND `deltime`> '0'");

// Полное очищение игры от удалённого аккаунта
while ($TheUser = db::fetch_assoc($Spr_Del))
{
	$UserID = $TheUser['id'];

	$Message .= "Удаление аккаунта " . $TheUser['username'] . ": ОК<br>";

	if ($TheUser['ally_id'] != 0)
	{
		$TheAlly = db::query("SELECT * FROM game_alliance WHERE `id` = '" . $TheUser['ally_id'] . "';", true);
		$TheAlly['ally_members'] -= 1;
		if ($TheAlly['ally_members'] > 0 && $TheAlly['ally_owner'] != $UserID)
		{
			db::query("UPDATE game_alliance SET `ally_members` = '" . $TheAlly['ally_members'] . "' WHERE `id` = '" . $TheAlly['id'] . "';");
			db::query("DELETE FROM game_alliance_members WHERE `u_id` = '" . $UserID . "';");
		}
		else
		{
			if ($TheAlly['ally_members'] > 1)
			{
				db::query("UPDATE game_users SET `ally_id` = '0', `ally_name` = '' WHERE ally_id = '" . $TheAlly['id'] . "' AND id != " . $UserID . "");
			}
			db::query("DELETE FROM game_alliance WHERE `id` = '" . $TheAlly['id'] . "';");
			db::query("DELETE FROM game_alliance_members WHERE a_id = '" . $TheAlly['id'] . "'");
			db::query("DELETE FROM game_alliance_requests WHERE a_id = '" . $TheAlly['id'] . "'");
			db::query("DELETE FROM game_alliance_diplomacy WHERE a_id = '" . $TheAlly['id'] . "' OR d_id = '" . $TheAlly['id'] . "';");
			db::query("DELETE FROM game_statpoints WHERE `stat_type` = '2' AND `id_owner` = '" . $TheAlly['id'] . "';");
		}
	}

	db::query("DELETE FROM game_alliance_requests WHERE `u_id` = '" . $UserID . "';");
	db::query("DELETE FROM game_statpoints WHERE `stat_type` = '1' AND `id_owner` = '" . $UserID . "';");
	db::query("DELETE FROM game_planets WHERE `id_owner` = '" . $UserID . "';");
	db::query("DELETE FROM game_messages WHERE `message_sender` = '" . $UserID . "';");
	db::query("DELETE FROM game_messages WHERE `message_owner` = '" . $UserID . "';");
	db::query("DELETE FROM game_notes WHERE `owner` = '" . $UserID . "';");
	db::query("DELETE FROM game_fleets WHERE `fleet_owner` = '" . $UserID . "';");
	db::query("DELETE FROM game_buddy WHERE `sender` = '" . $UserID . "';");
	db::query("DELETE FROM game_buddy WHERE `owner` = '" . $UserID . "';");
	db::query("DELETE FROM game_refs WHERE `r_id` = '" . $UserID . "' OR `u_id` = '" . $UserID . "';");
	db::query("DELETE FROM game_users WHERE `id` = '" . $UserID . "';");
	db::query("DELETE FROM game_users_inf WHERE `id` = '" . $UserID . "';");
	db::query("DELETE FROM game_banned WHERE `who` = '" . $UserID . "';");
	db::query("DELETE FROM game_log_attack WHERE `uid` = '" . $UserID . "';");
	db::query("DELETE FROM game_log_credits WHERE `uid` = '" . $UserID . "';");
	db::query("DELETE FROM game_log_ip WHERE `id` = '" . $UserID . "';");
	db::query("DELETE FROM game_logs WHERE `s_id` = '" . $UserID . "' OR `e_id` = '" . $UserID . "';");
	//db::query("UPDATE game_config SET `config_value`=`config_value`-1 WHERE `config_name` = 'users_amount';");

}

// Чистим старьё
db::query("DELETE FROM game_statpoints WHERE `stat_code` >= 2");
db::query("UPDATE game_statpoints SET `stat_code` = `stat_code` + '1';");

$active_users = 0;
$active_alliance = 0;

// Делаем выборку игрока и его очков в статистике
$GameUsers = db::query("SELECT u.*, s.total_rank, s.tech_rank, s.fleet_rank, s.build_rank, s.defs_rank FROM (game_users u, game_users_inf ui) LEFT JOIN game_statpoints s ON s.id_owner = u.id AND s.stat_type = 1 WHERE ui.id = u.id AND u.authlevel < 3 AND u.banaday = 0");
// Удаляем статистику игроков
db::query("DELETE FROM game_statpoints WHERE `stat_type` = '1';");
// Делаем выборку флотов и расчитываем очки
$FleetPoints = array();
$UsrFleets = db::query("SELECT * FROM game_fleets");

while ($CurFleet = db::fetch_assoc($UsrFleets))
{
	$Points = GetFleetPointsOnTour($CurFleet['fleet_array']);

	if (!isset($FleetPoints[$CurFleet['fleet_owner']]))
	{
		$FleetPoints[$CurFleet['fleet_owner']] = array();
		$FleetPoints[$CurFleet['fleet_owner']]['points'] = 0;
		$FleetPoints[$CurFleet['fleet_owner']]['count'] = 0;
		$FleetPoints[$CurFleet['fleet_owner']]['array'] = array();
	}

	$FleetPoints[$CurFleet['fleet_owner']]['points'] += ($Points['FleetPoint'] / 1000);
	$FleetPoints[$CurFleet['fleet_owner']]['count'] += $Points['FleetCount'];
	$FleetPoints[$CurFleet['fleet_owner']]['array'][] = $Points['fleet_array'];
}

// Просчитываем очки каждого игрока
while ($CurUser = db::fetch_assoc($GameUsers))
{
	$options = user::get()->unpackOptions($CurUser['options_toggle']);
	$CurUser['records'] = $options['records'];

	if ($CurUser['banaday'] != 0 || ($CurUser['urlaubs_modus_time'] != 0 && $CurUser['urlaubs_modus_time'] < (time() - 1036800)))
		$hide = 1;
	else
		$hide = 0;

	if ($hide == 0)
		$active_users++;

	// Запоминаем старое место в стате
	if ($CurUser['total_rank'] != "")
	{
		$OldTotalRank = $CurUser['total_rank'];
		$OldTechRank = $CurUser['tech_rank'];
		$OldFleetRank = $CurUser['fleet_rank'];
		$OldBuildRank = $CurUser['build_rank'];
		$OldDefsRank = $CurUser['defs_rank'];
	}
	else
	{
		$OldTotalRank = 0;
		$OldTechRank = 0;
		$OldBuildRank = 0;
		$OldDefsRank = 0;
		$OldFleetRank = 0;
	}

	// Вычисляем очки исследований
	$Points = GetTechnoPoints($CurUser);
	$TTechCount = $Points['TechCount'];
	$TTechPoints = ($Points['TechPoint'] / 1000);

	$TBuildCount = 0;
	$TBuildPoints = 0;
	$TDefsCount = 0;
	$TDefsPoints = 0;
	$TFleetCount = 0;
	$TFleetPoints = 0;
	$GCount = $TTechCount;
	$GPoints = $TTechPoints;
	$UsrPlanets = db::query("SELECT * FROM game_planets WHERE `id_owner` = '" . $CurUser['id'] . "';");

	$RecordArray = array();

	while ($CurPlanet = db::fetch_assoc($UsrPlanets))
	{
		$Points = GetBuildPoints($CurPlanet, $CurUser);
		$TBuildCount += $Points['BuildCount'];
		$GCount += $Points['BuildCount'];
		$PlanetPoints = ($Points['BuildPoint'] / 1000);
		$TBuildPoints += ($Points['BuildPoint'] / 1000);

		$Points = GetDefensePoints($CurPlanet, $RecordArray);
		$TDefsCount += $Points['DefenseCount'];
		;
		$GCount += $Points['DefenseCount'];
		$PlanetPoints += ($Points['DefensePoint'] / 1000);
		$TDefsPoints += ($Points['DefensePoint'] / 1000);

		$Points = GetFleetPoints($CurPlanet, $RecordArray);
		$TFleetCount += $Points['FleetCount'];
		$GCount += $Points['FleetCount'];
		$PlanetPoints += ($Points['FleetPoint'] / 1000);
		$TFleetPoints += ($Points['FleetPoint'] / 1000);

		$GPoints += $PlanetPoints;
	}

	// Складываем очки флота
	if (isset($FleetPoints[$CurUser['id']]['points']))
	{
		$TFleetCount += $FleetPoints[$CurUser['id']]['count'];
		$GCount += $FleetPoints[$CurUser['id']]['count'];
		$TFleetPoints += $FleetPoints[$CurUser['id']]['points'];
		$PlanetPoints = $FleetPoints[$CurUser['id']]['points'];
		$GPoints += $PlanetPoints;

		foreach ($FleetPoints[$CurUser['id']]['array'] AS $fleet)
		{
			foreach ($fleet AS $id => $amount)
			{
				if (isset($RecordArray[$id]))
					$RecordArray[$id] += $amount;
				else
					$RecordArray[$id] = $amount;
			}
		}
	}

	if ($CurUser['records'] == 1)
	{
		foreach ($RecordArray AS $id => $amount)
		{
			SetMaxInfo($id, $amount, $CurUser);
		}
	}

	if ($CurUser['race'] != 0)
	{
		$StatRace[$CurUser['race']]['count'] += 1;
		$StatRace[$CurUser['race']]['total'] += $GPoints;
		$StatRace[$CurUser['race']]['fleet'] += $TFleetPoints;
		$StatRace[$CurUser['race']]['tech'] += $TTechPoints;
		$StatRace[$CurUser['race']]['build'] += $TBuildPoints;
		$StatRace[$CurUser['race']]['defs'] += $TDefsPoints;
	}

	// Заносим данные в таблицу
	$QryInsertStats = "INSERT INTO game_statpoints SET ";
	$QryInsertStats .= "`id_owner` = '" . $CurUser['id'] . "', ";
	$QryInsertStats .= "`username` = '" . $CurUser['username'] . "', ";
	$QryInsertStats .= "`race` = '" . $CurUser['race'] . "', ";
	$QryInsertStats .= "`id_ally` = '" . $CurUser['ally_id'] . "', ";
	$QryInsertStats .= "`ally_name` = '" . $CurUser['ally_name'] . "', ";
	$QryInsertStats .= "`stat_type` = '1', ";
	$QryInsertStats .= "`stat_code` = '1', ";
	$QryInsertStats .= "`tech_points` = '" . $TTechPoints . "', ";
	$QryInsertStats .= "`tech_count` = '" . $TTechCount . "', ";
	$QryInsertStats .= "`tech_old_rank` = '" . $OldTechRank . "', ";
	$QryInsertStats .= "`build_points` = '" . $TBuildPoints . "', ";
	$QryInsertStats .= "`build_count` = '" . $TBuildCount . "', ";
	$QryInsertStats .= "`build_old_rank` = '" . $OldBuildRank . "', ";
	$QryInsertStats .= "`defs_points` = '" . $TDefsPoints . "', ";
	$QryInsertStats .= "`defs_count` = '" . $TDefsCount . "', ";
	$QryInsertStats .= "`defs_old_rank` = '" . $OldDefsRank . "', ";
	$QryInsertStats .= "`fleet_points` = '" . $TFleetPoints . "', ";
	$QryInsertStats .= "`fleet_count` = '" . $TFleetCount . "', ";
	$QryInsertStats .= "`fleet_old_rank` = '" . $OldFleetRank . "', ";
	$QryInsertStats .= "`total_points` = '" . $GPoints . "', ";
	$QryInsertStats .= "`total_count` = '" . $GCount . "', ";
	$QryInsertStats .= "`total_old_rank` = '" . $OldTotalRank . "', ";
	$QryInsertStats .= "`stat_hide` = '" . $hide . "';";
	db::query($QryInsertStats);
}


$qryResetRowNum = 'SET @rownum=0;';
$qryFormat = 'UPDATE game_statpoints SET `%1$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = %2$d AND `stat_code` = 1 AND stat_hide = 0 ORDER BY `%1$s_points` DESC, `id_owner` ASC;';
$rankNames = array('tech', 'fleet', 'defs', 'build', 'total');

foreach ($rankNames as $rankName)
{
	db::query($qryResetRowNum);
	db::query(sprintf($qryFormat, $rankName, 1));
}

$Message .= "Обновление статистики игроков: ОК<br>";

db::query("INSERT INTO game_statpoints
      (`tech_points`, `tech_count`, `build_points`, `build_count`, `defs_points`, `defs_count`,
        `fleet_points`, `fleet_count`, `total_points`, `total_count`, `id_owner`, `id_ally`, `stat_type`, `stat_code`,
        `tech_old_rank`, `build_old_rank`, `defs_old_rank`, `fleet_old_rank`, `total_old_rank`
      )
      SELECT
        SUM(u.`tech_points`), SUM(u.`tech_count`), SUM(u.`build_points`), SUM(u.`build_count`), SUM(u.`defs_points`),
        SUM(u.`defs_count`), SUM(u.`fleet_points`), SUM(u.`fleet_count`), SUM(u.`total_points`), SUM(u.`total_count`),
        u.`id_ally`, 0, 2, 1,
        a.tech_rank, a.build_rank, a.defs_rank, a.fleet_rank, a.total_rank
      FROM game_statpoints as u
        LEFT JOIN game_statpoints as a ON a.id_owner = u.id_ally AND a.stat_code = 2 AND a.stat_type = 2
      WHERE u.`stat_type` = 1 AND u.stat_code = 1 AND u.id_ally<>0
      GROUP BY u.`id_ally`");

db::query("UPDATE game_statpoints as new
      LEFT JOIN game_statpoints as old ON old.id_owner = new.id_owner AND old.stat_code = 2 AND old.stat_type = 1
    SET
      new.tech_old_rank = old.tech_rank,
      new.build_old_rank = old.build_rank,
      new.defs_old_rank  = old.defs_rank ,
      new.fleet_old_rank = old.fleet_rank,
      new.total_old_rank = old.total_rank
    WHERE
      new.stat_type = 2 AND new.stat_code = 2;");

db::query("DELETE FROM game_statpoints WHERE `stat_code` >= 2");

foreach ($rankNames as $rankName)
{
	db::query($qryResetRowNum);
	db::query(sprintf($qryFormat, $rankName, 2));
}

foreach ($StatRace AS $race => $arr)
{
	$QryInsertStats = "INSERT INTO game_statpoints SET ";
	$QryInsertStats .= "`race` = '" . $race . "', ";
	$QryInsertStats .= "`stat_type` = '3', ";
	$QryInsertStats .= "`stat_code` = '1', ";
	$QryInsertStats .= "`tech_points` = '" . $arr['tech'] . "', ";
	$QryInsertStats .= "`build_points` = '" . $arr['build'] . "', ";
	$QryInsertStats .= "`defs_points` = '" . $arr['defs'] . "', ";
	$QryInsertStats .= "`fleet_points` = '" . $arr['fleet'] . "', ";
	$QryInsertStats .= "`total_count` = '" . $arr['count'] . "', ";
	$QryInsertStats .= "`total_points` = '" . $arr['total'] . "';";
	db::query($QryInsertStats);
}

foreach ($rankNames as $rankName)
{
	db::query($qryResetRowNum);
	db::query(sprintf($qryFormat, $rankName, 3));
}

db::query("OPTIMIZE TABLE game_statpoints");

// Запись статистики в лог
db::query("INSERT INTO game_log_stats
	(`tech_points`, `tech_rank`, `build_points`, `build_rank`, `defs_points`, `defs_rank`, `fleet_points`, `fleet_rank`, `total_points`, `total_rank`, `id`, `type`, `time`)
	SELECT
		u.`tech_points`, u.`tech_rank`, u.`build_points`, u.`build_rank`, u.`defs_points`,
        u.`defs_rank`, u.`fleet_points`, u.`fleet_rank`, u.`total_points`, u.`total_rank`,
        u.`id_owner`, 1, ".$updateTime."
    FROM game_statpoints as u
    WHERE
    	u.`stat_type` = 1 AND u.stat_code = 1");

db::query("INSERT INTO game_log_stats
	(`tech_points`, `tech_rank`, `build_points`, `build_rank`, `defs_points`, `defs_rank`, `fleet_points`, `fleet_rank`, `total_points`, `total_rank`, `id`, `type`, `time`)
	SELECT
		u.`tech_points`, u.`tech_rank`, u.`build_points`, u.`build_rank`, u.`defs_points`,
        u.`defs_rank`, u.`fleet_points`, u.`fleet_rank`, u.`total_points`, u.`total_rank`,
        u.`id_owner`, 2, ".$updateTime."
    FROM game_statpoints as u
    WHERE
    	u.`stat_type` = 2 AND u.stat_code = 1");
// Конец

$active_alliance = db::first(db::query("SELECT COUNT(*) AS num FROM game_statpoints WHERE `stat_type` = '2' AND `stat_hide` = 0;", true));

$Message .= "Обновление статистики альянсов: ОК<br>";

// Чистим старые логи
db::query("DELETE FROM game_messages WHERE `message_time` <= '" . (time() - 432000) . "';");
db::query("DELETE FROM game_rw WHERE `time` <= '" . (time() - 172800) . "';");
db::query("DELETE FROM game_chat WHERE `timestamp` <= '" . (time() - 604800) . "';");
db::query("DELETE FROM game_lostpwd WHERE `time` <= '" . (time() - 86400) . "';");
db::query("DELETE FROM game_logs WHERE `time` <= '" . (time() - 259200) . "';");
db::query("DELETE FROM game_log_attack WHERE `time` <= '" . (time() - 604800) . "';");
db::query("DELETE FROM game_log_credits WHERE `time` <= '" . (time() - 604800) . "';");
db::query("DELETE FROM game_log_ip WHERE `time` <= '" . (time() - 604800) . "';");
db::query("DELETE FROM game_log_load WHERE `time` <= '" . (time() - 604800) . "';");
db::query("DELETE FROM game_log_history WHERE `time` <= '" . (time() - 604800) . "';");

$Message .= "Удаление старых логов: ОК<br>";

core::updateConfig('stat_update', time());
core::updateConfig('active_users', $active_users);
core::updateConfig('active_alliance', $active_alliance);

$Message .= "Обновление конфигурации: ОК<br>";

$Elements = array_merge($reslist['build'], $reslist['tech'], $reslist['fleet'], $reslist['defense']);

$array = "";
foreach ($Elements as $ElementID)
{
	if ($ElementID != 407 && $ElementID != 408)
		$array .= $ElementID . " => array('username' => '" . (isset($maxinfos[$ElementID]['username']) ? $maxinfos[$ElementID]['username'] : '-') . "', 'maxlvl' => '" . (isset($maxinfos[$ElementID]['maxlvl']) ? $maxinfos[$ElementID]['maxlvl'] : '-') . "'),\n";
}
$file = "<?php \n//The File is created on " . date("d. M y H:i:s", time()) . "\n$" . "RecordsArray = array(\n" . $array . "\n);\n?>";

if (!file_exists(ROOT_DIR . CACHE_DIR))
	mkdir(ROOT_DIR . CACHE_DIR, 0777);

file_put_contents(ROOT_DIR.CACHE_DIR."/CacheRecords.php", $file);

$Message .= "Обновление рекордов: ОК<br>";

echo $Message;

/*
$lastRefersUpdate = core::getConfig('lastRefersUpdate', 0);

if (system::startOfDay() > $lastRefersUpdate)
{
	$user = db::query("SELECT id, COUNT(*) AS cnt FROM game_moneys WHERE time >= ".system::startOfDay($lastRefersUpdate)." AND time <= ".system::endOfDay($lastRefersUpdate)." AND `user_agent` NOT LIKE '%vkShare%' GROUP BY id ORDER BY cnt DESC LIMIT 1", true);

	if ($user['cnt'] >= 5)
	{
		$credits = core::getConfig('refersCreditBonus', 5);

		sql::build()->update('game_users')->setField('+credits', $credits)->where('id', '=', $user['id'])->execute();

		sql::build()->insert('game_log_refers')->set(Array
		(
			'time' 		=> system::startOfDay(),
			'user_id' 	=> $user['id'],
			'refers' 	=> $user['cnt']
		))->execute();

		user::get()->sendMessage($user['id'], 0, 0, 1, 'Участие в реферальной программе', 'На ваш счет зачислено '.$credits.' кредитов за участие в реферальной программе. Ваш реферальный счет за прошедший день: '.$user['cnt'].' очков.');
	}

	core::updateConfig('lastRefersUpdate', system::startOfDay());

	echo 'Просчитываем реферальную программу: OK<br>';
}
*/

core::clearConfig();

?>
