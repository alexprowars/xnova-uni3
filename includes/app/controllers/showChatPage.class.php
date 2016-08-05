<?php

class showChatPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}
	
	public function show ()
	{
		$regTime = db::first(db::query("SELECT register_time FROM game_users_inf WHERE id = ".user::get()->getId()."", true));

		if ($regTime > (time() - 43200))
			$this->message('Доступ к чату будет открыт спустя 12 часов после регистрации.');

		$this->setTemplate('chat');
		$this->display('', "Межгалактический чат", false, (!isset($_GET['frame'])));
	}
}

?>