<? if ($parse['bonus']): ?>
	<table class="table">
		<tr>
			<td class="c" colspan="4">Ежедневный бонус</td>
		</tr>
		<tr>
			<th colspan="4">
				Сейчас вы можете получить по <b><?=($parse['bonus_multi'] * 500 * system::getGameSpeed()) ?></b> Металла, Кристаллов и Дейтерия.<br>
				Каждый день размер бонуса будет увеличиваться.<br>
				<br>
				<a href="?set=overview&mode=bonus">ПОЛУЧИТЬ БОНУС</a><br>
			</th>
		</tr>
	</table>
	<div class="separator"></div>
<? endif; ?>

<div class="block">
	<div class="title">
		<?=_getText('type_planet', $parse['planet_type']) ?> "<?=$parse['planet_name'] ?>"
		<a href="?set=galaxy&r=0&galaxy=<?=$parse['galaxy_galaxy'] ?>&system=<?=$parse['galaxy_system'] ?>">[<?=$parse['galaxy_galaxy'] ?>:<?=$parse['galaxy_system'] ?>:<?=$parse['galaxy_planet'] ?>]</a>
		<a href="?set=overview&mode=renameplanet" title="Редактирование планеты">(изменить)</a>

		<div id="clock" style="float:right;"><?=datezone("d-m-Y H:i:s", time()) ?></div>
		<script type="text/javascript">UpdateClock();</script>
	</div>
	<div class="content">
		<table class="table table-noborder">
			<? if (count($parse['fleet_list']) > 0): ?>
				<tr>
					<th colspan="4">
						<table class="table">
							<? foreach ($parse['fleet_list'] as $id => $list): ?>
								<tr class="<?=$list['fleet_status'] ?>">
								<th width="80">
									<div id="bxx<?=$list['fleet_order'] ?>" class="z"><?=$list['fleet_count_time'] ?></div>
									<font color="lime"><?=$list['fleet_time'] ?></font>
								</th>
								<th class="textLeft" colspan="3">
									<span class="<?=$list['fleet_status'] ?> <?=$list['fleet_prefix'] ?><?=$list['fleet_style'] ?>"><?=$list['fleet_descr'] ?></span>
								</th>
								<?= $list['fleet_javas'] ?>
								</tr>
							<? endforeach; ?>
						</table>
					</th>
				</tr>
			<? endif; ?>
			<?=$parse['Have_new_level'] ?>
			<? $m = core::getConfig('newsMessage', ''); ?>
			<? if ($m != ''): ?>
				<tr>
					<th><img src="<?=DPATH ?>img/warning.png" align="absmiddle" alt=""></th>
					<th colspan="3">
						<?=$m ?>
					</th>
				</tr>
			<? endif; ?>
			<tr>
				<th width="26%" valign="top">
					<div class="planet-image">
						<a href="?set=overview&mode=renameplanet" style="background-image: url(<?=DPATH ?>planeten/<?=$parse['planet_image'] ?>.jpg)"></a>
						<? if ($parse['moon_img'] != ''): ?>
							<div class="moon-image"><?=$parse['moon_img'] ?></div>
						<? endif; ?>
					</div>

					<div class="separator"></div>

					<div style="border: 1px solid rgb(153, 153, 255); width: 200px; margin: 0 auto;">
						<div id="CaseBarre" style="background-color: <?=$parse['case_barre_barcolor'] ?>; width: <?=$parse['case_pourcentage'] ?>%;  margin: 0 auto; text-align:center;">
							<font color="#000000"><b><?=$parse['case_pourcentage'] ?>%</b></font></div>
					</div>

					<? if (core::getConfig('noob', 0) == 1): ?>
						<div class="separator"></div>
						<img src="<?=DPATH ?>img/warning.png" align="absmiddle" alt="">
						<span style="font-weight:normal;"><span class="positive">Активен режим ускорения новичков.</span><br>Режим будет деактивирован после достижения 1000 очков.</span>
					<? endif; ?>
				</th>
				<th valign="top" width="7%">
					<table class="table">
						<? foreach ($parse['officiers'] AS $oId => $oTime): ?>
							<tr>
								<td align="center">
									<a href="?set=officier" class="tooltip" data-tooltip-content="<?=_getText('tech', $oId) ?><br><? if ($oTime > time()): ?>Нанят до <font color=lime><?=datezone("d.m.Y H:i", $oTime) ?></font><? else: ?><font color=lime>Не нанят</font><? endif; ?>"><span class="officier of<?=$oId ?><?=($oTime > time() ? '_ikon' : '') ?>"></span></a>
								</td>
							</tr>
						<? endforeach; ?>
					</table>
				</th>
				<th valign="top" width="33%">
					<table class="table">
						<tr>
							<td class="c" colspan="2">Диаметр</td>
						</tr>
						<tr>
							<th colspan="2"><?=strings::pretty_number($parse['planet_diameter']) ?> км</th>
						</tr>
						<tr>
							<td class="c" colspan="2">Занятость</td>
						</tr>
						<tr>
							<th colspan="2"><a title="Занятость полей"><?=$parse['planet_field_current'] ?></a> / <a title="Максимальное количество полей"><?=$parse['planet_field_max'] ?></a> поля</th>
						</tr>
						<tr>
							<td class="c" colspan="2">Температура</td>
						</tr>
						<tr>
							<th colspan="2">от. <?=$parse['planet_temp_min'] ?>&deg;C до <?=$parse['planet_temp_max'] ?>&deg;C</th>
						</tr>
						<tr>
							<td class="c" colspan="2">Обломки <?=$parse['get_link'] ?></td>
						</tr>
						<tr>
							<th colspan="2">
								<img src="/skins/default/images/s_metall.png" alt="" align="absmiddle" class="tooltip" data-tooltip-content="Металл">
								<?=strings::pretty_number($parse['metal_debris']) ?>
								/
								<img src="/skins/default/images/s_kristall.png" alt="" align="absmiddle" class="tooltip" data-tooltip-content="Кристалл">
								<?=strings::pretty_number($parse['crystal_debris']) ?>
							</th>
						</tr>
						<tr>
							<td class="c" colspan="2">Бои</td>
						</tr>
						<tr>
							<th colspan="2">
								<img src="/images/wins.gif" alt="" align="absmiddle" class="tooltip" data-tooltip-content="Победы">
								<?=$parse['raids_win'] ?>
								+
								<img src="/images/losses.gif" alt="" align="absmiddle" class="tooltip" data-tooltip-content="Поражения">
								<?=$parse['raids_lose'] ?>
								=
								<?=$parse['raids'] ?>
							</th>
						</tr>
						<tr>
							<th colspan="2">Фракция: <a href="?set=race"><?=$parse['race'] ?></a></th>
						</tr>
						<tr>
							<th colspan="2"><a href="?set=refers"><? if (SERVER_CODE == 'OK1U'): ?>Рефералы<? else: ?>http://<?=$_SERVER['HTTP_HOST'] ?>/?<?=$parse['user_id'] ?><? endif; ?></a> [<?=$parse['links'] ?>]</th>
						</tr>
					</table>
				</th>
				<th class="s" valign="top" width="33%">
					<table class="table">
						<tr>
							<td class="c" width="40%">Игрок:</td>
							<td class="c" ><div style="overflow: hidden;white-space:nowrap;width:115px;"><a onclick="showWindow('<?=$parse['user_username'] ?>', '?set=players&id=<?=$parse['user_id'] ?>&ajax&popup', 510);"><?=$parse['user_username'] ?></a></div></td>
						</tr>
						<tr>
							<th width="90">Постройки:</th>
							<th><span class="positive"><?=strings::pretty_number($parse['user_points']) ?></span></th>
						</tr>
						<tr>
							<th width="90">Флот:</th>
							<th><span class="positive"><?=strings::pretty_number($parse['user_fleet']) ?></span></th>
						</tr>
						<tr>
							<th width="90">Оборона:</th>
							<th><span class="positive"><?=strings::pretty_number($parse['user_defs']) ?></span></th>
						</tr>
						<tr>
							<th width="90">Наука:</th>
							<th><span class="positive"><?=strings::pretty_number($parse['player_points_tech']) ?></span></th>
						</tr>
						<tr>
							<th width="90">Всего:</th>
							<th><span class="positive"><?=strings::pretty_number($parse['total_points']) ?></span></th>
						</tr>
						<tr>
							<th width="90">Место:</th>
							<th><a href="?set=stat&range=<?=$parse['user_rank'] ?>"><?=$parse['user_rank'] ?></a> <span title="Изменение места в рейтинге">(<?=$parse['ile'] ?>)</span></th>
						</tr>
						<tr>
							<td class="c" colspan="2">Промышленный уровень:</td>
						</tr>
						<tr>
							<th colspan="2"><?=$parse['lvl_minier'] ?> из 100</th>
						</tr>
						<tr>
							<th colspan="2"><?=strings::pretty_number($parse['xpminier']) ?> / <?=strings::pretty_number($parse['lvl_up_minier']) ?> exp</th>
						</tr>
						<tr>
							<td class="c" colspan="2">Военный уровень:</td>
						</tr>
						<tr>
							<th colspan="2"><?=$parse['lvl_raid'] ?> из 100</th>
						</tr>
						<tr>
							<th colspan="2"><?=strings::pretty_number($parse['xpraid']) ?> / <?=strings::pretty_number($parse['lvl_up_raid']) ?> exp</th>
						</tr>
					</table>
				</th>
			</tr>
		</table>
	</div>
</div>

<? if (isset($parse['build_list']) && is_array($parse['build_list']) && count($parse['build_list']) > 0): ?>
	<table class="table">
		<? foreach ($parse['build_list'] as $id => $list): ?>
			<tr class="flight">
				<th width="80" <?=($id == 0 ? 'style="border-top:0;"' : '')?>>
					<div id="build<?=$id ?>" class="z"><?=($list[0] - time()) ?></div>
					<script type="text/javascript">FlotenTime('build<?=$id ?>', <?=($list[0] - time()) ?>);</script>
				</th>
				<th colspan="3" style="text-align:left;<?=($id == 0 ? 'border-top:0;' : '')?>">
					<span style="float:left;"><span class="flight owndeploy"><?=$list[1] ?></span></span>
					<font color="lime" style="float:right;"><?=datezone("d.m H:i:s", $list[0]) ?></font>
				</th>
			</tr>
		<? endforeach; ?>
	</table>
<? endif; ?>

<? if (is_array($parse['anothers_planets']) && count($parse['anothers_planets'])): ?>
	<div class="separator"></div>
	<table class="table anotherPlanets">
		<tr>
			<? foreach ($parse['anothers_planets'] AS $i => $UserPlanet): ?>
				<th width="17%" valign="top">
					<a href="?set=overview&amp;cp=<?= $UserPlanet['id'] ?>&amp;re=0" title="<?= $UserPlanet['name'] ?>"><img src="<?=DPATH ?>planeten/small/s_<?= $UserPlanet['image'] ?>.jpg" height="50" width="50" alt=""></a>
					<br><?=$UserPlanet['name'] ?>
				</th>
				<? if ($i%6 == 0): ?></tr><tr><? endif; ?>
			<? endforeach; ?>
		</tr>
	</table>
<? endif; ?>

<? if (isset($parse['activity'])): ?>
	<div class="separator"></div>

	<div id="tabs">
		<div class="head">
			<ul>
				<li><a href="#tabs-0">Чат</a></li>
				<li><a href="#tabs-1">Форум</a></li>
			</ul>
		</div>
		<div id="tabs-0">
			<table class="table">
				<tr>
					<th class="left">
						<div style="max-height: 150px;overflow-y: auto;">
						<? foreach ($parse['activity']['chat'] AS $activity): ?>
							<div class="activity"><div class="date1" style="float:left;padding-right:5px;"><?=date("H:i", $activity['TIME']) ?></div><div style="float:left;width:570px;"><?=$activity['MESS'] ?></div></div>
							<div class="clear"></div>
						<? endforeach; ?>
						</div>
					</th>
				</tr>
			</table>
		</div>
		<div id="tabs-1">
			<table class="table">
				<tr>
					<th class="left">
						<div style="max-height: 150px;overflow-y: auto;">
						<? foreach ($parse['activity']['forum'] AS $activity): ?>
							<div class="activity"><div class="date1" style="float:left;padding-right:5px;"><?=date("H:i", $activity['TIME']) ?></div><div style="float:left;width:570px;"><?=$activity['MESS'] ?></div></div>
							<div class="clear"></div>
						<? endforeach; ?>
						</div>
					</th>
				</tr>
			</table>
		</div>
	</div>
	<script type="text/javascript">
	$(function()
	{
	  	$( "#tabs" ).tabs();
	});
	</script>

<? endif; ?>