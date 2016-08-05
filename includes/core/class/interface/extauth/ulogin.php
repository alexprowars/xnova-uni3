<?php

class uloginAuth
{
	private $token = '';
	private $data = array();
	private $isLogin = false;

	function __construct($token)
	{
		$s = file_get_contents('http://u-login.com/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST']);
		$this->data = json_decode($s, true);

		$this->token = $token;

		if (isset($this->data['identity']))
		{
			$this->isLogin = true;
			$this->login();
		}
	}

	public function isAuthorized ()
	{
		return $this->isLogin;
	}

	public function login ()
	{
		if (!$this->isAuthorized())
			return false;

		$check = db::query("SELECT u.options_toggle, ui.id, ui.password FROM game_users u, game_users_inf ui WHERE ui.id = u.id AND ui.identity = '".$this->data['identity']."'", true);

		$expiretime = time() + 24192000;

		if (isset($check['id']))
		{
			global $session;

			$options = user::get()->unpackOptions($check['options_toggle']);

			$session->auth($check['id'], $check['password'],  $options['security'], $expiretime);

			return true;
		}
		else
			return $this->register();
	}

	public function register ()
	{
		$refer = (isset($_SESSION['ref']) ? intval($_SESSION['ref']) : 0);

		db::query("INSERT INTO game_users SET `username` = '".trim($this->data['first_name']." ".$this->data['last_name'])."', `sex` = '1', `id_planet` = '0', `user_lastip` = '". request::getClientIp(true) ."', `onlinetime` = '". time() ."';");

		$id = db::insert_id();

		if ($id > 0)
		{
			db::query("INSERT INTO game_users_inf SET `id` = '".$id."', `email` = '".$this->data['identity']."', identity = '".$this->data['identity']."', network = '".$this->data['network']."', `register_time` = '".time()."', `password` = '".md5($this->token)."';");

			if ($refer != 0)
			{
				$ref = db::query("SELECT id FROM game_users_inf WHERE id = '".$refer."'", true);

				if ($ref['id'] > 0)
				{
					db::query("INSERT INTO game_refs VALUES (" . $id . ", " . $ref['id'] . ")");
				}
			}

			system::CreateRegPlanet($id);
			core::updateConfig('users_amount', (core::getConfigFromDB('users_amount', 0) + 1));
			core::clearConfig();

			global $session;

			$session->auth($id, md5($this->token));

			return true;
		}
		else
			return false;
	}
}
 
?>