<?php

class showRegPage extends pageHelper
{
	function __construct ()
	{
		if (!defined('ALLOW_REGISTRATION') || !ALLOW_REGISTRATION)
			die('Регистрация закрыта');

		parent::__construct();
	}
	
	public function show ()
	{
		global $session;

		strings::includeLang('reg');

		if ($_POST)
		{
			$errors = 0;
			$errorlist = "";

			$_POST['email'] = strip_tags(trim($_POST['email']));

			if (!is_email($_POST['email']))
			{
				$errorlist .= "\"" . $_POST['email'] . "\" " . _getText('error_mail');
				$errors++;
			}

			$girilen = trim($_REQUEST["captcha"]);

			if (!isset($_SESSION['captcha']) || ($_SESSION['captcha'] != $girilen && $_SESSION['captcha'] != ""))
			{
				$errorlist .= _getText('error_captcha');
				$errors++;
			}

			if (!$_POST['character'])
			{
				$errorlist .= _getText('error_character');
				$errors++;
			}

			if (mb_strlen($_POST['passwrd'], 'UTF-8') < 4)
			{
				$errorlist .= _getText('error_password');
				$errors++;
			}

			if (!preg_match("/^[А-Яа-яЁёa-zA-Z0-9_\-\!\~\.@ ]+$/u", $_POST['character']))
			{
				$errorlist .= _getText('error_charalpha');
				$errors++;
			}

			if (!isset($_POST['rgt']) || !isset($_POST['sogl']) || $_POST['rgt'] != 'on' || $_POST['sogl'] != 'on')
			{
				$errorlist .= _getText('error_rgt');
				$errors++;
			}

			$ExistUser = db::query("SELECT `id` FROM game_users WHERE `username` = '" . db::escape_string(trim($_POST['character'])) . "' LIMIT 1;", true);

			if (isset($ExistUser['id']))
			{
				$errorlist .= _getText('error_userexist');
				$errors++;
			}

			$ExistMail = db::query("SELECT `id` FROM game_users_inf WHERE `email` = '" . db::escape_string(trim($_POST['email'])) . "' LIMIT 1;", true);

			if (isset($ExistMail['id']))
			{
				$errorlist .= _getText('error_emailexist');
				$errors++;
			}

			if ($_POST['sex'] != 'F' && $_POST['sex'] != 'M')
			{
				$errorlist .= _getText('error_sex');
				$errors++;
			}

			if ($errors != 0)
			{
				$this->setTemplate('reg');
				$this->set('errors', $errorlist);

				$this->display('', _getText('registry'), false, false);
			}
			else
			{
				$newpass 	= trim($_POST['passwrd']);
				$UserName 	= trim($_POST['character']);
				$UserEmail 	= trim($_POST['email']);

				$md5newpass = md5($newpass);

				$sex = ($_POST['sex'] == 'F') ? 2 : 1;

				sql::build()->insert('game_users')->set(Array
				(
					'username' 		=> db::escape_string(strip_tags($UserName)),
					'sex' 			=> $sex,
					'id_planet' 	=> 0,
					'user_lastip' 	=> request::getClientIp(true),
					'bonus' 		=> time(),
					'onlinetime' 	=> time()
				))
				->execute();

				$iduser = db::insert_id();

				sql::build()->insert('game_users_inf')->set(Array
				(
					'id' 			=> $iduser,
					'email' 		=> db::escape_string($UserEmail),
					'register_time' => time(),
					'password' 		=> $md5newpass
				))
				->execute();

				if (isset($_SESSION['ref']))
				{
					$refe = db::query("SELECT id FROM game_users WHERE id = " . $_SESSION['ref'] . "", true);
					if ($refe['id'] > 0)
					{
						db::query("INSERT INTO game_refs VALUES (" . $iduser . ", " . $_SESSION['ref'] . ")");
					}
				}

				system::CreateRegPlanet($iduser);
				core::updateConfig('users_amount', (core::getConfigFromDB('users_amount', 0) + 1));
				core::clearConfig();

				core::loadLib('mail');

				$mail = new PHPMailer();
				$mail->SetFrom(ADMINEMAIL, SITE_TITLE);
				$mail->AddAddress($UserEmail, $UserName);
				$mail->IsHTML(true);
				$mail->CharSet = 'utf-8';
				$mail->Subject = "Регистрация в игре XNova";
				$mail->Body = "Вы успешно зарегистрировались в игре XNova.<br>Ваши данные для входа в игру:<br>Email: " . $UserEmail . "<br>Пароль:" . $newpass . "";
				$mail->Send();

				$passw_string = $session->getCookiePassword($iduser, $md5newpass);
				$expiretime = 0;

				setcookie(COOKIE_NAME."_id", 		$iduser, 		$expiretime, "/", $_SERVER["SERVER_NAME"], 0);
				setcookie(COOKIE_NAME."_secret", 	$passw_string, 	$expiretime, "/", $_SERVER["SERVER_NAME"], 0);
				setcookie(COOKIE_NAME."_uni", 		"uni".UNIVERSE, $expiretime, "/", ".xnova.su", 0);

				request::redirectTo("?set=overview");
			}
		}
		else
		{
			$this->setTemplate('reg');

			$this->display('', _getText('registry'), false, false);
		}
	}
}

?>