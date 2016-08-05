<div id="tabs">
	<div class="head">
		<ul>
			<? foreach ($parse AS $i => $list): ?>
				<? if (!isset($list['required_list'])): ?>
					<li><a href="#tabs-<?=$i ?>"><?=$list['tt_name'] ?></a></li>
				<? endif; ?>
			<? endforeach; ?>
		</ul>
	</div>
	<div id="tabs-0">
	<? foreach ($parse AS $i => $list): if ($i == 0) continue; ?>
		<? if (!isset($list['required_list'])): ?>
			</div><div id="tabs-<?=$i ?>">
		<? else: ?>
			<div class="row">
				<div class="column inline five left middle">
					<a href="?set=infos&gid=<?=$list['tt_info'] ?>"><?=$list['tt_name'] ?></a>

					<? if ($list['required_list'] != ''): ?>
						<div style="float:right"><a href="?set=techtree&id=<?=$list['tt_info'] ?>">[i]</a></div>
					<? endif; ?>
				</div><div class="column inline seven left">
					<?=$list['required_list'] ?>
				</div>
			</div>
		<? endif; ?>
	<? endforeach; ?>
	</div>
</div>

<script type="text/javascript">
$(function()
{
  	$( "#tabs" ).tabs();
	$("#tabs .row:even").addClass("odd");
});
</script>