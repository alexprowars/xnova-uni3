<tr>
	<td class="c" width="30">Место</td>
	<td class="c" width="30">+/-</td>
	<td class="c">Альянс</td>
	<td class="c">Игроки</td>
	<td class="c">Очки</td>
	<td class="c">Очки на игрока</td>
</tr>
<? foreach ($stat AS $s): ?>
<tr>
	<th><?=$s['ally_rank'] ?></th>
	<th><?=$s['ally_rankplus'] ?></th>
	<th><?=$s['ally_name'] ?></th>
	<th><?=$s['ally_members'] ?></th>
	<th><?=$s['ally_points'] ?></th>
	<th><?=$s['ally_members_points'] ?></th>
</tr>
<? endforeach; ?>
</table>