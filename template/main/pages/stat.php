<form method="post" action="?set=stat" id="stats">
	<input type="hidden" name="old_who" value="<?=$who ?>">
	<input type="hidden" name="old_type" value="<?=$type ?>">
	<table class="table">
		<tr>
			<td class="c" colspan="6">Статистика: <?=$parse['stat_date'] ?></td>
		</tr>
		<tr>
			<th width="15%" height="30">Какой</th>
			<th>
				<select name="who" onChange="$('#stats').submit()">
					<? foreach (_getText('who') AS $key => $value): ?>
						<option value="<?=$key ?>" <?=($key == $who ? 'selected' : '') ?>><?=$value ?></option>
					<? endforeach; ?>
				</select>
			</th>
			<th width="15%">по</th>
			<th>
				<select name="type" onChange="$('#stats').submit()">
					<? foreach (_getText('type') AS $key => $value): ?>
						<option value="<?=$key ?>" <?=($key == $type ? 'selected' : '') ?>><?=$value ?></option>
					<? endforeach; ?>
				</select>
			</th>
			<th width="15%">на месте</th>
			<th><select name="range" onChange="$('#stats').submit()"><?=$parse['range'] ?></select></th>
		<tr>
	</table>
</form>
<div class="separator"></div>
<table width="100%">