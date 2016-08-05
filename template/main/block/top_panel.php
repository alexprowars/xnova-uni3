<script type="text/javascript">
	var ress = new Array(<?=$parse['metal'] ?>, <?=$parse['crystal'] ?>, <?=$parse['deuterium'] ?>);
	var max = new Array(<?=$parse['metal_m'] ?>,<?=$parse['crystal_m'] ?>,<?=$parse['deuterium_m'] ?>);
	var production = new Array(<?=($parse['metal_ph'] / 3600) ?>, <?=($parse['crystal_ph'] / 3600) ?>, <?=($parse['deuterium_ph'] / 3600) ?>);
	timeouts['res_count'] = window.setInterval("Res_count()", 1000);
	var serverTime = <?=$parse['time'] ?>000 - Djs + (timezone + 8) * 1800000;
</script>
<form action="" name="ress" id="ress" style="display:none">
	<input type="hidden" id="metall" value="0">
	<input type="hidden" id="crystall" value="0">
	<input type="hidden" id="deuterium" value="0">
	<input type="hidden" id="bmetall" value="0">
	<input type="hidden" id="bcrystall" value="0">
	<input type="hidden" id="bdeuterium" value="0">
</form>
<table class="topnav" cellpadding="0" cellspacing="0">
	<tr align="center">
		<? if (core::getConfig('showPlanetListSelect', 0) == 1): ?>
			<td>
				<select style="width:150px;" onChange="load(this.options[this.selectedIndex].value);"><?=$parse['planetlist'] ?></select>
			</td>
		<? endif; ?>
		<td width="16%">
			<a onclick="showWindow('<?=_getText('tech', 1) ?>', '?set=infos&gid=1&ajax&popup', 600)" class="tooltip" data-tooltip-content='<table width=150><tr><td width=30%>КПД:</td><td align=right><?=$parse['metal_mp'] ?>%</td></tr><tr><td>В час:</td><td align=right><?=strings::pretty_number($parse['metal_ph']) ?></td></tr><tr><td>День:</td><td align=right><?=strings::pretty_number($parse['metal_ph'] * 24) ?></td></tr></table>'><span class="sprite skin_metall"></span></a>
		</td>
		<td width="16%">
			<a onclick="showWindow('<?=_getText('tech', 2) ?>', '?set=infos&gid=2&ajax&popup', 600)" class="tooltip" data-tooltip-content='<table width=150><tr><td width=30%>КПД:</td><td align=right><?=$parse['crystal_mp'] ?>%</td></tr><tr><td>В час:</td><td align=right><?=strings::pretty_number($parse['crystal_ph']) ?></td></tr><tr><td>День:</td><td align=right><?=strings::pretty_number($parse['crystal_ph'] * 24) ?></td></tr></table>'><span class="sprite skin_kristall"></span></a>
		</td>
		<td width="16%">
			<a onclick="showWindow('<?=_getText('tech', 3) ?>', '?set=infos&gid=3&ajax&popup', 600)" class="tooltip" data-tooltip-content='<table width=150><tr><td width=30%>КПД:</td><td align=right><?=$parse['deuterium_mp'] ?>%</td></tr><tr><td>В час:</td><td align=right><?=strings::pretty_number($parse['deuterium_ph']) ?></td></tr><tr><td>День:</td><td align=right><?=strings::pretty_number($parse['deuterium_ph'] * 24) ?></td></tr></table>'><span class="sprite skin_deuterium"></span></a>
		</td>
		<td width="16%">
			<a onclick="showWindow('<?=_getText('tech', 4) ?>', '?set=infos&gid=4&ajax&popup', 600)" title="<?=_getText('tech', 4) ?>"><span class="sprite skin_energie"></span></a>
		</td>
		<td width="16%">
			<a class="tooltip" data-tooltip-content='<center>Вместимость:<br><?=$parse['ak'] ?></center>'>
				<? if ($parse['energy_ak'] > 0 && $parse['energy_ak'] < 100): ?>
					<img src="/images/batt.php?p=<?=$parse['energy_ak'] ?>" width="42" alt="">
				<? else: ?>
					<span class="sprite skin_batt<?=$parse['energy_ak'] ?>"></span>
				<? endif; ?>
			</a>
		</td>
		<td width="16%">
			<a href="?set=infokredits" class="tooltip" data-tooltip-content='
			<table width=550>
			<tr>
			<? foreach ($parse['officiers'] AS $oId => $oTime): ?>
				<td align="center" width="14%">
					<?=_getText('tech', $oId) ?>
					<div class="separator"></div>
					<span class="officier of<?=$oId ?><?=($oTime > time() ? '_ikon' : '') ?>"></span>
				</td>
			<? endforeach; ?>
			</tr>
			<tr>
			<? foreach ($parse['officiers'] AS $oId => $oTime): ?>
				<td align="center">
				<? if ($oTime > time()): ?>
					Нанят до <font color=lime><?=datezone("d.m.Y H:i", $oTime) ?></font>
				<? else: ?>
					<font color=lime>Не нанят</font>
				<? endif; ?>
				</td>
			<? endforeach; ?>
			</tr></table>'><span class="sprite skin_kredits"></span></a>
		</td>
	</tr>
	<tr align="center">
		<? if (core::getConfig('showPlanetListSelect', 0) == 1): ?>
			<td rowspan="3">&nbsp;</td>
		<? endif; ?>
		<td><font color="#FFFF00">Металл</font></td>
		<td><font color="#FFFF00">Кристалл</font></td>
		<td><font color="#FFFF00">Дейтерий</font></td>
		<td><font color="#FFFF00">Энергия</font></td>
		<td><font color="#FFFF00">Заряд</font></td>
		<td><font color="#FFFF00">Кредиты</font></td>
	</tr>
	<tr align="center">
		<td title="Количество ресурса на планете">
			<div id="met">-</div>
		</td>
		<td title="Количество ресурса на планете">
			<div id="cry">-</div>
		</td>
		<td title="Количество ресурса на планете">
			<div id="deu">-</div>
		</td>
		<td title="Энергетический баланс"><?=$parse['energy_total'] ?></td>
		<td><?=$parse['energy_ak'] ?>%</td>
		<td><?=$parse['credits'] ?></td>
	</tr>
	<tr align="center">
		<td title="Максимальная вместимость хранилищ"><?=$parse['metal_max'] ?></td>
		<td title="Максимальная вместимость хранилищ"><?=$parse['crystal_max'] ?></td>
		<td title="Максимальная вместимость хранилищ"><?=$parse['deuterium_max'] ?></td>
		<td title="Выработка энергии"><font color="#00ff00"><?=$parse['energy_max'] ?></font></td>
		<td colspan="2"></td>
	</tr>
</table>
<script type="text/javascript">
	$("#met").html(format(<?=$parse['metal'] ?>));
	$("#cry").html(format(<?=$parse['crystal'] ?>));
	$("#deu").html(format(<?=$parse['deuterium'] ?>));
	$(document).ready(Res_count);
</script>