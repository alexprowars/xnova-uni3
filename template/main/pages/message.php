<center>
	<table width="600">
		<tr>
			<td class="c"><font color="red"><?=$title ?></font></td>
		</tr>
		<tr>
			<th class="errormessage"><?=$text ?></th>
		</tr>
	</table>
</center>
<? if ($time && $destination): ?>
	<script type="text/javascript">
	<? if ($isAjax): ?>
		timeouts['message'] = setTimeout(function(){load('<?=$destination ?>')}, <?=($time * 1000) ?>);
	<? else: ?>
		setTimeout(function(){location.href = '<?=$destination ?>';}, <?=($time * 1000) ?>);
	<? endif; ?>
	</script>
<? endif; ?>