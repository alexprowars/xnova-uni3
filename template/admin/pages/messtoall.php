<div class="portlet box green">
	<div class="portlet-title">
		<div class="caption">Отправить сообщение всем игрокам</div>
	</div>
	<div class="portlet-body form">
		<form action="/admin/mode/messall/" method="post" class="form-horizontal form-bordered">
			<div class="form-body">
				<div class="form-group">
					<label class="col-md-3 control-label">Тема сообщения</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="temat">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Сообщение</label>
					<div class="col-md-9">
						<textarea name="tresc" cols="" rows="10" class="form-control"></textarea>
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn green">Отправить</button>
				</div>
			</div>
		</form>
	</div>
</div>