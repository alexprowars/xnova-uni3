<?php

include_once(ROOT_DIR.APP_PATH."varsGlobal.php");
include_once(ROOT_DIR.APP_PATH."functions/functions.php");

class app
{
	/**
	 * @var user $user
	 */
	static $user;
	/**
	 * @var planet $planetrow
	 */
	static $planetrow;

	public function __construct () {}

	public static function init ()
	{
		global $page;

		if (function_exists('sys_getloadavg'))
		{
			$load = sys_getloadavg();

			if ($load[0] > 25)
			{
				header('HTTP/1.1 503 Too busy, try again later');
				die('Server too busy. Please try again later.');
			}
		}

		if (self::$user instanceof user)
			die('kernel panic');

		self::$user = user::get();

		if (user::get()->isAuthorized())
		{
			// Кэшируем настройки профиля в сессию
			if (!isset($_SESSION['config']) || strlen($_SESSION['config']) < 10)
			{
				$inf = db::query("SELECT design, planet_sort, planet_sort_order, color, timezone, spy FROM game_users_inf WHERE id = " . user::get()->getId() . ";", true);
				$_SESSION['config'] = json_encode($inf);
			}

			if (!core::getConfig('showPlanetListSelect', 0))
				core::setConfig('showPlanetListSelect', user::get()->getUserOption('planetlistselect'));

			if (request::G('fullscreen') == 'Y')
			{
				setcookie(COOKIE_NAME."_full", "Y", (time() + 30 * 86400), "/", $_SERVER["SERVER_NAME"], 0);
				$_COOKIE[COOKIE_NAME."_full"] = 'Y';
			}

			if (isset($_COOKIE[COOKIE_NAME."_full"]) && $_COOKIE[COOKIE_NAME."_full"] = 'Y')
			{
				core::setConfig('socialIframeView', 0);
				core::setConfig('overviewListView', 1);
				core::setConfig('showPlanetListSelect', 0);
			}
			
			if (SERVER_CODE == 'OK1U')
			{
				core::setConfig('overviewListView', 1);
				core::setConfig('showPlanetListSelect', 0);
				core::setConfig('socialIframeView', 2);
			}

			// Заносим настройки профиля в основной массив
			$inf = json_decode($_SESSION['config'], true);
			user::get()->data = array_merge(user::get()->data, $inf);
			user::get()->getAllyInfo();

			if (!user::get()->isAdmin())
				core::setConfig('DEBUG', false);

			if (SERVER_CODE == 'OK1U')
			{
				$points = db::first(db::query("SELECT `total_points` FROM game_statpoints WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . user::get()->getId() . "';", true));

				if (!$points || $points < 1000)
				{
					core::setConfig('game_speed', core::getConfig('game_speed') * 5);
					core::setConfig('resource_multiplier', core::getConfig('resource_multiplier') * 3);
					core::setConfig('noob', 1);
				}
			}

			// Выставляем планету выбранную игроком из списка планет
			user::get()->setSelectedPlanet();

			if (user::get()->data['race'] == 0 && $page != 'infos' && $page != 'content')
				$page = 'race';
		}
	}

	static public function loadPlanet ()
	{
		if (app::$planetrow instanceof planet)
			return;

		global $page;

		if (user::get()->data['current_planet'] == 0 && user::get()->data['id_planet'] == 0)
		{
			user::get()->data['current_planet'] = system::CreateRegPlanet(user::get()->getId());
			user::get()->data['id_planet'] 		= user::get()->data['current_planet'];
		}

		// Выбираем информацию о планете
		self::$planetrow = new planet(user::get()->data['current_planet']);
		self::$planetrow->load_user_info(user::get());
		self::$planetrow->checkOwnerPlanet();

		// Проверяем корректность заполненных полей
		self::$planetrow->CheckPlanetUsedFields();

		if (isset(self::$planetrow->data['id']))
		{
			// Обновляем ресурсы на планете когда это необходимо
			if ((($page == "overview" || ($page == "fleet" && @$_GET['page'] != 'fleet_3') || $page == "galaxy" || $page == "resources" || $page == "imperium" || $page == "infokredits" || $page == "tutorial" || $page == "techtree" || $page == "search" || $page == "support" || $page == "sim" || $page == "tutorial" || !$page) && self::$planetrow->data['last_update'] > (time() - 60)))
				self::$planetrow->PlanetResourceUpdate(time(), true);
			else
				self::$planetrow->PlanetResourceUpdate();
		}

		// Проверка наличия законченных построек и исследований
		if (self::$planetrow->UpdatePlanetBatimentQueueList())
			self::$planetrow->PlanetResourceUpdate(time(), true);
	}
}