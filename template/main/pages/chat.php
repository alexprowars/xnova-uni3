<input type="hidden" name="message_id" id="message_id" value="1">

<table class="table">
	<tr>
		<th>
			<div id="shoutbox" class="shoutbox scrollbox"></div>
		</th>
	</tr>
	<tr>
		<th>
			<div id="editor"></div>
			<input name="msg" type="text" id="msg" style="width:95%" maxlength="750"><br>
			<input type="button" name="clear" value="Очистить" id="clear" onClick="ClearChat()">
			<input type="button" name="send" value="Отправить" id="send" onClick="addMessage()">
			<br>

			<div id="new_msg"></div>
		</th>
	</tr>
</table>
<script type="text/javascript" src="/scripts/chat.js"></script>
<script type="text/javascript">

var allowResize = <?=(core::getConfig('socialIframeView', 0) ? 0 : 1)?>;

$(document).ready(function()
{
	chatToolbar('msg');

	if (allowResize)
		setTimeout(chatResize, 1500);
});
</script>

<? if (isset($_GET['frame'])): ?>
<style>
	#box {
		width: 100%;
	}
</style>
<? endif; ?>