<?php

if (!defined('INSIDE')) {
	die("attemp hacking");
}

// Registration von Usern
$lang['registry']          = 'Регистрация';
$lang['form']              = 'Форма регистрации';
$lang['Register']          = 'XNova Регистрация';
$lang['Undefined']         = 'Не указано';
$lang['Male']              = 'Мужской';
$lang['Female']            = 'Женский';
$lang['Multiverse']        = '<b>XNova</b> UfaNet';
$lang['E-Mail']            = 'E-Mail Адрес (прим. addy@mail.com)';
$lang['MainPlanet']        = 'Название главной планеты';
$lang['GameName']          = 'Логин';
$lang['Sex']               = 'Пол';
$lang['accept']            = 'Я принимаю <a href="../../game/agb.php?conditions">Основные положения и правила</a>';
$lang['signup']            = 'Регистрация';
$lang['neededpass']        = 'Пароль';
$lang['Languese']          = 'Язык';
$lang['ru']                = 'Русский';

// User Send
$lang['mail_welcome']      = '
Спасибо за регистрацию в игре XNova Game
Ваш пароль от аккаунта : {password}
Запомните его!!!

С уважением администрация {gameurl}. На это сообщение отвечать не нужно.';
$lang['mail_title']        = 'Регистрация в игре XNova (xnova.su)';
$lang['thanksforregistry'] = '<a href="../../">На главную...</a><br /><br />Спасибо за регистрацию.<br />Копия пароля выслана вам на E-mail адрес';

// Registrierungs Fehler
$lang['error_mail']        = 'Неверный E-Mail!<br />';
$lang['error_captcha']        = 'Неверный код с картинки!<br />';
$lang['error_planet']      = 'Другая планета уже имеет то же название!<br />';
$lang['error_hplanetnum']  = 'В название планеты не должны содержаться некорректные символы.<br />';
$lang['error_character']   = 'Неверное имя.<br />';
$lang['error_charalpha']   = 'Вы можете использовать только латинские буквы и цифры!<br />';
$lang['error_lang']   = 'Неверно указан язык игры.<br />';
$lang['error_password']    = 'Пароль должен состоять как минимум из 4 знаков!<br />';
$lang['error_rgt']         = 'Вы должны согласиться с правилами!<br />';
$lang['error_userexist']   = 'Такое имя уже используется!<br />';
$lang['error_emailexist']  = 'Такой e-mail уже используется!<br />';
$lang['error_sex']         = 'Ошибка в выборе пола!<br />';
$lang['error_mailsend']    = 'Ошибка в отправлении электронной почти, ваш пароль: ';
$lang['reg_welldone']      = 'Регистрация завершена!';

?>