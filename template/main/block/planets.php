<? if (count($parse['list'])): ?>
	<td class="planetList">
			<div class="list">

			<? foreach ($parse['list'] AS $i => $planet): ?>
				<div class="planet type_<?=$planet['planet_type'] ?> <?=($parse['current'] == $planet['id'] ? 'current' : '') ?>">
					<a href="javascript:;" onclick="changePlanet(<?=$planet['id'] ?>)" title="<?=$planet['name'] ?>">
						<img src="<?=DPATH ?>planeten/small/s_<?=$planet['image'] ?>.jpg" height="40" width="40" alt="<?=$planet['name'] ?>">
					</a>
					<div>
						<?=$planet['name'] ?>
						<br>
						<?=BuildPlanetAdressLink($planet) ?>
					</div>
					<div class="clear"></div>
				</div>
			<? endforeach; ?>

		</div>
		<? if ($pageParams['ajaxNavigation'] != 0): ?>
			<script type="text/javascript">
				$(document).ready(function()
				{
					$('.planetList .list').on('mouseup', 'a', function()
					{
						$('.planetList .planet').removeClass('current');
						$(this).parents('.planet').addClass('current');
					});
				});
			</script>
		<? endif; ?>
	</td>
<? endif; ?>