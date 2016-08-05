<?php

class showBannedPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}

	public function show ()
	{
		$query = db::query('SELECT u.username AS user_1, u2.username AS user_2, b.* FROM game_banned b LEFT JOIN game_users u ON u.id = b.who LEFT JOIN game_users u2 ON u2.id = b.author ORDER BY b.`id` DESC');

		$bannedList = array();

		while ($u = db::fetch_assoc($query))
		{
			$bannedList[] = $u;
		}

		$this->setTemplate('banned');
		$this->set('bannedList', $bannedList);

		$this->display('', 'Список заблокированных игроков', false, (user::get()->getId() && user::get()->data['banaday'] == 0));
	}
}

?>