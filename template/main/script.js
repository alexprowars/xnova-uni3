function BuildTimeout(pp, pk, pl, at)
{
	var blc     	= $('#blc');
	var s           = pp;
	var m           = 0;
	var h           = 0;

	if ( s < 0 )
    {
		blc.html("Завершено<br>" + "<a href='#' onclick='load(\"?set=buildings&planet=" + pl + "\")'>Продолжить</a>");

		timeouts['build'+pk+'-'+pl] = window.setTimeout('load("?set=buildings&planet=' + pl + '");', 5000);

		return;
	}
    else
    {
		if ( s > 59) {
			m = Math.floor( s / 60);
			s = s - m * 60;
		}
		if ( m > 59) {
			h = Math.floor( m / 60);
			m = m - h * 60;
		}
		if ( s < 10 ) {
			s = "0" + s;
		}
		if ( m < 10 ) {
			m = "0" + m;
		}

		if (at > timestamp - 5)
			blc.html(h + ":" + m + ":" + s);
		else
			blc.html(h + ":" + m + ":" + s + "<br><a href='#' onclick='load(\"?set=buildings&listid=" + pk + "&cmd=cancel&planet=" + pl + "\")'>Отменить</a>");
	}

	pp--;

	timeouts['build'+pk+'-'+pl] = window.setTimeout("BuildTimeout("+pp+", "+pk+", "+pl+", "+(at - 1)+");", 999);
}

function reloadPlanetList ()
{
	$('.planetList .list').load('/ajax.php?action=getPlanetList');
}

$(document).ready(function()
{
	if (typeof FAPI != 'undefined')
	{
		setInterval(function()
		{
			var d = $('#gamediv');

			FAPI.UI.setWindowSize(800, (d.height() < 600 ? 600 : d.height()) + 150);
			
		}, 1000);
	}

	if ($('.planetList .list').length)
	{
		if( !isMobile )
		{
			$('.planetList .list').css('height', $(window).height() - 100);

			$(window).bind('resize', function()
			{
				$('.planetList .list').css('height', $(window).height() - 100);
			});

			/*if (ajax_nav == 1)
			{
				setInterval(function()
				{
					reloadPlanetList();
				}, 1200000);
			}*/
		}
		else
			$('.planetList .list').css('height', 'auto').css('min-height', 'auto');
	}

	/*
	$(document).on('keydown', function(event)
	{
		if (location.search.indexOf('galaxy') > 0)
		{
			if (event.keyCode == $.ui.keyCode.DOWN)
			{
				event.preventDefault();
				galaxy_submit('galaxyRight');
			}
			else if (event.keyCode == $.ui.keyCode.UP)
			{
				event.preventDefault();
				galaxy_submit('galaxyLeft');
			}
			else if (event.keyCode == $.ui.keyCode.RIGHT)
			{
				event.preventDefault();
				galaxy_submit('systemRight');
			}
			else if (event.keyCode == $.ui.keyCode.LEFT)
			{
				event.preventDefault();
				galaxy_submit('systemLeft');

			}
		}
	});
	*/
});

function changePlanet (pId)
{
	var a = parse_str(document.location.search.substr(1));

	var url = '?set='+a['set']+''+(a['mode'] !== undefined ? '&mode='+a['mode'] : '')+'&cp='+pId+'&re=0';

	if (ajax_nav == 1)
		load(url);
	else
		window.location.href = url;
}

function parse_str (url)
{
	var result = [];

	var lit = url.split('&');

	for (var x=0; x < lit.length; x++)
	{
		var tmp = lit[x].split('=');
		result[unescape(tmp[0])] = unescape(tmp[1]).replace(/[+]/g, ' ');
	}

	return result;
}