<div class="right">
	<div class="middle">
		<div class="text">
			<h1><?=core::getConfig('game_name') ?> - это браузерная игра в жанре космическая стратегия</h1>

			<p>Захватывающие битвы, множество альянсов, нескончаемый игровой мир, тысячи противников,
			-&nbsp;это неполный список того, что вам предстоит испытать на себе в космической стратегии XNova.</p>

			<p>Завоёвывайте планеты, покоряйте галактики, создайте нерушимый альянс сильнейших игроков!
			Сойдитесь в неравной космической битве со своими противниками, окунувшись в зрелищный и захватывающий мир XNova!</p>

			<div id="reg_button"><a href="javascript:;" onclick="showWindow('Регистрация', '?set=reg&ajax&xd', 500);">Регистрация</a></div>
		</div>
	</div>
</div>
<div class="left">
	<div class="middle">
		<div class="loginform">
			<div class="login">Вход в игру:</div>

			<div class="login-inputs">
				<div class="error" id="authError"></div>
				<form action="?set=login&xd" method="post" id="authForm">
					<div>
						<input class="input-text" name="emails" placeholder="Email" value="" type="text" />
						<input class="input-text" name="password" placeholder="Пароль" value="" type="password" />
						<input class="input-submit" type="submit" value="Вход" />
						<div class="remember">
							<input name="rememberme" id="rememberme" type="checkbox"><label for="rememberme">Запомнить?</label>
						</div>
					</div>
				</form>
			</div>
			<div class="lost-pass"><a id="lost-pass-link" href="javascript:;" onclick="showWindow('Восстановление пароля', '?set=lostpassword&ajax&xd', 400, 200);" title="Восстановление пароля">Забыли?</a></div>
			<div class="sm">
				Войти с помощью:
				<script type="text/javascript" src="http://u-login.com/js/ulogin.js"></script>
				<div id="uLogin" x-ulogin-params="display=small;fields=first_name,last_name,photo;providers=vkontakte,odnoklassniki,facebook,google,twitter,yandex,googleplus,mailru;redirect_uri=http%3A%2F%2F<?=$_SERVER['SERVER_NAME'] ?>%2F"></div>
			</div>
		</div>
	</div>
</div>
<div class="bottom">
	<div class="desk"><?=core::getConfig('game_name') ?> - многопользовательская онлайн-игра</div>
	<div class="nav">
		<a href="http://forum.xnova.su" title="Официальный форум" target="_blank">Форум</a>  |
		<a href="/xnsim/">Симулятор</a>  |  <a href="?set=stat">Статистика</a>  |
		<a href="http://vkontakte.ru/xnova_game" title="Официальная группа ВКонтакте" target="_blank">ВКонтакте</a>  |
		<a href="?set=agb">Правила</a>  |
		<a href="?set=banned">Блокировки</a>  |
		<a href="?set=contact">Администрация</a>
	</div>
	<div class="copy"><?=$parse['online_users'] ?> / <?=$parse['users_amount'] ?>&nbsp;&nbsp;&nbsp;&copy; <?=date("Y") ?> XNOVA.SU</div>
</div>
<div id="mask"></div>

<script type="text/javascript">
	$(document).ready(function()
	{
		$('#authForm').ajaxForm({
			url: '?set=login&xd&popup&ajax',
			beforeSubmit: function()
			{
				showLoading();
			},
			success: function(data)
			{
				$('#authError').html(data);
				hideLoading();
			}
		});
	});
</script>