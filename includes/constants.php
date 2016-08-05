<?php

if (!defined('INSIDE'))
	die("Hacking attempt");

$serverList = Array
(
	'UNI3' 	=> Array
	(
		'LOCATION'	=> 'uni3.xnova.su',
		'ROOT_DIR'	=> '/var/www/xnova/data/www/uni3.xnova.su/'
	)
);

define('VERSION'				  , '2.2 REV329');
define('DEFAULT_SKINPATH'		  , '/skins/default/');
define('ADMINEMAIL'               , "info@xnova.su");
define('TIMEZONE'				  , 'Europe/Moscow');
define('UTF8_SUPPORT'             , true);
define('CORE_PATH'				  , 'includes/core/');
define('APP_PATH'				  , 'includes/app/');
define('LIB_PATH'				  , 'includes/lib/');

?>