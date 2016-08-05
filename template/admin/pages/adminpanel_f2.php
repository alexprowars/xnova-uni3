<div class="portlet box green">
	<div class="portlet-title">
		<div class="caption"><?=_getText('adm_search_ip') ?></div>
	</div>
	<div class="portlet-body form">
		<form action="/admin/mode/paneladmina/" method="post" class="form-horizontal form-bordered">
			<input type="hidden" name="result" value="ip">
			<div class="form-body">
				<div class="form-group">
					<label class="col-md-3 control-label"><?=_getText('adm_ip') ?></label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="ip">
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn green"><?=_getText('adm_bt_search') ?></button>
				</div>
			</div>
		</form>
	</div>
</div>
