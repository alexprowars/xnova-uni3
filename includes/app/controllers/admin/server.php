<?php

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 3)
{
	$this->setTemplate('server');

	$this->display('', 'Серверное окружение', false);
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>