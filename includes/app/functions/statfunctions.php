<?php

function GetTechnoPoints ($CurrentUser)
{
	global $resource, $pricelist, $reslist;

	$TechCounts = 0;
	$TechPoints = 0;

	$res_array = array_merge($reslist['tech'], $reslist['tech_f']);

	foreach ($res_array as $Techno)
	{
		if ($CurrentUser[$resource[$Techno]] == 0)
			continue;

		if ($CurrentUser['records'] == 1 && $Techno < 300)
			SetMaxInfo($Techno, $CurrentUser[$resource[$Techno]], $CurrentUser);

		$Units = $pricelist[$Techno]['metal'] + $pricelist[$Techno]['crystal'] + $pricelist[$Techno]['deuterium'];

		for ($Level = 1; $Level <= $CurrentUser[$resource[$Techno]]; $Level++)
		{
			$TechPoints += $Units * pow($pricelist[$Techno]['factor'], $Level);
		}
		$TechCounts += $CurrentUser[$resource[$Techno]];
	}
	$RetValue['TechCount'] = $TechCounts;
	$RetValue['TechPoint'] = $TechPoints;

	return $RetValue;
}

function GetBuildPoints ($CurrentPlanet, $User)
{
	global $resource, $pricelist, $reslist;

	$BuildCounts = 0;
	$BuildPoints = 0;
	foreach ($reslist['build'] as $Build)
	{

		if ($CurrentPlanet[$resource[$Build]] == 0)
			continue;

		if ($User['records'] == 1)
			SetMaxInfo($Build, $CurrentPlanet[$resource[$Build]], $User);

		$Units = $pricelist[$Build]['metal'] + $pricelist[$Build]['crystal'] + $pricelist[$Build]['deuterium'];
		for ($Level = 1; $Level <= $CurrentPlanet[$resource[$Build]]; $Level++)
		{
			$BuildPoints += $Units * pow($pricelist[$Build]['factor'], $Level);
		}
		$BuildCounts += $CurrentPlanet[$resource[$Build]];
	}
	$RetValue['BuildCount'] = $BuildCounts;
	$RetValue['BuildPoint'] = $BuildPoints;

	return $RetValue;
}

function GetDefensePoints ($CurrentPlanet, &$RecordArray)
{
	global $resource, $pricelist, $reslist;

	$DefenseCounts = 0;
	$DefensePoints = 0;
	foreach ($reslist['defense'] as $Defense)
	{
		if ($CurrentPlanet[$resource[$Defense]] > 0)
		{

			if (isset($RecordArray[$Defense]))
				$RecordArray[$Defense] += $CurrentPlanet[$resource[$Defense]];
			else
				$RecordArray[$Defense] = $CurrentPlanet[$resource[$Defense]];

			$Units = $pricelist[$Defense]['metal'] + $pricelist[$Defense]['crystal'] + $pricelist[$Defense]['deuterium'];
			$DefensePoints += ($Units * $CurrentPlanet[$resource[$Defense]]);
			$DefenseCounts += $CurrentPlanet[$resource[$Defense]];
		}
	}
	$RetValue['DefenseCount'] = $DefenseCounts;
	$RetValue['DefensePoint'] = $DefensePoints;

	return $RetValue;
}

function GetFleetPoints ($CurrentPlanet, &$RecordArray)
{
	global $resource, $pricelist, $reslist;

	$FleetCounts = 0;
	$FleetPoints = 0;
	foreach ($reslist['fleet'] as $Fleet)
	{
		if ($CurrentPlanet[$resource[$Fleet]] > 0)
		{

			if (isset($RecordArray[$Fleet]))
				$RecordArray[$Fleet] += $CurrentPlanet[$resource[$Fleet]];
			else
				$RecordArray[$Fleet] = $CurrentPlanet[$resource[$Fleet]];

			$Units = $pricelist[$Fleet]['metal'] + $pricelist[$Fleet]['crystal'] + $pricelist[$Fleet]['deuterium'];
			$FleetPoints += ($Units * $CurrentPlanet[$resource[$Fleet]]);
			if ($Fleet != 212)
				$FleetCounts += $CurrentPlanet[$resource[$Fleet]];
		}
	}
	$RetValue['FleetCount'] = $FleetCounts;
	$RetValue['FleetPoint'] = $FleetPoints;

	return $RetValue;
}

function GetFleetPointsOnTour ($CurrentFleet)
{
	global $pricelist;

	$FleetCounts = 0;
	$FleetPoints = 0;
	$FleetArray = array();

	$split = trim(str_replace(';', ' ', $CurrentFleet));
	$split = explode(' ', $split);

	foreach ($split as $ship)
	{
		list($typ, $temp) = explode(',', $ship);
		list($amount, $lvl) = explode('!', $temp);
		$Units = $pricelist[$typ]['metal'] + $pricelist[$typ]['crystal'] + $pricelist[$typ]['deuterium'];
		$FleetPoints += ($Units * $amount);
		if ($typ != 212)
			$FleetCounts += $amount;

		if (isset($FleetArray[$typ]))
			$FleetArray[$typ] += $amount;
		else
			$FleetArray[$typ] = $amount;
	}

	$RetValue['FleetCount'] = $FleetCounts;
	$RetValue['FleetPoint'] = $FleetPoints;
	$RetValue['fleet_array'] = $FleetArray;

	return $RetValue;
}

?>