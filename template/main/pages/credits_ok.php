<table width="100%">
	<tr>
		<th align="center">
			<br>
			Для развития проекта Вы можете поддержать нас преобретая кредиты по следующему курсу:<br><br>
			<center>
				1 кредит - 1 ОК
			</center>
			<br><br>
		</th>
	</tr>
</table>
<div class="separator"></div>
<table width="100%">
	<tr>
		<td class="c" colspan="5"><b>Покупка кредитов</b></td>
	</tr>
	<tr>
		<? if (!core::getConfig('socialIframeView', 0)): ?>
			<th colspan="2" align="center">ПЕРЕЙДИТЕ НА САЙТ СОЦИАЛЬНОЙ СЕТИ ОДНОКЛАССНИКИ ДЛЯ ПОКУПКИ КРЕДИТОВ!</th>
		<? else: ?>
		<th width="50%" align="left">
			<br><br>Выберите пакет кредитов:
			<ul>
				<li>
					<div class="creditList">
						<span class="number">20</span> <img src="/images/officiers/smalcredts.gif" alt="" align="absmiddle"> <input type="button" onclick="buyCredits(20);" value="Купить">
					</div>
				</li>
				<li>
					<div class="creditList">
						<span class="number">60</span> <img src="/images/officiers/smalcredts.gif" alt="" align="absmiddle"> <input type="button" onclick="buyCredits(50);" value="Купить">
					</div>
				</li>
				<li>
					<div class="creditList">
						<span class="number">100</span> <img src="/images/officiers/smalcredts.gif" alt="" align="absmiddle"> <input type="button" onclick="buyCredits(100);" value="Купить">
					</div>
				</li>
				<li>
					<div class="creditList">
						<span class="number">200</span> <img src="/images/officiers/smalcredts.gif" alt="" align="absmiddle"> <input type="button" onclick="buyCredits(200);" value="Купить">
					</div>
				</li>
				<li>
					<div class="creditList">
						<span class="number">500</span> <img src="/images/officiers/smalcredts.gif" alt="" align="absmiddle"> <input type="button" onclick="buyCredits(500);" value="Купить">
					</div>
				</li>
			</ul>
			<br>
			Приобретая кредиты пакетами вы получаете бонус +10% от купленных кредитов
			<br><br>
		</th>
		<th>
			Введите число желаемых кредитов:
			<br><br><br>
			<input type="text" id="credits" value="10">
			<br>
			<input type="button" value="Купить" onclick="buyCredits($('#credits').val())">
		</th>
		<? endif; ?>
	</tr>
</table>

<?
	$params['application_key'] = $_SESSION['OKAPI']['application_key'];
	$params['format'] = 'JSON';
	$params['uid'] = $_SESSION['OKAPI']['logged_user_id'];
	$params['message'] = 'Я прошел обучение в Звёздной Империи. Теперь настало время воевать!';

	ksort($params);

	$sig = '';

	foreach($params as $k => $v)
		$sig .= $k.'='.$v;

	$sig .= APPSECRET;

	$params['sig'] = md5($sig);
?>

<!--<input type="button" onclick="FAPI.UI.showConfirmation('stream.publish', 'Я прошел обучение в Звёздной Империи. Теперь настало время воевать!', '<?=$params['sig'] ?>');" value="Show Confirmation">-->

<script type="text/javascript">
	function buyCredits(count)
	{
		if (isNaN(parseInt(count)))
			count = 10;

		showLoading();

		setTimeout(function(){hideLoading();}, 5000);

		FAPI.UI.showPayment(count+' кредитов', 'Кредиты нужны для оплаты дополнительных услуг в игре.', 'qCoins', count, null, '{"userId" : "<?=$userId ?>"}', 'ok', 'true');
	}

	//Callback function
	function API_callback(method, result, data)
	{
		if (method == 'showConfirmation' && result == 'ok')
		{
			$.ajax({
				'url' : '<?=$_SESSION['OKAPI']['api_server'] ?>api/stream/publish',
				'data': '<?=http_build_query($params) ?>&resig='+data,
				'success': function(data)
				{
					alert(data);
				}
			});
		}
	}
</script>