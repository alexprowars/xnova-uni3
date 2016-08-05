<?php
/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

define('INSIDE', true);

session_start();

require($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/class.core.php');
core::init();
core::loadConfig();
strings::setLang('ru');

require(ROOT_DIR.'includes/bootstrap.php');

?>