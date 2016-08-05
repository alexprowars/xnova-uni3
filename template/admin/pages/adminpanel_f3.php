<div class="portlet box green">
	<div class="portlet-title">
		<div class="caption"><?=_getText('adm_mod_level') ?></div>
	</div>
	<div class="portlet-body form">
		<form action="/admin/mode/paneladmina/" method="post" class="form-horizontal form-bordered">
			<input type="hidden" name="result" value="usr_level">
			<div class="form-body">
				<div class="form-group">
					<label class="col-md-3 control-label"><?=_getText('adm_player_nm') ?></label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="player">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?=_getText('adm_mess_lvl1') ?></label>
					<div class="col-md-9">
						<select class="form-control" name="authlvl">
							<? foreach (_getText('user_level') AS $id => $level): ?>
								<option value="<?=$id ?>"><?=$level ?></option>
							<? endforeach; ?>
						</select>
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn green"><?=_getText('adm_bt_change') ?></button>
				</div>
			</div>
		</form>
	</div>
</div>