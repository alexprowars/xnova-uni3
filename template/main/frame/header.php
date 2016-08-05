<? if (!$isAjax): ?>
	<!DOCTYPE HTML>
	<html>
	<head>
		<? foreach ($attributes AS $name => $content): ?>
			<? if ($name == 'title'): ?>
				<title><?=$content ?></title>
			<? else: ?>
				<meta name="<?=$name ?>" content="<?=$content ?>">
			<? endif; ?>
		<? endforeach; ?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=990">

		<link rel="image_src" href="http://<?=$_SERVER['HTTP_HOST'] ?>/images/logo.jpg" />
		<link rel="apple-touch-icon" href="http://<?=$_SERVER['HTTP_HOST'] ?>/images/apple-touch-icon.png"/>

		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

		<link rel="stylesheet" type="text/css" href="<?=DPATH ?>formate.css?v=<?=substr(md5(VERSION), 0, 3) ?>">

		<? if (file_exists(ROOT_DIR.'template/'.core::getConfig('gameTemplate').'/style.css')): ?>
			<link rel="stylesheet" type="text/css" href="/template/<?=core::getConfig('gameTemplate') ?>/style.css?v=<?=substr(md5(VERSION), 0, 3) ?>">
		<? endif; ?>
		<? if (file_exists(ROOT_DIR.'template/'.core::getConfig('gameTemplate').'/design_'.$pageParams['design'].'.css')): ?>
			<link rel="stylesheet" type="text/css" href="/template/<?=core::getConfig('gameTemplate') ?>/design_<?=$pageParams['design'] ?>.css?v=<?=substr(md5(VERSION), 0, 3) ?>">
		<? endif; ?>
		<? if (file_exists(ROOT_DIR.'template/'.core::getConfig('gameTemplate').'/script.js')): ?>
			<script type="text/javascript" src="/template/<?=core::getConfig('gameTemplate') ?>/script.js?v=<?=substr(md5(VERSION), 0, 3) ?>"></script>
		<? endif; ?>

		<script type="text/javascript" src="/scripts/jquery.form.min.js"></script>
		<link rel="stylesheet" type="text/css" href="/scripts/smoothness/jquery-ui-1.10.2.custom.css">
		<script type="text/javascript" src="/scripts/game.js?v=<?=substr(md5(VERSION), 0, 3) ?>"></script>
		<script type="text/javascript" src="/scripts/universe.js?v=<?=substr(md5(VERSION), 0, 3) ?>"></script>
		<script language="JavaScript" src="/scripts/smiles.js?v=<?=substr(md5(VERSION), 0, 3) ?>"></script>
		<script src="/scripts/ed.js?v=<?=substr(md5(VERSION), 0, 3) ?>" type="text/javascript"></script>
		<? if (core::getConfig('DEBUG')): ?>
			<link rel="stylesheet" type="text/css" href="/scripts/profiler.css">
			<script type="text/javascript" src="/scripts/profiler.js"></script>
		<? endif; ?>
	</head>
	<body <? if (core::getConfig('socialIframeView', 0) == 1): ?>class="iframe"<? endif; ?>>
	<script type="text/javascript">
		var timestamp = <?=time() ?>;
		var timezone = <?=$pageParams['timezone'] ?>;
		var ajax_nav = <?=$pageParams['ajaxNavigation'] ?>;
		var addToUrl = '<? if (!isset($_COOKIE[COOKIE_NAME.'_full']) && isset($_SESSION['OKAPI'])): ?><?=http_build_query($_SESSION['OKAPI']) ?><? endif; ?>';
	</script>
	<? if (isset($pageParams['leftMenu']) && $pageParams['leftMenu'] == true): ?>
		<?=$this->ShowBlock('menu'); ?>
	<? endif; ?>

	<? if (isset($pageParams['topPanel']) && $pageParams['topPanel'] == true): ?>
		<?=$this->ShowBlock('top_panel'); ?>
	<? endif; ?>

	<? if (!isset($pageParams['leftMenu']) || $pageParams['leftMenu'] == false): ?>
		<div class="contentBoxBody"><div id="boxBG"><div id="box"><center>
	<? endif; ?>

<? else: ?>

	<? if (isset($pageParams['topPanel']) && $pageParams['topPanel'] == true): ?>
		<?=$this->ShowBlock('top_panel'); ?>
	<? endif; ?>

<? endif; ?>

<? if (isset($pageParams['deleteUserTimer']) && $pageParams['deleteUserTimer'] > 0): ?>
	<table class="table"><tr><td class="c" align="center">Включен режим удаления профиля!<br>Ваш аккаунт будет удалён после <?=datezone("d.m.Y", $pageParams['deleteUserTimer']) ?> в <?=datezone("H:i:s", $pageParams['deleteUserTimer']) ?>. Выключить режим удаления можно в настройках игры.</td></tr></table><div class="separator"></div>
<? endif; ?>

<? if (isset($pageParams['vocationTimer']) && $pageParams['vocationTimer'] > 0): ?>
   <table class="table"><tr><td class="c" align="center"><font color="red">Включен режим отпуска! Функциональность игры ограничена.</font></td></tr></table><div class="separator"></div>
<? endif; ?>