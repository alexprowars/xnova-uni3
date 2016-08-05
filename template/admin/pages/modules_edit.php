<? if (isset($info)): ?>
	<div class="portlet box green">
		<div class="portlet-title">
			<div class="caption">Настройка модуля "<?=$info['name'] ?>"</div>
		</div>
		<div class="portlet-body form">
			<form action="/admin/mode/modules/action/edit/id/<?=$info['id'] ?>/" method="post" class="form-horizontal form-row-seperated">
				<div class="form-body">
					<div class="form-group">
						<label class="col-md-3 control-label"></label>
						<div class="col-md-9">
							<input id="active" type="checkbox" class="form-control" name="active" <?=($info['active'] == 1 ? 'checked' : '') ?>>
							<label for="active">Активность</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Алиас</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="alias">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Название</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="name">
						</div>
					</div>
					<div class="form-actions">
						<button type="submit" name="save" class="btn green" value="Y">Сохранить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<? endif; ?>