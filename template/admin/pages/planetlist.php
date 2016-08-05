<div class="table-responsive">
	<table class="table table-striped table-hover table-advance">
		<thead>
			<tr>
				<th width="50">ID</th>
				<th>Название планеты</th>
				<th>Галактика</th>
				<th>Система</th>
				<th>Планета</th>
				<th>Переход</th>
			</tr>
		</thead>
		<? foreach ($planetlist AS $planet): ?>
			<tr>
				<td class="b center"><?=$planet['id'] ?></td>
				<td class="b center"><?=$planet['name'] ?></td>
				<td class="b center"><?=$planet['galaxy'] ?></td>
				<td class="b center"><?=$planet['system'] ?></td>
				<td class="b center"><?=$planet['planet'] ?></td>
				<td class="b center"><?=BuildPlanetAdressLink($planet) ?></td>
			</tr>
		<? endforeach; ?>
	</table>
</div>
<div class="row">
	<div class="col-md-5 col-sm-12">
		<div class="dataTables_info">
			В игре <b><?=$all ?></b> планет<?=strings::morph($all, 'feminine', 5) ?>
		</div>
	</div>
	<div class="col-md-7 col-sm-12">
		<div class="dataTables_paginate paging_bootstrap">
			<?=$pagination ?>
		</div>
	</div>
</div>