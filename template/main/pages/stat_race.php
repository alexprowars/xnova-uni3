<tr>
	<td class="c" width="35">Место</td>
	<td class="c" width="40">&nbsp;</td>
	<td class="c" width="25%">Игроков</td>
	<td class="c" width="30%">Очков</td>
	<td class="c" width="30%">Очки на игрока</td>
</tr>
<? if (count($stat) > 0): ?>
<? foreach ($stat AS $s): ?>
	<tr>
		<th><?=$s['player_rank'] ?></th>
		<th><img src="<?=DPATH ?>images/race<?=$s['player_race'] ?>.gif" width="35" height="35"></th>
		<th><?=$s['player_count'] ?></th>
		<th><?=$s['player_points'] ?></th>
		<th><?=$s['player_pointatuser'] ?></th>
	</tr>
	<? endforeach; ?>
<? endif; ?>
</table>