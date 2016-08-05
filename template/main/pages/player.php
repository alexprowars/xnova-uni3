<style>.image {
	max-width: 556px !important
}</style>
<? if (!$isPopup): ?>
<table width="490">
	<tr>
		<td colspan="3" class="c"><b>Информация об игроке</b></td>
	</tr>
	<tr>
		<th>
<? endif; ?>
			<table class="table">
				<tr>
					<td width="138" height="128" style="background:url(<?=$parse['avatar'] ?>) 50% 50% no-repeat; margin:0; padding: 0 10px 0 0;vertical-align:bottom;text-align: center">
						<? if ($parse['ingame']): ?>
						<a href="javascript:;" onclick="showWindow('<?=$parse['username'] ?>: отправить сообщение', '?set=messages&amp;mode=write&amp;id=<?=$parse['id'] ?>&ajax&popup', 680)" title="Отправить сообщение"><span class='sprite skin_m'></span></a>&nbsp;
						<a href="?set=buddy&amp;a=2&amp;u=<?=$parse['id'] ?>" title="Добавить в друзья"><span class='sprite skin_b'></span></a>
						<? else: ?>&nbsp;<? endif; ?>
					</td>
					<td valign="top">
						<table width="100%">
							<tr>
								<td width="30%">Логин:</td>
								<td><?=$parse['username'] ?></td>
							</tr>
							<tr>
								<td>Планета</td>
								<td>
									<a href="?set=galaxy&r=3&galaxy=<?=$parse['galaxy'] ?>&system=<?=$parse['system'] ?>" style="font-weight:normal"><?=$parse['userplanet'] ?> [<?=$parse['galaxy'] ?>:<?=$parse['system'] ?>:<?=$parse['planet'] ?>]</a>
								</td>
							</tr>
							<? if ($parse['ally_name']): ?>
							<tr>
								<td>Альянс:</td>
								<td><?=$parse['ally_name'] ?></td>
							</tr>
							<? endif; ?>
							<tr>
								<td>Пол:</td>
								<td><?=$parse['sex'] ?></td>
							</tr>
						</table>

					</td>
					<td width="40">
						<? if ($parse['race'] != 0): ?><img src="<?=DPATH ?>images/race<?=$parse['race'] ?>.gif"><? else: ?>&nbsp;<? endif; ?>
					</td>
					<td width="70">
						<img src="/images/ranks/m<?=$parse['m'] ?>.png" alt="Промышленная отрасль" title="Промышленная отрасль"><br>
						<img src="/images/ranks/f<?=$parse['f'] ?>.png" alt="Военная отрасль" title="Военная отрасль">
					</td>
				</tr>

			</table>
			<table class="table">
				<tr>
					<td colspan="3" class="c" width="100%">Статистика игры</td>
				</tr>
				<tr>
					<td class="c" width="30%">&nbsp;</td>
					<td class="c" width="35%">Очки</td>
					<td class="c" width="35%">Место</td>
				</tr>
				<tr>
					<td class="c">Постройки</td>
					<th><?=$parse['build_points'] ?></th>
					<th><?=$parse['build_rank'] ?></th>
				</tr>
				<tr>
					<td class="c">Иследования</td>
					<th><?=$parse['tech_points'] ?></th>
					<th><?=$parse['tech_rank'] ?></th>
				</tr>
				<tr>
					<td class="c">Флот</td>
					<th><?=$parse['fleet_points'] ?></th>
					<th><?=$parse['fleet_rank'] ?></th>
				</tr>
				<tr>
					<td class="c">Оборона</td>
					<th><?=$parse['defs_points'] ?></th>
					<th><?=$parse['defs_rank'] ?></th>
				</tr>
				<tr>
					<td class="c">Всего</td>
					<th><?=$parse['total_points'] ?></th>
					<th><?=$parse['total_rank'] ?></th>
				</tr>
			</table>
			<table class="table">
				<tr>
					<td colspan="3" class="c" width="100%">Статистика боёв</td>
				</tr>
				<tr>
					<td class="c" width="30%">&nbsp;</td>
					<td class="c" width="35%"><b>Сумма</b></td>
					<td class="c" width="35%" align="right"><b>Процент</b></td>
				</tr>
				<tr>
					<td class="c">Победы</td>
					<th><b><?=$parse['wons'] ?></b></th>
					<th align="right"><b><?=$parse['siegprozent'] ?> %</b></th>
				</tr>
				<tr>
					<td class="c">Поражения</td>
					<th><b><?=$parse['loos'] ?></b></th>
					<th align="right"><b><?=$parse['loosprozent'] ?> %</b></th>
				</tr>
				<tr>
					<td class="c">Всего вылетов</td>
					<th><b><?=$parse['total'] ?></b></th>
					<th align="right"><b><?=$parse['totalprozent'] ?> %</b></th>
				</tr>
			</table>
			<? if (!$isPopup): ?>
	</tr>
<? if ($parse['about'] != ''): ?>
		<tr>
			<th class="b">
				<span id="m100500"></span>
				<script type="text/javascript">Text('<?=preg_replace("/(\r\n)/u", "<br>", stripslashes($parse['about'])) ?>', 'm100500');</script>
			</th>
		</tr>
			<? endif; ?>
	</table>
<? else: ?>
	<span id="m100500"></span>
	<script type="text/javascript">Text('<?=preg_replace("/(\r\n)/u", "<br>", stripslashes($parse['about'])) ?>', 'm100500');</script>
<? endif; ?>