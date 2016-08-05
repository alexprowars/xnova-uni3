<?php

/**
 * @author AlexPro
 * @copyright 2011 - 2013
 * @var $this templateDisplay
 * @var $params array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

$Display = $this->_HSTemplate->getDisplay('block');

global $user;

$planetsList = cache::get('app::planetlist_'.user::get()->getId().'');

if ($planetsList === false)
{
	$planetsList = user::get()->getUserPlanets(app::$user->data['id']);

	cache::set('app::planetlist_'.user::get()->getId().'', $planetsList, 300);
}

$parse = array();
$parse['list'] = $planetsList;
$parse['current'] = $user->data['current_planet'];

if (!$params['ajax'])
	$Display->addTemplate('planets', 'planets.php');
else
	$Display->addTemplate('planets', 'planets_ajax.php');

$Display->assign('parse', $parse, 'planets');

$Display->display();
 
?>