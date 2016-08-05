<form action="?set=buildings&mode=<?=$parse['mode'] ?>" method="post">
	<table class="table">
		<tr>
			<td class="c" colspan="2" align="center"><input type="submit" value="Построить"></td>
		</tr>
		<? if (count($parse['buildlist']) > 0): ?>
		<? $i = 0;
		foreach ($parse['buildlist'] AS $build): $i++ ?>
			<?= (($i % 2 == 1) ? '<tr>' : '') ?>
			<td class="j" width="50%">
				<div class="viewport buildings <? if (!$build['access']): ?>shadow<? endif; ?>">
					<? if (!$build['access']): ?>
						<div class="notAvailable tooltip" data-tooltip-content="Требования:<br><?=str_replace('"', '\'', getTechTree($build['i'])) ?>" onclick="showWindow('<?=_getText('tech', $build['i']) ?>', '?set=infos&gid=<?=$build['i'] ?>&ajax&popup', 600)"><span>недоступно</span></div>
					<? endif; ?>

					<div class="img">
						<a href="javascript:;" onclick="showWindow('<?=_getText('tech', $build['i']) ?>', '?set=infos&gid=<?=$build['i'] ?>&ajax&popup', 600)">
							<img src="<?=DPATH ?>gebaeude/<?=$build['i'] ?>.gif" alt='<?=_getText('tech', $build['i']) ?>' align=top width=120 height=120 class="tooltip" data-tooltip-content='<center><?=_getText('descriptions', $build['i']) ?></center>' data-tooltip-width="150">
						</a>

						<div class="overContent">
							<?=$build['price'] ?>
						</div>
					</div>
					<div class="title">
						<a href=?set=infos&gid=<?=$build['i'] ?>><?=_getText('tech', $build['i']) ?></a> (<span class="<?=($build['count'] > 0 ? 'positive' : 'negative') ?>"><?=strings::pretty_number($build['count']) ?></span>)
					</div>
					<div class="actions">
						<? if ($build['access']): ?>
							Время: <?=strings::pretty_time($build['time']); ?>
							<? if ($build['add'] != ''): ?>
								<?=$build['add'] ?>
							<? else: ?>
								<br>
							<? endif; ?>
							<? if ($build['can_build']): ?>
								<? if ($build['maximum']): ?>
									<br>
									<center><font color="red">Вы можете построить только <?=$build['max'] ?> постройку данного типа</font></center>
								<? else: ?>
									<br>
									<a href=javascript:setMaximum(<?=$build['i'] ?>,<?=$build['max']?>);>Максимум: <font color="lime"><?=$build['max']?></font></a>
									<div class="buildmax">
										<input type=text name=fmenge[<?=$build['i'] ?>] alt='<?=_getText('tech', $build['i']) ?>' size="7" maxlength="5" value="" placeholder="0">
									</div>
								<? endif; ?>
							<? endif; ?>
						<? endif; ?>
					</div>
				</div>
			</td>
			<?= (($i % 2 == 0) ? '</tr>' : '') ?>
			<? endforeach; ?>
		<? if ($i % 2 == 1): ?>
			<th style="border-spacing:0;height:100%;width:100%">&nbsp;</th></tr>
			<? endif; ?>
		<? endif; ?>
		<tr>
			<td class="c" colspan="2" align="center"><input type="submit" value="Построить"></td>
		</tr>
	</table>
</form>