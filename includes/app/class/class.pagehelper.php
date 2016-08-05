<?php

/**
 * @method setAtribute
 * @method getAtribute
 * @method setTemplate
 * @method setTemplateName
 * @method set
 * @method globals
 */
class pageHelper
{
	/**
	 * @var page
	 */
	public $page;
	public $name = '';
	public $mode = '';

	public function __construct()
	{
		$this->page = new page();
	}

	public function __call($name, $arguments)
	{
		if (is_callable(array($this->page, $name)))
		{
			call_user_func_array(array($this->page, $name), $arguments);
		}
	}

	public function display($html = '', $title = '', $topPanel = true, $leftMenu = true)
	{
		global $user;

		$this->page->setAtribute('title', strip_tags($title));

		$this->page->globals('pagePropMode', $this->mode);

		if (!isset($user->data['id']) || isset($_GET['ajax']))
			$leftMenu = false;

		$params = array();
		$params['topPanel'] = $topPanel;
		$params['leftMenu'] = $leftMenu;

		$params['timezone'] = (isset($user->data['timezone']) ? $user->data['timezone'] : 0);

		switch (core::getConfig('ajaxNavigation', 0))
		{
			case 0:
				$params['ajaxNavigation'] = 0;
				break;
			case 1:
				$params['ajaxNavigation'] = user::get()->getUserOption('ajax_navigation');
				break;
			default:
				$params['ajaxNavigation'] = 1;
		}

		if (isset($user->data['id']))
		{
			$params['deleteUserTimer'] 	= $user->data['deltime'];
			$params['vocationTimer'] 	= $user->data['urlaubs_modus_time'];
			$params['authLevel']		= $user->data['authlevel'];
			$params['newMessages']		= $user->data['new_message'];
			$params['design']			= isset($user->data['design']) ? $user->data['design'] : 1;
			$params['allyMessages']		= ($user->data['ally_id'] != 0) ? $user->data['mnl_alliance'] : 0;
		}

		$this->page->page = $html;
		$this->page->display($params);
	}

	protected function message ($text, $title = 'Ошибка', $dest = "", $time = 3, $left = true)
	{
		$this->page->setTemplate('message');
		$this->page->set('text', $text);
		$this->page->set('title', $title);
		$this->page->set('destination', $dest);
		$this->page->set('time', $time);

		$this->display('', ($title ? strip_tags($title) : 'Сообщение'), false, $left);
	}
}