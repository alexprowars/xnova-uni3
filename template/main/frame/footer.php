<? if (!$isAjax): ?>
	<? if (isset($pageParams['leftMenu']) && $pageParams['leftMenu'] == true): ?>
		</div>
		<div id="loadingOverlay">загрузка...<br><img src="/images/loading.gif" alt=""></div>
		<div id="preloadOverlay"><img src="/images/loading.gif" alt=""></div>

		<? if (core::getConfig('socialIframeView', 0) == 1): ?>
			</div>
		<? endif; ?>
		</td>
			<? if (core::getConfig('overviewListView', 0) == 1): ?>
				<?=$this->ShowBlock('planets', array('ajax' => $isAjax)); ?>
			<? endif; ?>
		</tr>
		</table>
		</div>
		</div></div>

		<div id="siteFooter">
			<div class="content">
				<div class="fleft textLeft">
					<a href="?set=news" title="Последние изменения"><?=VERSION ?></a>
					<? if (SERVER_CODE != 'OK1U'): ?>
						<a target="_blank" href="http://xnova.su/">© 2008 - <?=date("Y") ?> Xcms</a>
					<? endif; ?>
				</div>
				<div class="fright textRight">
					<? if (SERVER_CODE == 'OK1U'): ?>
						<a href="http://www.odnoklassniki.ru/group/56711983595558" class="ok" target="_blank">Группа игры</a>|
					<? endif; ?>
					<a href="http://forum.xnova.su/" target="_blank">Форум</a>|
					<a href="?set=banned">Тёмные</a>|
					<? if (SERVER_CODE != 'OK1U'): ?>
						<a href="wide.php" data-link="1">Большой монитор</a>|
						<a href="http://vk.com/xnova_game" target="_blank">ВК</a>|
						<a href="?set=contact">Контакты</a>|
					<? endif;?>
					<a href="?set=content&article=help">Новичкам</a>|
					<a href="?set=content&article=agb">Правила</a>|
					<a onclick="" title="Игроков в сети" style="color:green"><?=core::getConfig('online') ?></a>/<a onclick="" title="Всего игроков" style="color:yellow"><?=core::getConfig('users_amount') ?></a></div>
				<br class="clearfloat"/></div>
		</div>
	<? endif; ?>

	<?
		if (isset($_REQUEST['apiconnection']) && (!isset($_SESSION['OKAPI']) || !isset($_SESSION['OKAPI']['apiconnection'])))
		{
			$_SESSION['OKAPI'] = Array
			(
				'api_server' => $_REQUEST['api_server'],
				'apiconnection' => $_REQUEST['apiconnection'],
				'session_secret_key' => $_REQUEST['session_secret_key'],
				'session_key' => $_REQUEST['session_key'],
				'logged_user_id' => $_REQUEST['logged_user_id'],
				'sig' => $_REQUEST['sig']
			);
		}
	?>
	<? if (!isset($_COOKIE[COOKIE_NAME.'_full']) && isset($_SESSION['OKAPI']) && is_array($_SESSION['OKAPI'])): ?>
		<script src="<?=$_SESSION['OKAPI']['api_server'] ?>js/fapi5.js" type="text/javascript"></script>
		<script type="text/javascript">
			FAPI.init('<?=$_SESSION['OKAPI']['api_server'] ?>', '<?=$_SESSION['OKAPI']['apiconnection'] ?>',
				function()
				{
					//FAPI.UI.setWindowSize(800, 700);
				}
				, function(error)
				{
					alert("API initialization failed");
				}
			);
		</script>
	<? endif; ?>

	<div id="windowDialog" class="hidden"></div>
	<div id="tooltip" class="tip"></div>

	<? if ((!isset($pageParams['leftMenu']) || $pageParams['leftMenu'] == false)): ?>
		</center></div></div></div>
	<? endif; ?>

	<? if (core::getConfig('DEBUG')): ?>
		<div id="profilerToolbar"><?=showProfiler() ?></div>
	<? endif; ?>

	</body></html>

<? else: ?>

	<? if ($userId > 0 && !$isPopup && core::getConfig('overviewListView', 0) == 1): ?>
		<?=$this->ShowBlock('planets', array('ajax' => $isAjax)); ?>
	<? endif; ?>

	<? if (!$isPopup): ?>
		<script>document.title = "<?=$attributes['title'] ?>";</script>
	<? endif; ?>

	<? if (request::checkSaveState()): ?>
		<script>addHistoryState("<?=request::getClearQuery() ?>")</script>
	<? endif; ?>

	<? if (core::getConfig('DEBUG')): ?>
		<script>$("#profilerToolbar").html(\'<?=str_replace(Array("\n", "\r", '\'', '"'), '', showProfiler()) ?>\');PTB.init();$(".show").click();</script>
	<? endif; ?>

<? endif; ?>

<? if (isset($pageParams['ajaxNavigation']) && $pageParams['ajaxNavigation']): ?>
	<script type="text/javascript">setMenuItem("<?=((isset($_GET['set'])) ? (($_GET['set'] == 'buildings' && isset($_GET['mode'])) ? $_GET['set'].$_GET['mode'] : $_GET['set']) : '') ?>");</script>
<? endif; ?>

<script type="text/javascript">UpdateGameInfo('<?=$pageParams['newMessages'] ?>', '<?=$pageParams['allyMessages'] ?>'); timestamp = <?=time() ?>;</script>