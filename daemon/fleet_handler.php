<?

ini_set('max_execution_time', 15);

$_SERVER['DOCUMENT_ROOT'] = '/var/www/xnova/data/www/uni3.xnova.su';

define('INSIDE', true);

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/class.core.php');
core::init('UNI3');
core::loadConfig();
strings::setLang('ru');
strings::includeLang("fleet_engine");

error_reporting(E_ALL);

include(ROOT_DIR.'includes/app/functions/functions.php');
include(ROOT_DIR.'includes/app/varsGlobal.php');

if (function_exists('sys_getloadavg'))
{
	$load = sys_getloadavg();

	sql::build()->insert('game_log_load')->set(Array
	(
		'time' 	=> time(),
		'value' => json_encode($load)
	))
	->execute();

	if ($load[0] > 25)
		die('Server too busy. Please try again later.');
}

define('MAX_RUNS', 12);
define('TIME_LIMIT', 60);

$missionObjPattern = array
(
	1	=> 'MissionCaseAttack',
	2   => 'MissionCaseACS',
	3   => 'MissionCaseTransport',
	4   => 'MissionCaseStay',
	5   => 'MissionCaseStayAlly',
	6   => 'MissionCaseSpy',
	7   => 'MissionCaseColonisation',
	8   => 'MissionCaseRecycling',
	9   => 'MissionCaseDestruction',
	10  => 'MissionCaseCreateBase',
	15  => 'MissionCaseExpedition',
	20  => 'MissionCaseRak'
);

require_once(ROOT_DIR.APP_PATH.'class/interface/missions/interface.php');

$totalRuns = 1;

while ($totalRuns < MAX_RUNS)
{
	$_fleets = array_merge
	(
		db::extractResult(db::query("SELECT * FROM game_fleets WHERE (`fleet_start_time` <= '" . time() . "' AND `fleet_mess` = '0') LIMIT 3")),
		db::extractResult(db::query("SELECT * FROM game_fleets WHERE (`fleet_end_stay` <= '" . time() . "' AND `fleet_mess` != '1' AND `fleet_end_stay` != '0') LIMIT 3")),
		db::extractResult(db::query("SELECT * FROM game_fleets WHERE (`fleet_end_time` < '" . time() . "' AND `fleet_mess` != '0') LIMIT 3"))
	);

	uasort($_fleets, function($a, $b)
	{
		return ($a['fleet_time'] <= $b['fleet_time'] ? -1 : 1);
	});

	if (count($_fleets) > 0)
	{
		foreach ($_fleets AS $fleetRow)
		{
			if (!isset($missionObjPattern[$fleetRow['fleet_mission']]))
			{
				db::query("DELETE FROM game_fleets WHERE `fleet_id` = ".$fleetId);

				continue;
			}

			$missionName = $missionObjPattern[$fleetRow['fleet_mission']];

			if (!class_exists($missionName))
				require_once(ROOT_DIR.APP_PATH.'class/interface/missions/'.$missionName.'.php');

			/**
			 * @var $mission Mission
			 */
			$mission = new $missionName($fleetRow);

			if ($fleetRow['fleet_mess'] == 0 && $fleetRow['fleet_start_time'] <= time())
			{
				$mission->TargetEvent();
			}

			if ($fleetRow['fleet_mess'] == 3 && $fleetRow['fleet_end_stay'] <= time())
			{
				$mission->EndStayEvent();
			}

			if ($fleetRow['fleet_mess'] == 1 && $fleetRow['fleet_end_time'] <= time())
			{
				$mission->ReturnEvent();
			}

			unset($mission);
		}
	}

	$totalRuns++;
	sleep(TIME_LIMIT / MAX_RUNS);
}

die('true');

?>