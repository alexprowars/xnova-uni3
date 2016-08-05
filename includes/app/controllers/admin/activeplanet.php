<?php

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 2)
{
	$result = array();
	$result['rows'] = array();

	$start = isset($_GET['p']) ? intval($_GET['p']) : 0;
	$limit = 25;

	if (isset($_GET['sort']))
	{
		$sort 	= $_GET['sort'];
		$d 		= $_GET['dir'];

		switch ($sort['property'])
		{
			case 'name':
				$s = 'id';
			break;
			case 'position':
				$s = 'galaxy '.$d.', system '.$d.', planet';
			break;
			case 'activity':
				$s = 'last_update';
			break;
			default:
				$s = 'id';
		}
	}
	else
	{
		$s = 'name';
		$d = 'ASC';
	}

	$AllActivPlanet = db::query("SELECT `name`, `galaxy`, `system`, `planet`, `last_update` FROM game_planets WHERE `last_update` >= '" . (time() - 15 * 60) . "' ORDER BY `" . $s . "` ".$d." LIMIT ".$start.",".$limit."");

	while ($ActivPlanet = db::fetch_assoc($AllActivPlanet))
	{
		$result['rows'][] = array
		(
			'name' 		=> $ActivPlanet['name'],
			'position' 	=> BuildPlanetAdressLink($ActivPlanet),
			'activity' 	=> (time() - $ActivPlanet['last_update'])
		);
	}

	$result['total'] = db::first(db::query("SELECT COUNT(id) AS num FROM game_planets WHERE `last_update` >= '" . (time() - 15 * 60) . "'", true));

	$this->setTemplate('activeplanet');
	$this->set('parse', $result);

	$pagination = strings::pagination($result['total'], $limit, '?set=admin&mode=activeplanet', $start);
	$this->set('pagination', $pagination);

	$this->display('', _getText('adm_pl_title'), false, true);
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>