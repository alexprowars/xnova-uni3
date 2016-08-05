<?php

function is ($val, $key)
{
	return (isset($val[$key]) ? $val[$key] : '');
}

function is_email ($email)
{
	if (!$email)
		return false;

	if (preg_match('#^[^\\x00-\\x1f@]+@[^\\x00-\\x1f@]{2,}\.[a-z]{2,}$#iu', $email) == 0)
	{
		return false;
	}
	return true;
}

function message ($text, $title = 'Ошибка', $dest = "", $time = 3, $left = true)
{
	if (!class_exists('app'))
		die($text);

	$page = new pageHelper();
	$page->setTemplate('message');
	$page->set('text', $text);
	$page->set('title', $title);
	$page->set('destination', $dest);
	$page->set('time', $time);

	$page->display('', ($title ? strip_tags($title) : 'Сообщение'), false, $left);
}

function datezone ($format, $time = 0)
{
	global $user;

	if ($time == 0)
		$time = time();

	if (isset($user->data['timezone']))
		$time += $user->data['timezone'] * 1800;

	return date($format, $time);
}

/**
 * @return array|string
 */
function _getText ()
{
	return strings::getText(func_get_args());
}

function showProfiler ()
{
	profiler::collectData();

	$result = '<div id="ptb"><ul id="ptb_toolbar" class=""><li class="toolbar-info">' . VERSION . '</li>';

	if (profiler::cfg('showTotalInfo')):
		$result .= '<li class="time" title="application execution time"><span class="icon"></span> ' . profiler::formatTime(profiler::$DATA_APP_TIME) . '</li>
	      			<li class="ram" title="memory peak usage"> <span class="icon"></span> ' . profiler::formatMemory(profiler::$DATA_APP_MEMORY) . '</li>';
	endif;
	if (profiler::cfg('showSql')):
		$_total = 0;
		if (count(profiler::$DATA_SQL) > 0)
			foreach (profiler::$DATA_SQL as $v)
				$_total += $v['total']['count'];

		$result .= '<li class="sql"><span class="icon"></span> sql <span class="total">(' . $_total . ')</span></li>';
	endif;
	if (profiler::cfg('showCache')):
		$result .= '<li class="cache"><span class="icon"></span> cache <span class="total" title="get cache">' . profiler::$DATA_CACHE['total']['get'] . '</span><span class="total" title="set cache">/' . profiler::$DATA_CACHE['total']['set'] . '</span><span class="total" title="delete cache">/' . profiler::$DATA_CACHE['total']['del'] . '</span></li>';
	endif;
	if (profiler::cfg('showVars')):
		$result .= '<li class="vars"><span class="icon"></span> vars <span class="total" title="$_POST">' . count(profiler::$DATA_POST) . '</span><span class="total" title="$_GET">/' . count(profiler::$DATA_GET) . '</span><span class="total" title="$_FILES">/' . count(profiler::$DATA_FILES) . '</span><span class="total" title="$_COOKIE">/' . count(profiler::$DATA_COOKIE) . '</span><span class="total" title="$_SESSION">/' . count(profiler::$DATA_SESSION) . '</span></li>';
	endif;
	if (profiler::cfg('showRoutes')):
		$result .= '<li class="route"><span class="icon"></span> route</li>';
	endif;
	if (profiler::cfg('showIncFiles')):
		$result .= '<li class="files"><span class="icon"></span> files <span class="total">(' . profiler::$DATA_INC_FILES['total']['count'] . ')</span></li>';
	endif;
	if (profiler::cfg('showCustom')):
		$_total = 0;
		if (count(profiler::$DATA_CUSTOM) > 0)
			foreach (profiler::$DATA_CUSTOM as $v)
				$_total += count($v);

		$result .= '<li class="custom"><span class="icon"></span> YOUR <span class="total">(' . $_total . ')</span></li>';
	endif;

	$result .= '<li class="hide" title="Hide Profiler Toolbar"><span class="icon"></span></li><li class="show" title="Show Profiler Toolbar"><span class="icon"></span></li></ul>
	 			<div id="ptb_data" class="ptb_bg" style="display: none;">';

	if (profiler::cfg('showSql'))
		$result .= profiler::render('sql');
	if (profiler::cfg('showCache'))
		$result .= profiler::render('cache');
	if (profiler::cfg('showVars'))
		$result .= profiler::render('vars');
	if (profiler::cfg('showRoutes'))
		$result .= profiler::render('route');
	if (profiler::cfg('showIncFiles'))
		$result .= profiler::render('files');
	if (profiler::cfg('showCustom'))
		$result .= profiler::render('custom');

	$result .= '</div></div>';

	return $result;
}

function p ($array)
{
	if (user::get()->isAdmin())
	{
		echo '<pre>'; print_r($array); echo '</pre>';
	}
}
 
?>