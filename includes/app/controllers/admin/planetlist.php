<?php

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= "2")
{
	$this->setTemplate('planetlist');

	$p = @intval($_GET['p']);
	if ($p < 1)
		$p = 1;

	$list = db::query("SELECT `id`, `name`, `galaxy`, `system`, `planet` FROM game_planets WHERE planet_type = '1' ORDER by id LIMIT " . (($p - 1) * 50) . ", 50");

	$total = db::query("SELECT COUNT(*) AS num FROM game_planets WHERE planet_type = '1'", true);

	$this->set('planetlist', db::extractResult($list));
	$this->set('all', $total['num']);

	$pagination = strings::pagination($total['num'], 50, '?set=admin&mode=planetlist', $p);

	$this->set('pagination', $pagination);

	$this->display('', 'Список планет', false, true);
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>