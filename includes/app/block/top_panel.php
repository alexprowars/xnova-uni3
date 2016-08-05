<?php

/**
 * @author AlexPro
 * @copyright 2011 - 2013.ru
 * @var $Display templateDisplay
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

$Display = $this->_HSTemplate->getDisplay('block');

$parse = array();

if (isset(app::$planetrow))
	$parse = ShowTopNavigationBar();

$parse['tutorial'] = app::$user->data['tutorial'];

$Display->addTemplate('top', 'top_panel.php');
$Display->assign('parse', $parse, 'top');

$Display->display();

?>
