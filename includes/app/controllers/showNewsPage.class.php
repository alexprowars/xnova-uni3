<?php

class showNewsPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();

		strings::includeLang('news');
	}
	
	public function show ()
	{
		$news = array();

		foreach (_getText('news') as $a => $b)
		{
			$news[] = array($a, nl2br($b));
		}

		$this->setTemplate('news');
		$this->set('parse', $news);

		$this->display('', 'Новости', false, (isset(user::get()->data['id'])));
	}
}

?>