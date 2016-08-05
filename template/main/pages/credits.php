<table class="table">
	<tr>
		<th align="center">
			<br>
			Для развития проекта Вы можете поддержать нас, получая кредиты по следующему курсу:<br><br>
			<center>
				1 кредит - 1 руб.
			</center>
			<br><br>
		</th>
	</tr>
</table>
<div class="separator"></div>
<table class="table">
	<tr>
		<td class="c" colspan="5"><b>Покупка кредитов</b></td>
	</tr>
	<tr>
		<th>
			<? if (!isset($_POST['OutSum'])): ?>
				<br><br>
				Ваш ID: <span class="neutral"><?=$userId ?></span>
				<br><br>


				<form action="?set=infokredits" method="POST">
					Введите ID игрока, на счет которого будут зачислены кредиты:
					<br>(если поле не заполнено, то кредиты поступят на ваш счет)
					<br>
					<input type="text" name="userId" value="">
					<br><br>
					Введите число желаемых кредитов:
					<br>
					<input type="text" name="OutSum" value="10">
					<br>
					<input type="submit" value="Купить">
				</form>

				<br><br>
			<? else: ?>
				<br>
				Счет сформирован. Нажмите кнопку "перейти к оплате" для продолжения процедуры покупки кредитов
				<br><br>

				<form class="noajax" action="https://auth.robokassa.ru/Merchant/Index.aspx" method="POST" target="_blank">
					<input type="hidden" name="MrchLogin" value="<?=SHOP_LOGIN ?>">
					<input type="hidden" name="InvDesc" value="Покупка кредитов">
					<input type="hidden" name="InvId" value="0">
					<input type="hidden" name="Email" value="<?=$useremail ?>">
					<input type="hidden" name="Shp_UID" value="<?=((isset($_POST['userId']) && is_numeric($_POST['userId']) && $_POST['userId'] > 0) ? intval($_POST['userId']) : $userid) ?>">
					<input type="hidden" name="SignatureValue" value="<?=md5(SHOP_LOGIN.":".intval($_POST['OutSum']).":0:".SHOP_MERCHANT.":Shp_UID=".((isset($_POST['userId']) && is_numeric($_POST['userId']) && $_POST['userId'] > 0) ? intval($_POST['userId']) : $userid)) ?>">
					<input type="hidden" name="Culture" value="RU">
					<input type="hidden" name="OutSum" value="<?=intval($_POST['OutSum']) ?>">
					<br>
					<input type="submit" value="Перейти к оплате">
				</form>

				<br><br>
				Счет выставлен для ID
				<span class="neutral"><?=((isset($_POST['userId']) && is_numeric($_POST['userId']) && $_POST['userId'] > 0) ? intval($_POST['userId']) : $userid) ?></span>

				<br><br>
			<? endif; ?>
		</th>
	</tr>
</table>