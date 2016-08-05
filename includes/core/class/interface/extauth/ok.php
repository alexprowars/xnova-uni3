<?php

class okAuth
{
	private $isLogin = false;
	private $data = array();

	function __construct()
	{
		if ($_POST['application_key'] != '' && $_POST['api_server'] != '')
		{
			socials::okConnect($_POST['application_key'], $_POST['api_server']);

			$uInfo = socials::okLoad('users/getInfo', array('uids' => intval($_POST['logged_user_id']), 'fields' => 'first_name,last_name,name,gender,birthday,age,locale,location,current_location,online,pic128x128'));
			$this->data = $uInfo[0];

			$this->isLogin = true;

			$this->login();
		}
	}

	public function isAuthorized ()
	{
		return $this->isLogin;
	}

	public function login()
	{
		if (!$this->isAuthorized())
			return false;

		global $session;

		if (md5($_POST['logged_user_id'].$_POST['session_key'].APPSECRET) != $_POST['auth_sig'])
		{
			die('<script type="text/javascript">alert("Параметры авторизации являются некорректными!")</script>');
		}
		else
		{
			$Row = db::query("SELECT u.id, u.tutorial, ui.password FROM game_users u, game_users_inf ui WHERE ui.id = u.id AND ui.`ok_uid` = '".intval($_POST['logged_user_id'])."';", true);

			if (!isset($Row['id']))
			{
				$this->register();
			}
			else
			{
				if (isset($this->data['uid']))
					db::query("UPDATE game_users_inf SET ok_photo = '".$this->data['pic128x128']."' WHERE id = ".$Row['id']."");

				$session->auth($Row['id'], $Row['password'], 0, (time() + 2419200));

				setcookie(COOKIE_NAME."_uni", "uni".UNIVERSE, (time() + 2419200), "/", ".xnova.su", 0);
			}

			setcookie(COOKIE_NAME."_full", "", 0, "/", $_SERVER["SERVER_NAME"], 0);

			session_start();
			unset($_SESSION['OKAPI']);
			$_SESSION['OKAPI'] = $_POST;

			$set = 'overview';

			if ($Row['tutorial'] == 0)
				$set = 'tutorial';

			echo '<center>Загрузка...</center><script>parent.location.href="?set='.$set.'&'.http_build_query($_POST).'";</script>';
			die();
		}
	}

	public function register ()
	{
		$uid = intval($_POST['logged_user_id']);

		if (!$uid)
			return false;

		if (isset($_POST['custom_args']))
		{
			parse_str($_POST['custom_args'], $cArgs);

			$refer = (isset($cArgs['userId']) ? intval($cArgs['userId']) : 0);
		}
		else
			$refer = 0;

		global $session;

		$NewPass = strings::randomSequence();

		if ($refer != 0)
		{
			$refe = db::query("SELECT id FROM game_users_inf WHERE id = '".$refer."'", true);

			if (!isset($refe['id']))
				$refer = 0;
		}
		
		db::query("LOCK TABLES game_users_inf WRITE, game_users WRITE");
		
		$check = db::query("SELECT id FROM game_users_inf WHERE ok_uid = '".$uid."'", true);
		
		if (isset($check['id']))
			return false;

		db::query("INSERT INTO game_users SET `username` = '".addslashes(str_replace('\'', '', $this->data['name']))."', `sex` = '".($this->data['gender'] == 'male' ? 1 : 2)."', `id_planet` = '0', `user_lastip` = '". request::getClientIp(true) ."', `onlinetime` = '". time() ."'");

		$iduser = db::insert_id();

		if ($iduser > 0)
		{
			db::query("INSERT INTO game_users_inf SET `id` = '".$iduser."', `email` = '', ok_uid = '".$uid."', `register_time` = '".time()."', `password` = '".md5($NewPass)."', ok_photo = '".$this->data['pic128x128']."'");

			db::query("UNLOCK TABLES");
			
			if ($refer != 0)
			{
				db::query("INSERT INTO game_refs VALUES (" . $iduser . ", " . $refer . ")");
				db::query("INSERT INTO game_buddy (sender, owner, active) VALUES (".$iduser.", ".$refer.", 1)");
			}

			system::CreateRegPlanet($iduser);
			core::updateConfig('users_amount', (core::getConfigFromDB('users_amount', 0) + 1));
			core::clearConfig();

			$session->auth($iduser, md5($NewPass));

			return true;
		}
		else
		{
			db::query("UNLOCK TABLES");
			
			return false;
		}
	}
}

?>