function calc_capacity()
{
	var msp = 1000000000;
	var cap = 0;
	var sp = msp;
	var tmp;
	var id;

	for (var i = 200; i < 230; i++)
	{
		id = "ship" + i;
		if (document.getElementsByName(id)[0])
		{
			cnt = parseInt($("*[name=" + id + "]").val());
			cap += cnt * parseInt($("*[name=capacity" + i + "]").val());
			if (cnt > 0)
			{
				tmp = parseInt($("*[name=speed" + i + "]").val());
				if (tmp < sp)
					sp = tmp;
			}
		}
	}
	if (cap <= 0)
		cap = "-";
	else
		cap = validate_number(cap);

	if ((sp <= 0) || (sp >= msp))
		sp = "-";
	else
		sp = validate_number(sp);

	$("#allcapacity").html(cap);
	$("#allspeed").html(sp);
}

function speed()
{
	return($("*[name=speed]").val());
}

function target()
{
	var galaxy = $("*[name=galaxy]").val();
	var system = $("*[name=system]").val();
	var planet = $("*[name=planet]").val();

	return("[" + galaxy + ":" + system + ":" + planet + "]");
}

function setTarget(galaxy, solarsystem, planet, planettype)
{
	$("*[name=galaxy]").val(galaxy);
	$("*[name=system]").val(solarsystem);
	$("*[name=planet]").val(planet);
	$('*[name=planettype]').val(planettype);
}

function setMission(mission)
{
	$('*[name=order]')[0].selectedIndex = mission;
}

function setUnion(unionid)
{
	$('*[name=union2]')[0].selectedIndex = unionid;
}

function setTargetLong(galaxy, solarsystem, planet, planettype, mission, cnt)
{
	setTarget(galaxy, solarsystem, planet, planettype);
	setMission(mission);
	setUnions(cnt);
}

function maxspeed()
{
	var msp = 1000000000;
	for (var i = 200; i < 230; i++)
	{
		if (document.getElementsByName("ship" + i)[0])
		{
			if ((parseInt($("*[name=speed" + i + "]").val())) >= 1 && (parseInt($("*[name=ship" + i + "]").val())) >= 1)
			{
				msp = Math.min(msp, parseInt($("*[name=speed" + i + "]").val()));
			}
		}
	}

	return(msp);
}

function distance()
{
	var thisGalaxy;
	var thisSystem;
	var thisPlanet;

	var targetGalaxy;
	var targetSystem;
	var targetPlanet;

	var dist;

	thisGalaxy = document.getElementsByName("thisgalaxy")[0].value;
	thisSystem = document.getElementsByName("thissystem")[0].value;
	thisPlanet = document.getElementsByName("thisplanet")[0].value;

	targetGalaxy = document.getElementsByName("galaxy")[0].value;
	targetSystem = document.getElementsByName("system")[0].value;
	targetPlanet = document.getElementsByName("planet")[0].value;

	dist = 0;
	if ((targetGalaxy - thisGalaxy) != 0)
	{
		dist = Math.abs(targetGalaxy - thisGalaxy) * 20000;
	}
	else if ((targetSystem - thisSystem) != 0)
	{
		dist = Math.abs(targetSystem - thisSystem) * 5 * 19 + 2700;
	}
	else if ((targetPlanet - thisPlanet) != 0)
	{
		dist = Math.abs(targetPlanet - thisPlanet) * 5 + 1000;
	}
	else
	{
		dist = 5;
	}

	return(dist);
}

function duration()
{

	var speedfactor = document.getElementsByName("speedfactor")[0].value;
	var msp = maxspeed();
	var sp = speed();
	var dist = distance();

	return Math.round(((35000 / sp * Math.sqrt(dist * 10 / msp) + 10) / speedfactor ));
}

function consumption2()
{
	var basicConsumption = 0;

	for (var i = 200; i < 230; i++)
	{
		if (document.getElementsByName("ship" + i)[0])
		{
			basicConsumption = basicConsumption + document.getElementsByName("consumption" + i)[0].value * document.getElementsByName("ship" + i)[0].value;
		}
	}

	var speedfactor = document.getElementsByName("speedfactor")[0].value;
	var sp = speed();
	var dist = distance();

	return Math.round(basicConsumption * dist / 35000 * ((sp / 10) + 1) * ((sp / 10) + 1)) + 1;
}

function consumption()
{
	var consumption = 0;
	var basicConsumption = 0;

	var sp = speed();
	var dist = distance();
	var dur = duration();
	var speedfactor = document.getElementsByName("speedfactor")[0].value;

	var shipspeed, spd;

	for (var i = 200; i < 230; i++)
	{
		if (document.getElementsByName("ship" + i)[0])
		{
			shipspeed = document.getElementsByName("speed" + i)[0].value;
			spd = 35000 / (dur * speedfactor - 10) * Math.sqrt(dist * 10 / shipspeed);

			basicConsumption = document.getElementsByName("consumption" + i)[0].value * document.getElementsByName("ship" + i)[0].value;
			consumption += basicConsumption * dist / 35000 * ((spd / 10) + 1) * ((spd / 10) + 1);
		}
	}

	return Math.round(consumption) + 1;
}

function probeConsumption()
{
	var consumption = 0;
	var basicConsumption = 0;

	var sp = speed();
	var dist = distance();
	var dur = duration();
	var speedfactor = document.getElementsByName("speedfactor")[0].value;

	if (document.getElementsByName("ship210")[0])
	{
		var shipspeed = document.getElementsByName("speed210")[0].value;
		var spd = 35000 / (dur * speedfactor - 10) * Math.sqrt(dist * 10 / shipspeed);

		basicConsumption = document.getElementsByName("consumption210")[0].value * document.getElementsByName("ship210")[0].value;
		consumption += basicConsumption * dist / 35000 * ((spd / 10) + 1) * ((spd / 10) + 1);
	}

	return Math.round(consumption) + 1;
}

function unusedProbeStorage()
{

	var storage = document.getElementsByName('capacity210')[0].value * document.getElementsByName('ship210')[0].value;
	var stor = storage - probeConsumption();
	return (stor > 0) ? stor : 0;

}

function storage()
{
	var storage = 0;

	for (i = 200; i < 300; i++)
	{

		if (document.getElementsByName("ship" + i)[0])
		{
			if ((document.getElementsByName("ship" + i)[0].value * 1) >= 1)
			{
				storage += document.getElementsByName("ship" + i)[0].value * document.getElementsByName("capacity" + i)[0].value
			}
		}
	}

	storage -= consumption();
	if (document.getElementsByName("ship210")[0])
	{
		storage -= unusedProbeStorage();
	}

	return(storage);
}

function fleetInfo()
{
	$("#speed").html(speed() * 10 + "%");
	$("#target").html(target());
	$("#distance").html(distance());

	var seconds = duration();
	var hours = Math.floor(seconds / 3600);
	seconds -= hours * 3600;

	var minutes = Math.floor(seconds / 60);
	seconds -= minutes * 60;

	if (minutes < 10) minutes = "0" + minutes;
	if (seconds < 10) seconds = "0" + seconds;

	$("#duration").html(hours + ":" + minutes + ":" + seconds + " h");

	var stor = storage();
	var cons = consumption();
	$("#maxspeed").html(tsdpkt(maxspeed()));
	if (stor >= 0)
	{
		$("#consumption").html('<font color="lime">' + cons + '</font>');
		$("#storage").html('<font color="lime">' + stor + '</font>');
	}
	else
	{
		$("#consumption").html('<font color="red">' + cons + '</font>');
		$("#storage").html('<font color="red">' + stor + '</font>');
	}
	calculateTransportCapacity();
}

function shortInfo()
{
	document.getElementById("distance").innerHTML = tsdpkt(distance());
	var seconds = duration();
	var hours = Math.floor(seconds / 3600);
	seconds -= hours * 3600;

	var minutes = Math.floor(seconds / 60);
	seconds -= minutes * 60;

	if (minutes < 10) minutes = "0" + minutes;
	if (seconds < 10) seconds = "0" + seconds;

	document.getElementById("duration").innerHTML = hours + ":" + minutes + ":" + seconds + " h";
	var stor = storage();
	var cons = consumption();

	document.getElementById("maxspeed").innerHTML = tsdpkt(maxspeed());
	if (stor >= 0)
	{
		document.getElementById("consumption").innerHTML = '<font color="lime">' + tsdpkt(cons) + '</font>';
		document.getElementById("storage").innerHTML = '<font color="lime">' + tsdpkt(stor) + '</font>';
	}
	else
	{
		document.getElementById("consumption").innerHTML = '<font color="red">' + tsdpkt(cons) + '</font>';
		document.getElementById("storage").innerHTML = '<font color="red">' + tsdpkt(stor) + '</font>';
	}

	durationTime = duration() * 1000;

	durationTimer();
}

var durationTime = 0;

function durationTimer()
{
	var D0 = new Date;
	hms('end_time', new Date(D0.getTime() + serverTime + durationTime));

	timeouts['durationTimer'] = setTimeout(durationTimer, 999);
}

function setResource(id, val)
{
	if (document.getElementsByName(id)[0])
	{
		document.getElementsByName("resource" + id)[0].value = val;
	}
}

function maxResource(id)
{
	var thisresource = parseInt(document.getElementsByName("thisresource" + id)[0].value);
	var thisresourcechosen = parseInt(document.getElementsByName("resource" + id)[0].value);

	if (isNaN(thisresourcechosen))
	{
		thisresourcechosen = 0;
	}
	if (isNaN(thisresource))
	{
		thisresource = 0;
	}

	var storCap = storage();
	if (id == 3)
	{
		thisresource -= consumption();
	}

	var metalToTransport = parseInt(document.getElementsByName("resource1")[0].value);
	var crystalToTransport = parseInt(document.getElementsByName("resource2")[0].value);
	var deuteriumToTransport = parseInt(document.getElementsByName("resource3")[0].value);

	if (isNaN(metalToTransport))
	{
		metalToTransport = 0;
	}
	if (isNaN(crystalToTransport))
	{
		crystalToTransport = 0;
	}
	if (isNaN(deuteriumToTransport))
	{
		deuteriumToTransport = 0;
	}

	var freeCapacity = Math.max(storCap - metalToTransport - crystalToTransport - deuteriumToTransport, 0);
	var cargo = Math.min(freeCapacity + thisresourcechosen, thisresource);

	if (document.getElementsByName("resource" + id)[0])
	{
		document.getElementsByName("resource" + id)[0].value = cargo;
	}
	calculateTransportCapacity();
}

function maxResources()
{

	var storCap = storage();
	var metalToTransport = document.getElementsByName("thisresource1")[0].value;
	var crystalToTransport = document.getElementsByName("thisresource2")[0].value;
	var deuteriumToTransport = document.getElementsByName("thisresource3")[0].value - consumption();

	var freeCapacity = storCap - metalToTransport - crystalToTransport - deuteriumToTransport;
	if (freeCapacity < 0)
	{
		metalToTransport = Math.min(metalToTransport, storCap);
		crystalToTransport = Math.min(crystalToTransport, storCap - metalToTransport);
		deuteriumToTransport = Math.min(deuteriumToTransport, storCap - metalToTransport - crystalToTransport);
	}
	document.getElementsByName("resource1")[0].value = Math.max(metalToTransport, 0);
	document.getElementsByName("resource2")[0].value = Math.max(crystalToTransport, 0);
	document.getElementsByName("resource3")[0].value = Math.max(deuteriumToTransport, 0);
	calculateTransportCapacity();
}

function maxShip(id)
{
	if (document.getElementsByName(id)[0])
	{
		document.getElementsByName(id)[0].value = document.getElementsByName("max" + id)[0].value;
	}
}

function maxShips()
{
	var id;
	for (var i = 200; i < 230; i++)
	{
		id = "ship" + i;
		maxShip(id);
	}
}

function noShip(id)
{
	if (document.getElementsByName(id)[0])
	{
		document.getElementsByName(id)[0].value = 0;
	}
}

function noShips()
{
	var id;
	for (var i = 200; i < 230; i++)
	{
		id = "ship" + i;
		noShip(id);
	}
}

function calculateTransportCapacity()
{
	var hold = 0;

	if (mission == 5 && $("select[name=holdingtime]").length)
	{
		var holdtime = $("select[name=holdingtime]").val();

		if (holdtime > 0)
		{
			hold = parseInt($('input[name=stayConsumption]').val()) * holdtime;
		}
	}

	var metal = Math.abs(document.getElementsByName("resource1")[0].value);
	var crystal = Math.abs(document.getElementsByName("resource2")[0].value);
	var deuterium = Math.abs(document.getElementsByName("resource3")[0].value);

	var transportCapacity = storage() - metal - crystal - deuterium - hold;

	if (transportCapacity < 0)
		$("#remainingresources").html("<font color=red>" + number_format(transportCapacity, 0, ',', '.') + "</font>");
	else
		$("#remainingresources").html("<font color=lime>" + number_format(transportCapacity, 0, ',', '.') + "</font>");

	return transportCapacity;
}

function getLayerRef(id, document)
{
	if (!document)
		document = window.document;

	if (document.layers)
	{
		for (var l = 0; l < document.layers.length; l++)
			if (document.layers[l].id == id)
				return document.layers[l];
		for (l = 0; l < document.layers.length; l++)
		{
			var result = getLayerRef(id, document.layers[l].document);
			if (result)
				return result;
		}
		return null;
	}
	else if (document.all)
	{
		return document.all[id];
	}
	else if (document.getElementById)
	{
		return document.getElementById(id);
	}
}

function setVisibility(objLayer, visible)
{
	if (document.layers)
	{
		objLayer.visibility =
				(visible == true) ? 'show' : 'hide';
	}
	else
	{
		objLayer.style.visibility =
				(visible == true) ? 'visible' : 'hidden';
	}
}

function setVisibilityForDivByPrefix(prefix, visible, d)
{
	if (!d)
		d = window.document;

	if (document.layers)
	{
		for (var i = 0; i < d.layers.length; i++)
		{
			if (d.layers[i].id.substr(0, prefix.length) == prefix)
				setVisibility(d.layers[l], visible);
			setVisibilityForDivByPrefix(prefix, visible, d.layers[i].document);
		}
	}
	else if (document.all)
	{
		var layers = document.all.tags("div");
		for (i = 0; i < layers.length; i++)
		{
			if (layers[i].id.substr(0, prefix.length) == prefix)
				setVisibility(document.all.tags("div")[i].visible);
		}
	}
	else if (document.getElementsByTagName)
	{
		var layers = document.getElementsByTagName("div");
		for (i = 0; i < layers.length; i++)
		{
			if (layers[i].id.substr(0, prefix.length) == prefix)
				setVisibility(layers[i].visible);
		}
	}
}

function setPlanet(string)
{
	var splitstring = string.split(":");
	document.getElementsByName('galaxy')[0].value = splitstring[0];
	document.getElementsByName('system')[0].value = splitstring[1];
	document.getElementsByName('planet')[0].value = splitstring[2];
	document.getElementsByName('planettype')[0].value = splitstring[3];
	setMission(splitstring[4]);
}

function setUnions(cnt)
{
	var galaxy = document.getElementsByName('galaxy')[0].value;
	var system = document.getElementsByName('system')[0].value;
	var planet = document.getElementsByName('planet')[0].value;
	var planettype = document.getElementsByName('planettype')[0].value;

	var thisgalaxy = document.getElementsByName("thisgalaxy")[0].value;
	var thissystem = document.getElementsByName("thissystem")[0].value;
	var thisplanet = document.getElementsByName("thisplanet")[0].value;
	var thisplanettype = document.getElementsByName("thisplanettype")[0].value;

	var spd = document.getElementsByName("speed")[0].value;
	var speedfactor = document.getElementsByName("speedfactor")[0].value;

	var time, targetgalaxy, targetsystem, targetplanet, targetplanettype, inSpeedLimit;

	for (var i = 0; i < cnt; i++)
	{
		var string = document.getElementById("union" + i).innerHTML;
		time = document.getElementsByName('union' + i + 'time')[0].value;

		targetgalaxy = document.getElementsByName('union' + i + 'galaxy')[0].value;
		targetsystem = document.getElementsByName('union' + i + 'system')[0].value;
		targetplanet = document.getElementsByName('union' + i + 'planet')[0].value;
		targetplanettype = document.getElementsByName('union' + i + 'planettype')[0].value;

		if (targetgalaxy == galaxy && targetsystem == system && targetplanet == planet && targetplanettype == planettype)
		{

			inSpeedLimit = isInSpeedLimit(flightTime(thisgalaxy, thissystem, thisplanet, targetgalaxy, targetsystem, targetplanet, spd, maxspeed(), speedfactor), time);

			if (inSpeedLimit == 2)
			{
				document.getElementById("union" + i).innerHTML = '<font color="lime">' + string + '</font>';
			}
			else if (inSpeedLimit == 1)
			{
				document.getElementById("union" + i).innerHTML = '<font color="orange">' + string + '</font>';
			}
			else
			{
				document.getElementById("union" + i).innerHTML = '<font color="red">' + string + '</font>';
			}
		}
		else
		{
			document.getElementById("union" + i).innerHTML = '<font color="#00a0ff">' + string + '</font>';
		}
	}
}

function isInSpeedLimit(flightlength, eventtime)
{
	var time = new Date();
	time = Math.round(time / 1000);
	if (flightlength < ((eventtime - time) * (1 + 0.5)))
	{
		return 2;
	}
	else if (flightlength < ((eventtime - time) * 1))
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

function flightTime(galaxy, system, planet, targetgalaxy, targetsystem, targetplanet, spd, maxspeed, speedfactor)
{

	var dist;

	if ((galaxy - targetgalaxy) != 0)
	{
		dist = Math.abs(galaxy - targetgalaxy) * 20000;
	}
	else if ((system - targetsystem) != 0)
	{
		dist = Math.abs(system - targetsystem) * 5 * 19 + 2700;
	}
	else if ((planet - targetplanet) != 0)
	{
		dist = Math.abs(planet - targetplanet) * 5 + 1000;
	}
	else
	{
		dist = 5;
	}
	return Math.round(((35000 / spd * Math.sqrt(dist * 10 / maxspeed) + 10) / speedfactor));
}

function showCoords()
{
	document.getElementsByName('speed')[0].disabled = false;
	document.getElementsByName('galaxy')[0].disabled = false;
	document.getElementsByName('system')[0].disabled = false;
	document.getElementsByName('planet')[0].disabled = false;
	document.getElementsByName('planettype')[0].disabled = false;
	document.getElementsByName('shortlinks')[0].disabled = false;
}

function hideCoords()
{
	document.getElementsByName('speed')[0].disabled = true;
	document.getElementsByName('galaxy')[0].disabled = true;
	document.getElementsByName('system')[0].disabled = true;
	document.getElementsByName('planet')[0].disabled = true;
	document.getElementsByName('planettype')[0].disabled = true;
	document.getElementsByName('shortlinks')[0].disabled = true;
}

function showOrders()
{
	document.getElementsByName('order')[0].disabled = false;
}

function hideOrders()
{
	document.getElementsByName('order')[0].disabled = true;
}

function showResources()
{
	document.getElementsByName('resource1')[0].disabled = false;
	document.getElementsByName('resource2')[0].disabled = false;
	document.getElementsByName('resource3')[0].disabled = false;
	document.getElementsByName('holdingtime')[0].disabled = false;
}

function hideResources()
{
	document.getElementsByName('resource1')[0].disabled = true;
	document.getElementsByName('resource2')[0].disabled = true;
	document.getElementsByName('resource3')[0].disabled = true;
	document.getElementsByName('holdingtime')[0].disabled = true;
}

function setShips(s16, s17, s18, s19, s20, s21, s22, s23, s24, s25, s27, s28, s29)
{

	setNumber('202', s16);
	setNumber('203', s17);
	setNumber('204', s18);
	setNumber('205', s19);
	setNumber('206', s20);
	setNumber('207', s21);
	setNumber('208', s22);
	setNumber('209', s23);
	setNumber('210', s24);
	setNumber('211', s25);
	setNumber('213', s27);
	setNumber('214', s28);
	setNumber('215', s29);

}

function setNumber(name, number)
{
	if (typeof document.getElementsByName('ship' + name)[0] != 'undefined')
	{
		document.getElementsByName('ship' + name)[0].value = number;
	}
}

function tsdpkt(f)
{
	var r = "";
	var vz = "";
	if (f < 0)
	{
		vz = "-";
	}
	f = abs(f);
	r = f % 1000;
	while (f >= 1000)
	{
		k1 = "";
		if ((f % 1000) < 100)
		{
			k1 = "0";
		}
		if ((f % 1000) < 10)
		{
			k1 = "00";
		}
		if ((f % 1000) == 0)
		{
			k1 = "00";
		}
		f = abs((f - (f % 1000)) / 1000);
		r = f % 1000 + "." + k1 + r;
	}
	r = vz + r;
	return r;
}

function abs(a)
{
	if (a < 0) return -a;
	return a;
}

function ACS(id)
{
	document.getElementsByName('acs')[0].value = id;
}

function t()
{
	var v = new Date();
	var n = new Date();
	var o = new Date();

	var bxx, ss, s, m, h;

	for (var cn = 1; cn <= anz; cn++)
	{
		bxx = $('#bxx' + cn);
		ss = bxx.attr('title');
		s = ss - Math.round((n.getTime() - v.getTime()) / 1000.);
		m = 0;
		h = 0;
		if (s < 0)
		{
			bxx.html("-");
		}
		else
		{
			if (s > 59)
			{
				m = Math.floor(s / 60);
				s = s - m * 60;
			}
			if (m > 59)
			{
				h = Math.floor(m / 60);
				m = m - h * 60;
			}
			if (s < 10)
			{
				s = "0" + s;
			}
			if (m < 10)
			{
				m = "0" + m;
			}
			bxx.html(h + ":" + m + ":" + s);
		}
		bxx.attr('title', ss - 1);
	}
	window.setTimeout("t();", 999);
}

function addZeros(value, count)
{
	var ret = "";
	var ost;
	for (i = 0; i < count; i++)
	{
		ost = value % 10;
		value = Math.floor(value / 10);
		ret = ost + ret;
	}
	return(ret);
}

function validate_number(value)
{
	if (value == 0)
	{
		ret = 0;
	}
	else
	{
		var inv;
		if (value < 0)
		{
			value = -value;
			inv = 1;
		}
		else
		{
			inv = 0;
		}

		var ret = "";
		var ost;

		while (value > 0)
		{
			ost = value % 1000;
			value = Math.floor(value / 1000);

			if (value <= 0)
				s_ost = ost;
			else
				s_ost = addZeros(ost, 3);

			if (ret == "")
				ret = s_ost;
			else
				ret = s_ost + "." + ret;
		}
		if (inv == 1)
		{
			ret = "-" + ret;
		}
	}
	return(ret);
}

function chShipCount(id, diff)
{
	diff = parseInt(diff);
	var ncur = parseInt(document.getElementsByName("ship" + id)[0].value);
	var count = ncur + diff;
	if(count < 0){
		count = 0;
	}
	if(count > document.getElementsByName("maxship" + id)[0].value){
		count = document.getElementsByName("maxship" + id)[0].value;
	}
	document.getElementsByName("ship" + id)[0].value = count;
}