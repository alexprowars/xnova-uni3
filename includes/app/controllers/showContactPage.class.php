<?php

class showContactPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();

		strings::includeLang('contact');
	}

	function show()
	{
		$contacts = array();

		$GameOps = db::query("SELECT u.`username`, ui.`email`, u.`authlevel` FROM game_users u, game_users_inf ui WHERE ui.id = u.id AND u.`authlevel` != '0' ORDER BY u.`authlevel` DESC");

		while ($Ops = db::fetch_assoc($GameOps))
		{
			$contacts[] = array
			(
				'ctc_data_name' => $Ops['username'],
				'ctc_data_auth' => _getText('user_level', $Ops['authlevel']),
				'ctc_data_mail' => $Ops['email']
			);
		}

		$this->setTemplate('contact');
		$this->set('contacts', $contacts);

		$this->display('', _getText('ctc_title'), false, (isset(user::get()->data['id'])));
	}
}