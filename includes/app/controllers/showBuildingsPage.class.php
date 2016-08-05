<?php

class showBuildingsPage extends pageHelper
{
	/**
	 * @var building $building
	 */
	private $building;

	function __construct ()
	{
		parent::__construct();

		strings::includeLang('buildings');

		app::loadPlanet();

		if (user::get()->data['urlaubs_modus_time'] > 0)
		{
			$this->message("Нет доступа!");
		}

		$this->building = new building();
		$this->building->planet = app::$planetrow;
		$this->building->user = user::get();
	}

	public function fleet()
	{
		global $resource;

		if (app::$planetrow->data[$resource[21]] == 0)
			$this->message(_getText('need_hangar'), _getText('tech', 21));

		$parse = $this->building->pageShipyard('fleet');
		$parse['mode'] = $this->mode;

		$this->setTemplate('buildings/buildings_shipyard');
		$this->set('parse', $parse);

		if (app::$planetrow->data['b_hangar_id'] != '')
		{
			$this->setTemplate('buildings/buildings_script');
			$data = $this->building->ElementBuildListBox();
			$this->set('parse', $data);
		}

		$this->display('', 'Верфь');
	}

	public function research()
	{
		global $resource;

		if (app::$planetrow->data[$resource[31]] == 0)
			$this->message(_getText('no_laboratory'), _getText('Research'));

		$parse = $this->building->pageResearch(($this->mode == 'research_fleet' ? 'fleet' : ''));

		$this->setTemplate('buildings/buildings_research');
		$this->set('parse', $parse);

		$this->display('', 'Исследования');
	}

	public function research_fleet()
	{
		$this->research();
	}

	public function defense()
	{
		global $resource;

		if (app::$planetrow->data[$resource[21]] == 0 && app::$planetrow->data['planet_type'] != 5)
			$this->message(_getText('need_hangar'), _getText('tech', 21));

		if (app::$planetrow->data['planet_type'] == 5)
			user::get()->setUserOption('only_available', 1);

		$parse = $this->building->pageShipyard('defense');
		$parse['mode'] = $this->mode;

		$this->setTemplate('buildings/buildings_shipyard');
		$this->set('parse', $parse);

		if (app::$planetrow->data['b_hangar_id'] != '')
		{
			$this->setTemplate('buildings/buildings_script');
			$data = $this->building->ElementBuildListBox();
			$this->set('parse', $data);
		}

		$this->display('', 'Оборона');
	}
	
	public function show ()
	{
		$parse = $this->building->pageBuilding();

		$this->setTemplate('buildings/buildings_build');
		$this->set('parse', $parse);

		$this->display('', 'Постройки');
	}
}

?>