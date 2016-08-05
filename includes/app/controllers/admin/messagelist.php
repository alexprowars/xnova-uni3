<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2013 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 * @var $user user
 */

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 2)
{
	strings::includeLang('admin/messagelist');

	$Prev = (!empty($_POST['prev'])) ? true : false;
	$Next = (!empty($_POST['next'])) ? true : false;
	$DelSel = (!empty($_POST['delsel'])) ? true : false;
	$DelDat = (!empty($_POST['deldat'])) ? true : false;
	$CurrPage = (!empty($_POST['curr'])) ? intval($_POST['curr']) : 1;
	$Selected = (!empty($_POST['type'])) ? intval($_POST['type']) : 1;
	$SelPage = @$_POST['page'];

	if ($Selected == 6)
		$Selected = 0;

	$ViewPage = (!empty($SelPage)) ? $SelPage : 1;

	if ($Prev == true)
	{
		$CurrPage -= 1;

		if ($CurrPage >= 1)
			$ViewPage = $CurrPage;
		else
			$ViewPage = 1;
	}
	elseif ($Next == true)
	{
		$CurrPage += 1;

		$ViewPage = $CurrPage;
	}
	elseif ($DelSel == true)
	{
		foreach ($_POST['sele_mes'] as $MessId => $Value)
		{
			if ($Value = "on")
				db::query("DELETE FROM game_messages WHERE `message_id` = '" . $MessId . "';");
		}
	}
	elseif ($DelDat == true)
	{
		$SelDay 	= intval($_POST['selday']);
		$SelMonth 	= intval($_POST['selmonth']);
		$SelYear 	= intval($_POST['selyear']);

		$LimitDate = mktime(0, 0, 0, $SelMonth, $SelDay, $SelYear);

		if ($LimitDate != false)
		{
			db::query("DELETE FROM game_messages WHERE `message_time` <= '" . $LimitDate . "';");
			db::query("DELETE FROM game_rw WHERE `time` <= '" . $LimitDate . "';");
		}
	}

	$Mess = db::query("SELECT COUNT(*) AS `max` FROM game_messages WHERE `message_type` = '" . $Selected . "';", true);
	$MaxPage = ceil(($Mess['max'] / 25));

	$parse = array();
	$parse['mlst_data_page'] = $ViewPage;
	$parse['mlst_data_pagemax'] = $MaxPage;
	$parse['mlst_data_sele'] = $Selected;

	if (isset($_POST['userid']) && $_POST['userid'] != "")
	{
		$userid = " AND message_owner = " . intval($_POST['userid']) . "";
		$parse['userid'] = intval($_POST['userid']);
	}
	elseif (isset($_POST['userid_s']) && $_POST['userid_s'] != "")
	{
		$userid = " AND message_sender = " . intval($_POST['userid_s']) . "";
		$parse['userid_s'] = intval($_POST['userid_s']);
	}
	else
		$userid = "";

	$Messages = db::query("SELECT m.*, u.username FROM game_messages m LEFT JOIN game_users u ON u.id = m.message_owner WHERE m.`message_type` = '" . $Selected . "' " . $userid . " ORDER BY m.`message_time` DESC LIMIT " . (($ViewPage - 1) * 25) . ",25;");

	$parse['mlst_data_rows'] = array();

	while ($row = db::fetch_assoc($Messages))
	{
		$bloc['mlst_id'] = $row['message_id'];
		$bloc['mlst_from'] = $row['message_from'];
		$bloc['mlst_to'] = $row['username'] . " ID:" . $row['message_owner'];
		$bloc['mlst_text'] = $row['message_text'];
		$bloc['mlst_time'] = date("d.m.Y H:i:s", $row['message_time']);

		$parse['mlst_data_rows'][] = $bloc;
	}

	if (isset($_POST['delit']))
	{
		db::query("DELETE FROM game_messages WHERE `message_id` = '" . $_POST['delit'] . "';");
		$this->message(_getText('mlst_mess_del') . " ( " . $_POST['delit'] . " )", _getText('mlst_title'), "?set=admin&mode=messagelist", 3);
	}

	$this->setTemplate('messagelist');
	$this->set('parse', $parse);

	$this->display('', _getText('mlst_title'), false, true);
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>