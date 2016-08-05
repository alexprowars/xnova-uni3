<table width="100%">
	<tr>
		<td class=c colspan=3>Привлечённые игроки</td>
<tr>

	<? if (count($parse['ref']) > 0): ?>
	<tr>
		<td class=c>Ник</td>
		<td class=c>Дата регистрации</td>
		<td class=c>Уровень развития</td>
	</tr>

	<? foreach ($parse['ref'] AS $list): ?>
		<tr>
			<th><? if (datezone("d", $list['register_time']) >= 15)
				echo '+&nbsp;'; ?><a href="?set=players&id=<?=$list['id'] ?>"><?=$list['username'] ?></a></th>
			<th><?=datezone("d.m.Y H:i", $list['register_time']) ?></th>
			<th>П:<?=$list['lvl_minier'] ?>, В:<?=$list['lvl_raid'] ?></th>
		</tr>
		<? endforeach; ?>
	<? else: ?>
	<tr>
		<th colspan="3">Нет привлеченных игроков</th>
	</tr>
	<? endif; ?>
</table>

<? if (isset($parse['you'])): ?>
	<br><br>
	<table width=100%>
		<tr>
			<th>Вы были привлечены игроком:</th>
			<th><a href="?set=players&id=<?=$parse['you']['id'] ?>"><?=$parse['you']['username'] ?></a></th>
		</tr>
	</table>
<? endif; ?>

<? if (core::getConfig('socialIframeView', 0) == 0): ?>
	<br><br>
	<table width=100%>
		<tr>
			<th>Поделиться:</th>
			<th><script type="text/javascript" src="//yandex.st/share/share.js"
		charset="utf-8"></script>
		<div class="yashare-auto-init"
			data-yashareL10n="ru"
			data-yashareType="icon"
			data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj,friendfeed,moikrug,gplus,surfingbird"
			data-yashareLink="http://uni<?=UNIVERSE ?>.xnova.su/?<?=$userId ?>"
			data-yashareTitle="<?=core::getConfig('game_name') ?>"
		></div>
		</th></tr></table>

	<div class="separator"></div>
	<table class="table">
		<tr>
			<td class="c">Юзербар</td>
		</tr>
		<tr>
			<th>
				<br>
				<img src="/userbar<?=$userId ?>.jpg">

				<br><br>
				HTML код:
				<br>
				<input style="width:100%" type="text" value="<?=htmlspecialchars('<a href="http://uni'.UNIVERSE.'.xnova.su/?'.$userId.'"><img src="http://uni'.UNIVERSE.'.xnova.su/userbar'.$userId.'.jpg"></a>') ?>">
				<div class="separator"></div>
				BB код:
				<input style="width:100%" type="text" value="<?=htmlspecialchars('[url=http://uni'.UNIVERSE.'.xnova.su/?'.$userId.'][img]http://uni'.UNIVERSE.'.xnova.su/userbar'.$userId.'.jpg[/img][/url]') ?>">
			</th>
		</tr>
	</table>
<? endif; ?>

<? if (SERVER_CODE == 'OK1U'): ?>
	<br><br>
	<table width=100%>
		<tr>
				<td class=c>Информация</td>
		<tr>
		<tr>
			<th>Приглашайте друзей в игру, и с каждым их полученным боевым и мирным уровнем вам будет начислено некоторое количество кредитов.
			<br><br>
			<input type="button" value="Пригласить друзей" onclick="FAPI.UI.showInvite('Пришло время воевать!', 'userId=<?=$userId ?>');">
			</th>
		</tr>
	</table>
<? endif; ?>