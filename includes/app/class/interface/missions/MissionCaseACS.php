<?php

class MissionCaseACS extends fleet_engine implements Mission
{
	function __construct($Fleet)
	{
			$this->_fleet = $Fleet;
	}

	public function TargetEvent()
	{
		$this->ReturnFleet();
	}

	public function EndStayEvent()
	{
		return;
	}

	public function ReturnEvent()
	{
		$this->RestoreFleetToPlanet();
		$this->KillFleet();
	}
}

?>