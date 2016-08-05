<? if (SERVER_CODE == 'OK1U'): ?>
	<script type="text/javascript">
		var title = new Array();
		title[0] = "Звездная империя – космическая стратегия в реальном времени.";
		title[1] = "Звездная империя - отличная космическая войнушка.";
		title[2] = "Звездная империя - интересная игра, приглашаю стать моим союзником.";
		title[3] = "Отстраивай планету и повышай свой рейтинг";
		title[4] = "Ищу союзников, для серьезных дел.";
		title[5] = "Игра затягивает! Советую!";
		title[6] = "Друзья, помогите мне с победой. Спасибо!";
		title[7] = "Пришло время воевать!";

		function showInviteBox ()
		{
			var text = title[Math.round(Math.random()*7)];

			FAPI.UI.showInvite(text, 'userId=<?=$userId ?>');
		}
	</script>
<? endif; ?>
<div id="boxBG" class="contentBoxBody set_<?=$set ?>">
	<div id="box">
		<div id="game_menu">
			<div class="bar">
				<div class="message_list">
					<? if (isset($tutorial) && $tutorial < 10): ?>
						<a class="m1 tooltip" href="?set=tutorial" data-tooltip-content="Обучение"><span class="sprite ico_tutorial"></span></a>
					<? endif; ?>
					<a class="m1 tooltip" href="?set=chat" data-tooltip-content="Чат"><span class="sprite ico_chat"></span></a>
					<a class="m1 tooltip" href="?set=messages" data-tooltip-content="Сообщения"><span class="sprite ico_mail"></span> <b id="new_messages"><?=$mess ?></b></a>
					<? if ($mess_ally != ''): ?>
						<a class="m1 tooltip" href="?set=alliance&mode=circular" data-tooltip-content="Альянс"><span class="sprite ico_alliance"></span> <b id="ally_messages"><?=$mess_ally ?></b></a>
					<? endif; ?>
				</div>
				<table align="center" class="top_menu">
					<tr>
						<? if (SERVER_CODE == 'OK1U' && core::getConfig('socialIframeView', 0)): ?>
							<td class="m1"><a href="?fullscreen=Y" target="_blank" class="tooltip sprite ico_fullscreen" data-tooltip-content="Развернуть"></a></td>
						<? endif; ?>
						<? if (!core::getConfig('socialIframeView', 0)): ?>
							<td class="m1"><a href="http://xnova.su/" target="_blank" class="tooltip sprite ico_space" data-tooltip-content="Вселенные"></a></td>
						<? endif; ?>
						<td class="m1"><a href="?set=stat" class="tooltip sprite ico_stats" data-tooltip-content="Статистика"></a></td>
						<td class="m1"><a href="?set=techtree" class="tooltip sprite ico_tech" data-tooltip-content="Технологии"></a>
						<td class="m1"><a href="?set=sim" class="tooltip sprite ico_sim" data-tooltip-content="Симулятор"></a>
						<td class="m1"><a href="?set=search" class="tooltip sprite ico_search" data-tooltip-content="Поиск"></a></td>
						<? if (SERVER_CODE != 'OK1U'): ?>
							<td class="m1"><a href="?set=support" class="tooltip sprite ico_support" data-tooltip-content="Техподдержка"></a></td>
						<? endif; ?>
						<? if (SERVER_CODE != 'OK1U'): ?>
							<td class="m1"><a href="<?=core::getConfig('forum_url', '') ?>" target="_blank" class="tooltip sprite ico_forum" data-tooltip-content="Форум"></a></td>
						<? endif; ?>
						<td class="m1"><a href="?set=options" class="tooltip sprite ico_settings" data-tooltip-content="Настройки"></a></td>
						<? if (SERVER_CODE != 'OK1U'): ?>
							<td class="m1"><a href="?set=logout&popup" class="tooltip sprite ico_exit" data-tooltip-content="Выход"></a></td>
						<? endif; ?>
						<? if (SERVER_CODE == 'OK1U'): ?>
							<? if (!$_COOKIE[COOKIE_NAME.'_full']): ?>
								<td class="m1"><a style="color:#38CA38" href="javascript:;" onclick="showInviteBox()">Пригласить друзей</a></td>
							<? endif; ?>
						<? endif; ?>
					</tr>
				</table>
			</div>
		</div>
		<table width="100%" <? if (core::getConfig('socialIframeView', 0) == 0): ?>style="margin-top: 5px;"<? endif; ?>>
			<tr>
				<td class="menu"></td>
				<td></td>
				<? if (!core::getConfig('socialIframeView', 0) && core::getConfig('overviewListView', 0) == 1): ?>
					<td class="planetList" style="padding: 0 0 0 20px"></td>
				<? endif; ?>
			</tr>
			<tr>
				<td class="menu" height="100%" valign="top" align="left">
					<ul id="menu_links">
						<? foreach(_getText('main_menu') AS $id => $menu): if ($menu[2] > $adminlevel) continue; ?>
							<li><a id="link_<?=$id ?>" <? if (isset($menu[3])): ?>data-link="1"<? endif; ?> href="<?=$menu[1] ?>" <?=($set == $id ? 'class="check"' : '') ?>><?=$menu[0] ?></a></li>
						<? endforeach; ?>
					</ul>
					<!-- Yandex.Metrika counter -->
					<script type="text/javascript">
					(function (d, w, c) {
						(w[c] = w[c] || []).push(function() {
							try {
								w.yaCounter25961143 = new Ya.Metrika({id:25961143});
							} catch(e) { }
						});

						var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); };
						s.type = "text/javascript";
						s.async = true;
						s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

						if (w.opera == "[object Opera]") {
							d.addEventListener("DOMContentLoaded", f, false);
						} else { f(); }
					})(document, window, "yandex_metrika_callbacks");
					</script>
					<!-- /Yandex.Metrika counter -->
				</td>
				<td valign='top' align='center'>
					<? if (core::getConfig('socialIframeView', 0) == 1): ?>
						<div class="iframe_wrapper">
					<? endif; ?>
					<div id="gamediv">