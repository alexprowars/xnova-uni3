<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?=TEMPLATE_PATH ?>/assets/plugins/flot/jquery.flot.js"></script>
<script src="<?=TEMPLATE_PATH ?>/assets/plugins/flot/jquery.flot.time.js"></script>
<!-- END PAGE LEVEL PLUGINS -->

<? for ($i = 0; $i < 4; $i++): $time = time() - 21600 * ($i + 1); ?>
	<div class="portlet box red">
		<div class="portlet-title">
			<div class="caption">
				Загрузка сервера (<?=date("d.m.Y H:i", (time() - 21600 * ($i + 1))) ?> - <?=date("d.m.Y H:i", (time() - 21600 * $i)) ?>)
			</div>
		</div>
		<div class="portlet-body">
			<div id="chart_<?=$i ?>" class="chart"></div>
		</div>
	</div>
<? endfor; ?>

<script type="text/javascript">
	<? for ($i = 0; $i < 4; $i++): $time = time() - 21600 * ($i + 1); ?>
		var load_<?=$i ?> = [
			<? $j = 0; foreach ($parse['rows'] AS $row): if ($row['TIME'] < $time || $row['TIME'] > ($time + 21600)) continue; ?>
				<?=($j > 0 ? ',' : '') ?>["<?=$row['TIME'] ?>000", <?=$row['LOAD'][0] ?>]
			<? $j++; endforeach; ?>
		];
	<? endfor; ?>

	var config =
	{
		series: {
			lines: {
				show: true,
				lineWidth: 1,
				fill: true,
				fillColor: {
					colors: [
						{
							opacity: 0.05
						},
						{
							opacity: 0.01
						}
					]
				}
			},
			points: {
				show: false
			},
			shadowSize: 2
		},
		grid: {
			hoverable: true,
			tickColor: "#eee",
			borderWidth: 0
		},
		colors: ["#d12610", "#37b7f3", "#52e136"],
		xaxis: {
			mode: "time",
			timezone: "browser",
			tickLength: 0
		},
		yaxis: {
			ticks: 11,
			tickDecimals: 2
		}
	};

	$(document).ready(function ()
	{
		<? for ($i = 0; $i < 4; $i++): ?>
			var plot<?=$i ?> = $.plot("#chart_<?=$i ?>", [load_<?=$i ?>], config);
		<? endfor; ?>

		function showTooltip(x, y, contents)
		{
			$('<div id="tooltip">' + contents + '</div>').css({
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 15,
				border: '1px solid #333',
				padding: '4px',
				color: '#fff',
				'border-radius': '3px',
				'background-color': '#333',
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		}

		var previousPoint = null;
		$(".chart").bind("plothover", function (event, pos, item)
		{
			$("#x").text(pos.x.toFixed(2));
			$("#y").text(pos.y.toFixed(2));

			if (item)
			{
				if (previousPoint != item.dataIndex)
				{
					previousPoint = item.dataIndex;

					$("#tooltip").remove();
					var y = item.datapoint[1].toFixed(2);

					showTooltip(item.pageX, item.pageY, y);
				}
			}
			else
			{
				$("#tooltip").remove();
				previousPoint = null;
			}
		});
	});

</script>