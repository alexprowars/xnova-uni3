<?php

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 2)
{
	$parse = array();
	$parse['moon'] = '';

	$query = db::query("SELECT * FROM game_planets WHERE planet_type='3' ORDER BY galaxy,system,planet");
	$i = 0;

	while ($u = db::fetch($query))
	{
		$parse['moon'] .= "<tr>"
				. "<td>" . $u['id'] . "</td>"
				. "<td>" . $u['name'] . "</td>"
				. "<td>" . $u['parent_planet'] . "</td>"
				. "<td>" . $u['galaxy'] . "</td>"
				. "<td>" . $u['system'] . "</td>"
				. "<td>" . $u['planet'] . "</td>"
				. "</tr>";
		$i++;
	}

	if ($i == 1)
		$parse['count'] = "В игре одна луна";
	else
		$parse['count'] = "В игре {$i} лун";

	$this->setTemplate('moonlist');
	$this->set('parse', $parse);

	$this->display('', 'Список лун', false, true);
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>