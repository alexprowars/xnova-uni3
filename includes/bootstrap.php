<?php

/**
 * Файл полной инициализации игрового движка
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * @global $page page Объект шаблонизатора
 * @global $user user Объект пользователя
 * @global $planetrow planet Объект активной планеты
 * @global $session session Объект сессии
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if (!defined('INSIDE'))
	die("Hacking attempt");

header("Content-type: text/html; charset=utf-8");
header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
header('Access-Control-Allow-Origin: *');

if (core::getConfig('DEBUG'))
{
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}
else
	error_reporting(E_ALL ^ E_NOTICE);

request::parseUrl();

// Показывать админу все ошибки скрипта
if (isset($_COOKIE['x_id']) && ($_COOKIE['x_id'] == '1' || $_SERVER["SERVER_NAME"] == 'xnova' || isset($_GET['alexpro'])))
	error_reporting(E_ALL);

$page 	= request::G('set');
$mode 	= request::G('mode');

require(ROOT_DIR.'includes/core/vars.php');

if (!isset($game_modules))
	die('Повреждение модулей');

$user = user::get();

$session = new session();
$session->checkExtAuth();
$session->CheckTheUser();

if ($session->isAuthorized())
	$user->load_from_array($session->user);
else
	$session->CheckReferLink();

define('DPATH', DEFAULT_SKINPATH);

if (user::get()->isAuthorized())
{
	if (!$user->isAdmin())
		core::setConfig('DEBUG', false);

	include_once(ROOT_DIR.APP_PATH.'application.php');

	app::init();
}
else
	core::setConfig('DEBUG', false);

$page = trim(str_replace(array('_', '\\', '/', '.', "\0"), '', $page));

if (!isset($game_modules[(string) $page]))
	$page = ($session->isAuthorized()) ? core::getConfig('defaultController', 'main') : 'login';

if ($game_modules[(string) $page] == 1 && !$session->isAuthorized())
	$page = 'login';
elseif ($game_modules[(string) $page] == 2 && $session->isAuthorized())
	$page = core::getConfig('defaultController', 'main');

$pageClass	= 'show'.ucfirst($page).'Page';
$path       = ROOT_DIR.APP_PATH.'controllers/'.$pageClass.'.class.php';

if (!file_exists($path))
	message('Controller "'.(string) $page.'" not found in application', 'Fatal Error');

request::setG('set', $page);

require($path);

if (!$mode)
	$mode = core::getConfig('defaultAction', 'show');

$mode = str_replace(Array("__call", "__construct", "display"), "", $mode);

$pageObj = new $pageClass;
$pageObj->name = $page;
$pageObj->mode = $mode;

if (!method_exists($pageObj, $mode) || (method_exists($pageObj, $mode) && !in_array($mode, get_class_methods($pageObj))))
{
	$mode = core::getConfig('defaultAction', 'show');
}

$pageObj->{$mode}();