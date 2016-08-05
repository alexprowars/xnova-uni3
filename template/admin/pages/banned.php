<div class="portlet box red">
	<div class="portlet-title">
		<div class="caption">Банилка</div>
	</div>
	<div class="portlet-body form">
		<form action="/admin/mode/banned/" method="post" class="form-horizontal form-bordered">
			<input type="hidden" name="modes" value="banit">
			<div class="form-body">
				<div class="form-group">
					<label class="col-md-3 control-label">Логин</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="name">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Причина</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="why">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Время бана</label>
					<div class="col-md-9">
						<div class="col-md-3"><input name="days" type="text" value="0" size="5" class="form-control"> д </div>
						<div class="col-md-3"><input name="hour" type="text" value="0" size="5" class="form-control"> ч </div>
						<div class="col-md-3"><input name="mins" type="text" value="0" size="5" class="form-control"> м </div>
						<div class="col-md-3"><input name="secs" type="text" value="0" size="5" class="form-control"> с </div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Режим отпуска</label>
					<div class="col-md-9">
						<input name="ro" type="checkbox" value="1" class="form-control">
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn green">Забанить</button>
				</div>
			</div>
		</form>
	</div>
</div>