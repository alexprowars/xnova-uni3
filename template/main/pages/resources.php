<form action="?set=resources" method="post">

	<table width="100%">
		<tr>
			<td class="c" align="center">Уровень производства</td>
			<th><?=$parse['production_level'] ?></th>
			<th width="350">
				<div style="border: 1px solid rgb(153, 153, 255); width: 350px;">
					<div id="prodBar" style="background-color: <?=$parse['production_level_barcolor'] ?>; width: <?=$parse['production_level_bar'] ?>px;">
						&nbsp;
					</div>
				</div>
			</th>
		</tr>
		<tr>
			<td class="c" align="center"><a href="?set=infos&gid=113">Энергетическая технология</a></td>
			<th><?=$parse['et'] ?> ур.</th>
		</tr>
	</table>
	<br>

	<table width="100%">
		<tr>
			<td class="c" colspan="8">Производство на планете <?=$parse['name'] ?></td>
		</tr>
		<tr>
			<th width="200"></th>
			<th>Ур.</th>
			<th>Бонус</th>
			<th><a href="javascript:;" onclick="showWindow('<?=_getText('tech', 1) ?>', '?set=infos&gid=1&ajax&popup', 600)">Металл</a></th>
			<th><a href="javascript:;" onclick="showWindow('<?=_getText('tech', 2) ?>', '?set=infos&gid=2&ajax&popup', 600)">Кристалл</a></th>
			<th><a href="javascript:;" onclick="showWindow('<?=_getText('tech', 3) ?>', '?set=infos&gid=3&ajax&popup', 600)">Дейтерий</a></th>
			<th><a href="javascript:;" onclick="showWindow('<?=_getText('tech', 4) ?>', '?set=infos&gid=4&ajax&popup', 600)">Энергия</a></th>
			<th width="100">КПД</th>
		</tr>
		<tr>
			<th align="left" style="text-align:left;">&nbsp;Естесственное производство</th>
			<td class="k">-</td>
			<td class="k">-</td>
			<td class="k"><?=$parse['metal_basic_income'] ?></td>
			<td class="k"><?=$parse['crystal_basic_income'] ?></td>
			<td class="k"><?=$parse['deuterium_basic_income'] ?></td>
			<td class="k"><?=$parse['energy_basic_income'] ?></td>
			<td class="k">100%</td>
		</tr>
		<? foreach ($parse['resource_row'] as $res): ?>
            <tr>
                <th height="22" align="left" style="text-align:left;">&nbsp;<a href="javascript:;" onclick="showWindow('<?=_getText('tech', $res['id']) ?>', '?set=infos&gid=<?=$res['id'] ?>&ajax&popup', 600)"><?=_getText('tech', $res['id']) ?></a></th>
                <th><font color="#ffffff"><?=$res['level_type'] ?></font></th>
                <th><font color="#ffffff"><?=$res['bonus'] ?>%</font></th>
                <th><font color="#ffffff"><?=strings::colorNumber(strings::pretty_number($res['metal_type'])) ?></font></th>
                <th><font color="#ffffff"><?=strings::colorNumber(strings::pretty_number($res['crystal_type'])) ?></font></th>
                <th><font color="#ffffff"><?=strings::colorNumber(strings::pretty_number($res['deuterium_type'])) ?></font></th>
                <th><font color="#ffffff"><?=strings::colorNumber(strings::pretty_number($res['energy_type'])) ?></font></th>
                <th>
                    <select name="<?=$res['name'] ?>">
                    <? for ($Option = 10; $Option >= 0; $Option--): ?>
                        <option value="<?=$Option ?>"<?=($Option == $res['porcent'] ? ' selected=selected' : '') ?>><?=($Option * 10) ?>%</option>
                    <? endfor; ?>
                    </select>
                </th>
            </tr>
		<? endforeach; ?>
		<tr>
		</tr>
		<tr>
			<th colspan="2">Вместимость:</th>
			<th><?=$parse['bonus_h'] ?>%</th>
			<td class="k"><?=$parse['metal_max'] ?></td>
			<td class="k"><?=$parse['crystal_max'] ?></td>
			<td class="k"><?=$parse['deuterium_max'] ?></td>
			<td class="k"><font color="#00ff00"><?=$parse['energy_max'] ?></font></td>
			<td class="k"><input name="action" value="Пересчитать" type="submit"></td>
		</tr>
		<tr>
			<th colspan="3">Сумма:</th>
			<td class="k"><?=$parse['metal_total'] ?></td>
			<td class="k"><?=$parse['crystal_total'] ?></td>
			<td class="k"><?=$parse['deuterium_total'] ?></td>
			<td class="k"><?=$parse['energy_total'] ?></td>
		</tr>
	</table>

	<br>
	<table width="100%">
		<tr>
			<td class="c" colspan="5">Информация о производстве</td>
		</tr>
		<tr>
			<th width="16%">&nbsp;</th>
			<th width="21%">Час</th>
			<th width="21%">День</th>
			<th width="21%">Неделя</th>
			<th width="21%">Месяц</th>
		</tr>
		<tr>
			<th>Металл</th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['metal_total'])) ?></th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['metal_total'] * 24)) ?></th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['metal_total'] * 24 * 7)) ?></th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['metal_total'] * 24 * 30)) ?></th>
		</tr>
		<tr>
			<th>Кристалл</th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['crystal_total'])) ?></th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['crystal_total'] * 24)) ?></th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['crystal_total'] * 24 * 7)) ?></th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['crystal_total'] * 24 * 30)) ?></th>
		</tr>
		<tr>
			<th>Дейтерий</th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['deuterium_total'])) ?></th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['deuterium_total'] * 24)) ?></th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['deuterium_total'] * 24 * 7)) ?></th>
			<th><?=strings::colorNumber(strings::pretty_number($parse['deuterium_total'] * 24 * 30)) ?></th>
		</tr>
	</table>

	<br>
	<table width="100%">
		<tr>
			<td class="c" colspan="5">Управление производством</td>
		</tr>
		<tr>
			<th width="50%"><a href="?set=resources&production_full=1" class="button">Включить на всех планетах</a></th>
			<th><a href="?set=resources&production_empty=1" class="button">Выключить на всех планетах</a></th>
		</tr>
	</table>
	<br>

	<table width="100%">
		<tr>
			<td class="c" colspan="3">Статус хранилища</td>
		</tr>
		<tr>
			<th width="200">Металл</th>
			<th width="100"><?=$parse['metal_storage'] ?>%</th>
			<th>
				<div style="border: 1px solid rgb(153, 153, 255); width: 425px;">
					<div id="AlmMBar" style="background-color: <?=$parse['metal_storage_barcolor'] ?>; width: <?=$parse['metal_storage_bar'] ?>px;">
						&nbsp;
					</div>
				</div>
			</th>
		</tr>
		<tr>
			<th>Кристалл</th>
			<th><?=$parse['crystal_storage'] ?>%</th>
			<th width="250">
				<div style="border: 1px solid rgb(153, 153, 255); width: 425px;">
					<div id="AlmCBar" style="background-color: <?=$parse['crystal_storage_barcolor'] ?>; width: <?=$parse['crystal_storage_bar'] ?>px; opacity: 0.98;">
						&nbsp;
					</div>
				</div>
			</th>
		</tr>
		<tr>
			<th>Дейтерий</th>
			<th><?=$parse['deuterium_storage'] ?>%</th>
			<th width="250">
				<div style="border: 1px solid rgb(153, 153, 255); width: 425px;">
					<div id="AlmDBar" style="background-color: <?=$parse['deuterium_storage_barcolor'] ?>; width: <?=$parse['deuterium_storage_bar'] ?>px;">
						&nbsp;
					</div>
				</div>
			</th>
		</tr>
	</table>
</form>
<table width="100%">
	<tr>
		<td class="c" colspan="5">Покупка ресурсов (8 ч. выработка ресурсов)</td>
	</tr>
	<tr>
		<th width="30%">
			<? if ($parse['merchand'] < time()): ?>
				<a href="?set=resources&buy=1" class="button">Купить за 10 кредитов</a>
			<? else: ?>
				Через <?= strings::pretty_time($parse['merchand'] - time()) ?>
			<? endif; ?>
		</th>
		<th>Вы можете купить: <?=$parse['buy_metal'] ?> металла, <?=$parse['buy_crystal'] ?> кристалла, <?=$parse['buy_deuterium'] ?> дейтерия</th>
	</tr>
</table>
