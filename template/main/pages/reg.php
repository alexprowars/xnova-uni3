<? if (isset($errors)): ?>
	<div class="error"><?=$errors ?></div>
<? endif; ?>
<form action="?set=reg&ajax&popup&xd" method="post" id="regForm" class="form">
	<table class="table">
		<tbody>
		<tr>
			<th width="293">Игровой ник</th>
			<th width="293"><input name="character" size="20" maxlength="20" type="text" value="<?=is($_POST, 'character') ?>"></th>
		</tr>
		<tr>
			<th>Пароль</th>
			<th><input name="passwrd" id="password" size="20" maxlength="20" type="password"></th>
		</tr>
		<tr>
			<th>Подтверждение пароля</th>
			<th><input name="сpasswrd" size="20" maxlength="20" type="password"></th>
		</tr>
		<tr>
			<th>E-Mail</th>
			<th><input name="email" size="20" maxlength="40" type="text" value="<?=is($_POST, 'email') ?>"></th>
		</tr>
		<tr>
			<th>Пол</th>
			<th>
				<select name="sex">
					<option value="" <?=(is($_POST, 'sex') == '' ? 'selected' : '') ?>>неизвестный</option>
					<option value="M" <?=(is($_POST, 'sex') == 'M' ? 'selected' : '') ?>>мужской</option>
					<option value="F" <?=(is($_POST, 'sex') == 'F' ? 'selected' : '') ?>>женский</option>
				</select>
			</th>
		</tr>
		<tr>
			<th><img src="/captcha.php?rnd=<?=mt_rand(0, 11111) ?>"></th>
			<th><input type="text" name="captcha" size="20" maxlength="20"/></th>
		</tr>
		<tr>
			<td height="20" colspan="2"></td>
		</tr>
		<tr>
			<th colspan=2 class="text-left">
				<input name="sogl" id="sogl" type="checkbox" <?=(is($_POST, 'sogl') ? 'checked' : '') ?>>
				<label for="sogl">Я принимаю</label> <a href="?set=sogl" target="_blank">Пользовательское соглашение</a>
			</th>
		</tr>
		<tr>
			<th colspan=2 class="text-left">
				<input name="rgt" id="rgt" type="checkbox" <?=(is($_POST, 'rgt') ? 'checked' : '') ?>>
				<label for="rgt">Я принимаю</label> <a href="?set=agb" target="_blank">Законы игры</a>
			</th>
		</tr>
		<tr>
			<th colspan=2><input name="submit" type="submit" value="Регистрация"></th>
		</tr>
	</table>
</form>
<script>
	$(document).ready(function()
	{
		$('#regForm').validate({
			submitHandler: function(form)
			{
				$(form).ajaxSubmit({
					target: '#windowDialog'
				});
			},
			focusInvalid: false,
			focusCleanup: true,
			rules:
			{
				'character': 'required',
				'passwrd': 'required',
				'сpasswrd': {required: true, 'equalTo': '#password'},
				'email': {required: true, email: true},
				'captcha': 'required'
			},
			messages:
			{
				'character': 'Введите ваш игровой ник',
				'passwrd': 'Введите пароль от игры',
				'сpasswrd': {required: 'Введите подтверждение пароля', equalTo: 'Пароли не совпадают'},
				'email': {required: 'Введите Email адрес', email: 'Введите корректный Email адрес'},
				'captcha': 'Введите проверочный код с картинки'
			}
		});
	});
</script>