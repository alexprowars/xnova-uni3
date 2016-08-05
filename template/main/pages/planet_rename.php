<form action="?set=overview&mode=renameplanet&pl=<?=$parse['planet_id'] ?>" method="POST">
	<table class="table">
		<tr>
			<td class="c" colspan=3>Переименовать или покинуть планету</td>
		</tr>
		<? if (!$isPopup): ?>
			<tr>
				<th><?=$parse['galaxy_galaxy'] ?>:<?=$parse['galaxy_system'] ?>:<?=$parse['galaxy_planet'] ?></th>
				<th><?=$parse['planet_name'] ?></th>
				<th><input type="submit" name="action" value="Покинуть колонию" alt="Покинуть колонию"></th>
			</tr>
		<? endif; ?>
		<tr>
			<th>Сменить название</th>
			<th><input type="text" placeholder="<?=$parse['planet_name'] ?>" name="newname" size=25 maxlength=20></th>
			<th><input type="submit" name="action" value="Сменить название"></th>
		</tr>
	</table>
</form>
<? if ($parse['type'] != ''): ?>
	<div class="separator"></div>
	<form action="?set=overview&mode=renameplanet&pl=<?=$parse['planet_id'] ?>" method="POST">
		<table class="table">
			<tr>
				<td class="c">Сменить фон планеты</td>
			</tr>
			<tr>
				<th>
					<div class="separator"></div>
					<? for ($i = 1; $i <= $parse['images'][$parse['type']]; $i++): ?>
						<? if ($i%5 == 1 && $i != 1): ?><div class="separator"></div><? endif; ?>
						<input type="radio" name="image" value="<?=$i ?>" id="image_<?=$i ?>">
						<label for="image_<?=$i ?>"><img src="<?=DPATH ?>planeten/small/s_<?=$parse['type'] ?>planet<?=($i < 10 ? '0' : '').$i ?>.jpg" align="absmiddle"></label>
					<? endfor; ?>
					<div class="separator"></div>
				</th>
			</tr>
			<tr>
				<th>
					<input type="submit" name="action" value="Сменить картинку (1 кредит)">
				</th>
			</tr>
		</table>
	</form>
<? endif; ?>