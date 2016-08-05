<?php

if (!defined("INSIDE"))
	die("attemp hacking");

strings::includeLang('admin/settings');

if (user::get()->data['authlevel'] >= 3)
{
	if (isset($_POST['save']))
	{
		foreach ($_POST['setting'] AS $key => $value)
		{
			core::updateConfig($key, addslashes($value));
		}

		core::clearConfig();

		$this->message('Настройки игры успешно сохранены!', 'Выполнено');
	}
	else
	{
		$parse = array();
		$parse['settings'] = array();

		$settings = db::query("SELECT * FROM game_config ORDER BY `key`");

		while ($setting = db::fetch_assoc($settings))
		{
			$parse['settings'][] = $setting;
		}

		$this->setTemplate('options');
		$this->set('parse', $parse);

		$this->display('', _getText('adm_opt_title'), false, true);
	}
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>