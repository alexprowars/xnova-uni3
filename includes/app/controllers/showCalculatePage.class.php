<?php

class showCalculatePage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();

		app::loadPlanet();
	}

	public function cost()
	{
		$this->setTemplate('calculate/cost');
		$this->set('planet', app::$planetrow->data);
		$this->set('user', user::get()->data);
		$this->display('', 'Калькуляторы', false);
	}

	public function moon()
	{
		$this->setTemplate('calculate/moon');
		$this->display('', 'Калькуляторы', false);
	}

	public function fleet()
	{
		$this->setTemplate('calculate/fleet');
		$this->set('planet', app::$planetrow->data);
		$this->set('user', user::get()->data);
		$this->display('', 'Калькуляторы', false);
	}
	
	public function show ()
	{
		$this->setTemplate('calculate/index');
		$this->display('', 'Калькуляторы', false);
	}
}

?>