<?php

if (!extension_loaded('memcache'))
{
	dl('memcache.so');
}

$_SERVER['DOCUMENT_ROOT'] = '/var/www/xnova/data/www/uni3.xnova.su';

define('INSIDE', true);

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/class.core.php');
core::init('UNI3');

$online = db::first(db::query("SELECT COUNT(*) as `online` FROM game_users WHERE `onlinetime` > '" . (time() - ONLINETIME * 60) . "';", true));

core::updateConfig('online', $online);
core::clearConfig();

echo 'true';

?>