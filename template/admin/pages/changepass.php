<div class="portlet box green">
	<div class="portlet-title">
		<div class="caption">Смена пароля</div>
	</div>
	<div class="portlet-body form">
		<form action="/admin/mode/md5changepass/" method="post" class="form-horizontal form-bordered">
			<div class="form-body">
				<div class="form-group">
					<label class="col-md-3 control-label">Логин игрока</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="username">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Пароль</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="password">
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn green">Сменить</button>
				</div>
			</div>
		</form>
	</div>
</div>