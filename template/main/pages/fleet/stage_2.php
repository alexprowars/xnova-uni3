<script type="text/javascript">

	var mission = 0;

	$(document).ready(function()
	{
		mission = $('input[name=mission]:checked').val();
		durationTime = duration() * 1000;

		durationTimer();

		$('.mission').hide();

		if ($('.mission.m_'+mission+'').length)
			$('.mission.m_'+mission+'').show();

		calculateTransportCapacity();

		$("select[name=holdingtime]").on('change', function()
		{
			var obj = $(this).val();

			if (obj <= 0)
				$('#stayRes').hide();
			else
			{
				var res = parseInt($('input[name=stayConsumption]').val()) * obj;

				$('#stayRes').html('<br>Потребуется <span class="positive">'+number_format(res, 0, ',', '.')+'</span> дейтерия').show();
			}

			calculateTransportCapacity();
		});

		$("input[name=mission]").on('change', function()
		{
			$('.mission').hide();

			mission = $(this).val();

			if ($('.mission.m_'+mission+'').length)
				$('.mission.m_'+mission+'').show();

			calculateTransportCapacity();
		});
	});
</script>