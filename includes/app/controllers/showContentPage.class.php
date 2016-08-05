<?php

class showContentPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}

	function show()
	{
		if (!request::G('article'))
			$this->message('Страница не найдена!');

		$content = db::query("SELECT * FROM game_content WHERE alias = '".request::G('article')."' LIMIT 1", true);

		if (!isset($content['id']))
			$this->message('Страница не найдена!');

		$this->setTemplate('content');
		$this->set('html', stripslashes($content['html']));

		$this->display('', $content['title'], false);
	}
}