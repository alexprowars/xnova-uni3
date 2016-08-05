<? if ($msg): ?><?= $msg ?><? endif; ?>
<? if (!$isPopup): ?><br><? endif; ?>
<center>
<form action="?set=messages&mode=write&id=<?=$id ?>" method="post" <? if ($isPopup): ?>class="popup"<? endif; ?>>
	<table width="651">
		<? if (!$isPopup): ?>
		<tr>
			<td class="c" colspan="2">Отправка сообщения</td>
		</tr>
		<? endif; ?>
		<tr>
			<th>Получатель: <input type="text" name="to" id="to" size="55" value="<?=$to ?>"/></th>
		</tr>
		<tr>
			<th class="nopadding">
				<div id="editor"></div>
				<script type="text/javascript">edToolbar('text');</script>
				<textarea name="text" id="text" rows="15" onkeypress="if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) submit()"><?=$text ?></textarea></th>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Отправить"></th>
		</tr>
	</table>
	<div id="showpanel" style="display:none">
		<table align="center" width='651'>
			<tr>
				<td class="c"><b>Предварительный просмотр</b></td>
			</tr>
			<tr>
				<td class="b"><span id="showbox"></span></td>
			</tr>
		</table>
	</div>
</form>
</center>