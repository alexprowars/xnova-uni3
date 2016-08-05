<?php

class showInfokreditsPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}
	
	public function show ()
	{
		$userinf = db::query("SELECT email FROM game_users_inf WHERE id = " . user::get()->getId() . ";", true);

		if (!defined('APPID'))
			$this->setTemplate('credits');
		else
			$this->setTemplate('credits_ok');

		$this->set('userid', user::get()->getId());
		$this->set('useremail', $userinf['email']);

		$this->display('', 'Покупка кредитов', false);
	}
}

?>