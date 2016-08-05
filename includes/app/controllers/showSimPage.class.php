<?php

class showSimPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();

		app::loadPlanet();
	}
	
	public function show ()
	{
		global $reslist, $resource;
		
		$r = (isset($_GET['r'])) ? $_GET['r'] : '';
		$r = explode(";", $r);
		
		$parse = array();
		$parse['att'] = array();
		$parse['def'] = array();
		
		foreach ($r AS $row)
		{
			if ($row != '')
			{
				@$Element = explode(",", $row);
				@$Count = explode("!", $Element[1]);
				if (isset($Count[1]))
					@$parse['def'][$Element[0]] = array('c' => $Count[0], 'l' => $Count[1]);
			}
		}
		
		$res = array_merge($reslist['fleet'], $reslist['defense'], $reslist['tech']);
		
		foreach ($res AS $id)
		{
			if (isset(app::$planetrow->data[$resource[$id]]) && app::$planetrow->data[$resource[$id]] > 0)
				$parse['att'][$id] = array('c' => app::$planetrow->data[$resource[$id]], 'l' => ((isset(user::get()->data['fleet_' . $id])) ? user::get()->data['fleet_' . $id] : 0));
		
			if (isset(user::get()->data[$resource[$id]]) && user::get()->data[$resource[$id]] > 0)
				$parse['att'][$id] = array('c' => user::get()->data[$resource[$id]]);
		}
		
		$this->setTemplate('sim');
		$this->set('parse', $parse);
		
		$this->display('', 'Симулятор', false);
	}
}

?>