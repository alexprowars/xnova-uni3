<div class="portlet box green">
	<div class="portlet-title">
		<div class="caption">Начисление кредитов на счет</div>
	</div>
	<div class="portlet-body form">
		<form action="/admin/mode/money/action/add/" method="post" class="form-horizontal form-bordered">
			<div class="form-body">
				<div class="form-group">
					<label class="col-md-3 control-label">Логин или ID игрока</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="username">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Сумма</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="money">
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn green">Начислить</button>
				</div>
			</div>
		</form>
	</div>
</div>