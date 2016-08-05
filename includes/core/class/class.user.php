<?php

/**
 * Класс пользователя
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */
class user
{
	public $data 		= array();
	private $bonus 		= array();
	private $options 	= array
	(
		'security' 			=> 0,
		'widescreen' 		=> 0,
		'bb_parser' 		=> 0,
		'ajax_navigation' 	=> 0,
		'planetlist' 		=> 0,
		'planetlistselect' 	=> 0,
		'gameactivity' 		=> 0,
		'records' 			=> 0,
		'only_available' 	=> 0,
	);

	/**
	 * @var $instance user
	 */
	private static $instance;

	public static function get()
    {
        if (!isset(self::$instance))
		{
            $className = __CLASS__;
            self::$instance = new $className;
        }

        return self::$instance;
    }

	private function __clone(){}
	public function __construct() {}

	/**
	 * Получение параметра пользователя
	 * @param $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->__isset($key) ? $this->data[$key] : null;
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	/**
	 * Загрузка параметров пользователя из массива
	 * @param array $array массив параметров
	 * @param bool $parse заполнение массива бонусов
	 */
	public function load_from_array ($array, $parse = true)
	{
		$this->data = $array;

		if ($parse)
			$this->ParseUserData();
	}

	/**
	 * Получение параметров пользователя по id
	 * @param int $user_id id пользователя
	 * @param array $fields список полей выборки
	 * @return array|bool
	 */
	public function getById ($user_id, $fields = array())
	{
		if (intval($user_id) > 0)
		{
			return db::query("SELECT " . (count($fields) ? implode(',', $fields) : '*') . " FROM game_users WHERE id = " . intval($user_id), true);
		}
		else
			return false;
	}

	public function load_from_id ($user_id, $fields = array(), $parse = true)
	{
		$user = $this->getById($user_id, $fields);

		if ($user !== false)
			$this->data = $user;
		else
			return false;

		if ($parse)
			$this->ParseUserData();

		return true;
	}

	public function bonusValue ($key)
	{
		return (isset($this->bonus[$key]) ? $this->bonus[$key] : 1);
	}

	private function ParseUserData ()
	{
		if (!isset($this->data['id']))
			return false;

		$bonusArrays = array
		(
			'storage', 'metal', 'crystal', 'deuterium', 'energy', 'solar',
			'res_fleet', 'res_defence', 'res_research', 'res_building', 'res_levelup',
			'time_fleet', 'time_defence', 'time_research', 'time_building',
			'fleet_fuel', 'fleet_speed'
		);

		// Значения по умолчанию
		foreach ($bonusArrays AS $name)
		{
			$this->bonus[$name] = 1;
		}

		// Расчет бонусов от офицеров
		if ($this->data['rpg_geologue'] > time())
		{
			$this->bonus['metal'] 		+= 0.25;
			$this->bonus['crystal'] 	+= 0.25;
			$this->bonus['deuterium'] 	+= 0.25;
			$this->bonus['storage'] 	+= 0.25;
		}
		if ($this->data['rpg_ingenieur'] > time())
		{
			$this->bonus['energy'] 		+= 0.15;
			$this->bonus['solar'] 		+= 0.15;
			$this->bonus['res_defence'] -= 0.1;
		}
		if ($this->data['rpg_admiral'] > time())
		{
			$this->bonus['res_fleet'] 	-= 0.1;
			$this->bonus['fleet_speed'] += 0.25;
		}
		if ($this->data['rpg_constructeur'] > time())
		{
			$this->bonus['time_fleet'] 		-= 0.25;
			$this->bonus['time_defence'] 	-= 0.25;
			$this->bonus['time_building'] 	-= 0.25;
		}
		if ($this->data['rpg_technocrate'] > time())
		{
			$this->bonus['time_research'] -= 0.25;
		}
		if ($this->data['rpg_meta'] > time())
		{
			$this->bonus['fleet_fuel'] -= 0.1;
		}

		// Расчет бонусов от рас
		if ($this->data['race'] == 1)
		{
			$this->bonus['metal'] 		+= 0.15;
			$this->bonus['solar'] 		+= 0.15;
			$this->bonus['res_levelup'] -= 0.1;
			$this->bonus['time_fleet'] 	-= 0.1;
		}
		elseif ($this->data['race'] == 2)
		{
			$this->bonus['deuterium'] 	+= 0.15;
			$this->bonus['solar'] 		+= 0.05;
			$this->bonus['storage'] 	+= 0.2;
			$this->bonus['res_fleet'] 	-= 0.1;
		}
		elseif ($this->data['race'] == 3)
		{
			$this->bonus['metal'] 			+= 0.05;
			$this->bonus['crystal'] 		+= 0.05;
			$this->bonus['deuterium'] 		+= 0.05;
			$this->bonus['res_defence'] 	-= 0.05;
			$this->bonus['res_building'] 	-= 0.05;
			$this->bonus['time_building'] 	-= 0.1;
		}
		elseif ($this->data['race'] == 4)
		{
			$this->bonus['crystal'] 		+= 0.15;
			$this->bonus['energy'] 			+= 0.05;
			$this->bonus['res_research'] 	-= 0.1;
			$this->bonus['fleet_speed'] 	+= 0.1;
		}

		$this->options = $this->unpackOptions($this->data['options_toggle']);

		return true;
	}

	public function unpackOptions ($opt, $isToggle = true)
	{
		$result = array();

		if ($isToggle)
		{
			$o = array_reverse(str_split(decbin($opt)));

			$i = 0;

			foreach ($this->options AS $k => $v)
			{
				$result[$k] = (isset($o[$i]) ? $o[$i] : 0);

				$i++;
			}
		}

		return $result;
	}

	public function packOptions ($opt, $isToggle = true)
	{
		if ($isToggle)
		{
			$r = array();

			foreach ($this->options AS $k => $v)
			{
				if (isset($opt[$k]))
					$v = $opt[$k];

				$r[] = $v;
			}

			return bindec(implode('', array_reverse($r)));
		}
		else
			return 0;
	}

	public function isAuthorized()
	{
		return (count($this->data) > 0);
	}

	public function isAdmin()
	{
		if ($this->isAuthorized())
			return ($this->data['authlevel'] == 3);
		else
			return false;
	}

	public function getId ()
	{
		return (isset($this->data['id']) ? $this->data['id'] : false);
	}

	function getRankId ($lvl)
	{
		if ($lvl == 1)
			$lvl = 0;

		if ($lvl <= 80)
			return (ceil($lvl / 4) + 1);
		else
			return 22;
	}

	public function getAllyInfo ()
	{
		$this->data['ally'] = array();

		if ($this->data['ally_id'] > 0)
		{
			$ally = db::query("SELECT a.id, a.ally_owner, a.ally_name, a.ally_ranks, m.rank FROM game_alliance a, game_alliance_members m WHERE m.a_id = a.id AND m.u_id = ".$this->data['id']." AND a.id = ".$this->data['ally_id']."", true);

			if (isset($ally['id']))
			{
				if (!$ally['ally_ranks'])
					$ally['ally_ranks'] = 'a:0:{}';

				$ally_ranks = json_decode($ally['ally_ranks'], true);

				$this->data['ally'] = $ally;
				$this->data['ally']['rights'] = isset($ally_ranks[$ally['rank'] - 1]) ? $ally_ranks[$ally['rank'] - 1] : array('name' => '', 'planet' => 0);
			}
		}
	}

	public function getUserPlanets ($userId, $moons = true, $allyId = 0)
	{
		if (!$userId)
			return array();

		$qryPlanets = "SELECT `id`, `name`, `image`, `galaxy`, `system`, `planet`, `planet_type`, `destruyed` FROM game_planets WHERE `id_owner` = '" . $userId . "' ";

		$qryPlanets .= ($allyId > 0 ? " OR id_ally = '".$allyId."'" : "");

		if (!$moons)
			$qryPlanets .= " AND planet_type != 3 ";

		$qryPlanets .= $this->getPlanetListSortQuery();

		return db::extractResult(db::query($qryPlanets));
	}

	public function getPlanetListSortQuery ($sort = false, $order = false)
	{
		if ($this->isAuthorized())
		{
			if (!$sort)
				$sort 	= $this->data['planet_sort'];
			if (!$order)
				$order 	= $this->data['planet_sort_order'];
		}

		$qryPlanets = ' ORDER BY ';

		switch ($sort)
		{
			case 1:
				$qryPlanets .= "`galaxy`, `system`, `planet`, `planet_type` ";
				break;
			case 2:
				$qryPlanets .= "`name` ";
				break;
			case 3:
				$qryPlanets .= "`planet_type` ";
				break;
			default:
				$qryPlanets .= "`id` ";
		}

		$qryPlanets .= ($order == 1) ? "DESC" : "ASC";

		return $qryPlanets;
	}

	public function setSelectedPlanet ()
	{
		if (isset($_GET['cp']) && is_numeric($_GET['cp']) && isset($_GET['re']) && intval($_GET['re']) == 0)
		{
			$selectPlanet = intval($_GET['cp']);

			if ($this->data['current_planet'] == $selectPlanet)
				return true;

			$IsPlanetMine = db::query("SELECT `id`, `id_owner`, `id_ally` FROM game_planets WHERE `id` = '" . $selectPlanet . "' AND (`id_owner` = '" . $this->getId() . "' OR (`id_ally` > 0 AND `id_ally` = '".$this->data['ally_id']."'));", true);

			if (isset($IsPlanetMine['id']))
			{
				if ($IsPlanetMine['id_ally'] > 0 && $IsPlanetMine['id_owner'] != $this->getId() && !$this->data['ally']['rights']['planet'])
				{
					message("Вы не можете переключится на эту планету. Недостаточно прав.", "Альянс", "?set=overview", 2);
				}

				$this->data['current_planet'] = $selectPlanet;

				sql::build()->update('game_users')->setField('current_planet', $this->data['current_planet'])->where('id', '=', $this->getId())->execute();
			}
			else
				return false;
		}

		return true;
	}

	public function isNameValid($name)
	{
		if (UTF8_SUPPORT)
			return preg_match('/^[\p{L}\p{N}_\-. ]*$/u', $name);
		else
			return preg_match('/^[A-z0-9_\-. ]*$/', $name);
	}

	public function isMailValid($address)
	{
		if (function_exists('filter_var'))
			return filter_var($address, FILTER_VALIDATE_EMAIL) !== FALSE;
		else
			return preg_match('^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$', $address);
	}

	public function sendMessage ($owner, $sender, $time, $type, $from, $message)
	{
		if (!$time)
			$time = time();

		if (!$owner && $this->isAuthorized())
			$owner = $this->data['id'];

		if (!$owner)
			return false;

		if ($sender === false && $this->isAuthorized())
			$sender = $this->data['id'];

		if ($this->isAuthorized() && $owner == $this->data['id'])
			$this->data['new_message']++;

		sql::build()->insert('game_messages')->set(Array
		(
			'message_owner'		=> $owner,
			'message_sender'	=> $sender,
			'message_time'		=> $time,
			'message_type'		=> $type,
			'message_from'		=> addslashes($from),
			'message_text'		=> addslashes($message)
		))
		->execute();

		sql::build()->update('game_users')->set(Array('+new_message' => 1))->where('id', '=', $owner)->execute();

		return true;
	}

	public function getUserOption ($key = false)
	{
		if ($key === false)
			return $this->options;

		return (isset($this->options[$key]) ? $this->options[$key] : 0);
	}

	public function setUserOption ($key, $value)
	{
		$this->options[$key] = $value;
	}
}

?>