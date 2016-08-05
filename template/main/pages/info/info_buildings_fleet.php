<table class="table">
	<? if (!$isPopup): ?>
	<tr>
		<td class="c" colspan="2"><?=$parse['name'] ?></td>
	</tr>
	<? endif; ?>
	<tr>
		<th colspan="2" class="nopadding">
			<table class="margin5">
				<tr>
					<td valign="top"><img src="<?=DPATH ?>gebaeude/<?=$parse['image'] ?>.gif" class="info" align="top" border="0" height="120" width="120"></td>
					<td valign="top" class="left"><?=$parse['description'] ?></td>
				</tr>
			</table>
		</th>
	</tr>
	<tr>
		<th width="50%">Броня</th>
		<th><?=$parse['hull_pt'] ?></th>
	</tr>
	<? if ($parse['image'] != 212): ?>
	<tr>
		<th>Оценка атаки</th>
		<th><?=$parse['attack_pt'] ?></th>
	</tr>
	<tr>
		<th>Грузоподъёмность</th>
		<th><?=$parse['capacity_pt'] ?></th>
	</tr>
	<tr>
		<th>Скорость</th>
		<th><?=$parse['base_speed'] ?></th>
	</tr>
	<tr>
		<th>Потребление топлива (дейтерий)</th>
		<th><?=$parse['base_conso'] ?></th>
	</tr>
	<tr>
		<th>Тип двигателя</th>
		<th><?=$parse['base_engine'] ?></th>
	</tr>
	<tr>
		<th>Тип оружия</th>
		<th><?=$parse['gun'] ?></th>
	</tr>
	<tr>
		<th>Тип брони</th>
		<th><?=$parse['armour'] ?></th>
	</tr>
	<tr>
		<th>Блокировка атаки</th>
		<th><?=$parse['block'] ?>%</th>
	</tr>
		<? if ($parse['upgrade']): ?>
		<tr>
			<th>Усиление на уровень</th>
			<th><?=$parse['upgrade'] ?>%</th>
		</tr>
			<? endif; ?>
		<? endif; ?>
	<tr>
		<td class="c" colspan="2">Затраты на производство</td>
	</tr>
	<tr>
		<th>Металл</th>
		<th><?=$parse['met'] ?></th>
	</tr>
	<tr>
		<th>Кристалл</th>
		<th><?=$parse['cry'] ?></th>
	</tr>
	<tr>
		<th>Дейтерий</th>
		<th><?=$parse['deu'] ?></th>
	</tr>
</table>
<? if ($parse['image'] != 212): ?>
	<div class="separator"></div>
	<table class="table">
		<tr>
			<td class="c left">Скорострел</td>
			<td class="c positive">Поражает флот</td>
			<td class="c negative">Теряет флот</td>
		</tr>
		<? foreach ($parse['speedBattle'] AS $fId => $battle): ?>
			<tr>
				<th class="left"><a href="?set=infos&gid=<?=$fId ?>"><?=_getText('tech', $fId) ?></a></th>
				<th class="positive">
					<? if (isset($battle['TO'])): ?>
						<?=$battle['TO'] ?>
					<? else: ?>
						< 1
					<? endif; ?>
				</th>
				<th class="negative">
					<? if (isset($battle['FROM'])): ?>
						<?=$battle['FROM'] ?>
					<? else: ?>
						< 1
					<? endif; ?>
				</th>
			</tr>
		<? endforeach; ?>
	</table>
<? endif; ?>
<div class="separator"></div>