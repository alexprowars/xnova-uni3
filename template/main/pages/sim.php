<script>

	var groups = new Array(100);

	function vis_row(TAG, gID) {
		var coll = document.getElementsByTagName(TAG);
		if (!groups[gID] == null || groups[gID] == 0) {
			groups[gID] = 1;
		} else {
			groups[gID] = 0;
		}
		if (coll != null) {
			if (coll.length != null) {
				if (groups[gID] == 0) {
					for (var i = 0; i < coll.length; i++) {
						if (coll[i].id == gID)
							coll[i].style.display = 'none';
					}

				} else {
					for (var i = 0; i < coll.length; i++) {
						if (coll[i].id == gID)
							coll[i].style.display = '';
					}
				}
			}
		}
	}

	function vis_cols(TAG, PRE, sID, gID) {
		var s = parseInt(sID);
		var g = parseInt(gID);
		for (var i = s; i < s + 5; i++) {
			if (i < s + g) {
				groups[PRE + i] = 0;
				vis_row(TAG, PRE + i);
			} else {
				groups[PRE + i] = 1;
				vis_row(TAG, PRE + i);
			}
		}
	}

	function opt() {
		var txt = "", tstr = "", tkey, tval;
		var coll = document.getElementsByTagName("INPUT");
		tkey = new Array();
		if (coll != null) {
			if (coll.length != null) {
				for (var i = 0; i < coll.length; i++) {
					if (coll[i].value > 0) {
						tstr = coll[i].name;
						tval = tstr.split("-");
						if (tval[2] == undefined) {
							if (document.getElementById("" + tval[0] + "-" + tval[1] + "-l") != undefined) {
								tvar = tval[0];
								tval[0] = parseInt(tval[0].charAt(2));
								if (tkey[tval[0]]) {
									tkey[tval[0]] += parseInt(tval[1]) + ',' + coll[i].value + '!' + document.getElementById("" + tvar + "-" + tval[1] + "-l").value + ';';
								} else {
									tkey[tval[0]] = parseInt(tval[1]) + ',' + coll[i].value + '!' + document.getElementById("" + tvar + "-" + tval[1] + "-l").value + ';';
								}
							} else {
								if (parseInt(tval[1]) < 200) {
									tval[0] = parseInt(tval[0].charAt(2));
									if (tkey[tval[0]]) {
										tkey[tval[0]] += parseInt(tval[1]) + ',' + coll[i].value + ';';
									} else {
										tkey[tval[0]] = parseInt(tval[1]) + ',' + coll[i].value + ';';
									}
								} else {
									tval[0] = parseInt(tval[0].charAt(2));
									if (tkey[tval[0]]) {
										tkey[tval[0]] += parseInt(tval[1]) + ',' + coll[i].value + '!0;';
									} else {
										tkey[tval[0]] = parseInt(tval[1]) + ',' + coll[i].value + '!0;';
									}
								}
							}
						}
					}
				}
			}
		}
		if (tkey != null) {
			if (tkey.length != null) {
				for (var i = 0; i < tkey.length; i++) {
					if (tkey[i]) {
						txt += tkey[i] + '|';
					} else {
						txt += '|';
					}
				}
			}
		}
		document.forms.form.r.value = txt;
		document.forms.form.submit;
	}

	function gclear(gID) {
		var tstr = "", tval;
		var coll = document.getElementsByTagName("INPUT");
		tkey = new Array();
		if (coll != null) {
			if (coll.length != null) {
				for (var i = 0; i < coll.length; i++) {
					if (coll[i].name != "") {
						tstr = coll[i].name;
					} else {
						tstr = coll[i].id;
					}
					tval = tstr.split("-");
					tval[0] = parseInt(tval[0].charAt(2));
					if (gID == "all") {
						coll[i].value = 0;
					} else if (tval[0] == gID) {
						coll[i].value = 0;
					}
				}
			}
		}
	}

</script>

<style>
	input.number {
		width: 45px;
		text-align: right;
	}

	input.lvl {
		width: 23px;
		text-align: right;
	}
</style>

<center>
<form method="post" action="/xnsim/sim.php<?=(core::getConfig('socialIframeView', 0) ? '?ingame' : '') ?>" name="form" id="form" autocomplete="off" <?=(user::get()->getUserOption('ajax_navigation') ? 'class="noajax"' : '') ?> target="_blank">
<input type="hidden" name="r" value="">
<table>

<tr>
	<th> XNova SIM</th>
	<th colspan="11" class="spezial">

		<SELECT NAME="Att" SIZE="1" onchange='vis_cols("TH","gr",0,this.value);'>
			<OPTION VALUE="1" SELECTED>1
			<OPTION VALUE="2">2
			<OPTION VALUE="3">3
		</SELECT>

		Исходная ситуация

		<SELECT NAME="Def" SIZE="1" onchange='vis_cols("TH","gr",5,this.value);'>
			<OPTION VALUE="1" SELECTED>1
			<OPTION VALUE="2">2
			<OPTION VALUE="3">3
		</SELECT>

	</th>
</tr>

<tr>
	<th>&nbsp;</th>
	<th id='gr0'>Ведущий</th>
	<th id='gr1'>Атакующий&nbsp;1</th>
	<th id='gr2'>Атакующий&nbsp;2</th>
	<th id='gr3'>Атакующий&nbsp;3</th>
	<th id='gr4'>Атакующий&nbsp;4</th>
	<th id='gr5'>Планета</th>
	<th id='gr6'>Защитник&nbsp;1</th>
	<th id='gr7'>Защитник&nbsp;2</th>
	<th id='gr8'>Защитник&nbsp;3</th>
	<th id='gr9'>Защитник&nbsp;4</th>
</tr>


<tr>
	<td class=c colspan="12">Исследования и офицеры</td>
</tr>

<tr>

	<th>Оружейная технология</th>

	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['109']['c'])) ? $parse['att']['109']['c'] : 0)?>" type="text" name="gr0-109" maxlength="2"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-109" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-109" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-109" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-109" maxlength="2"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['109']['c'])) ? $parse['def']['109']['c'] : 0)?>" type="text" name="gr5-109" maxlength="2"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-109" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-109" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-109" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-109" maxlength="2"></th>

</tr>

<tr>

	<th>Щитовая технология</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['110']['c'])) ? $parse['att']['110']['c'] : 0)?>" type="text" name="gr0-110" maxlength="2"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-110" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-110" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-110" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-110" maxlength="2"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['110']['c'])) ? $parse['def']['110']['c'] : 0)?>" type="text" name="gr5-110" maxlength="2"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-110" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-110" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-110" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-110" maxlength="2"></th>

</tr>

<tr>

	<th>Броня космических кораблей</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['111']['c'])) ? $parse['att']['111']['c'] : 0)?>" type="text" name="gr0-111" maxlength="2"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-111" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-111" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-111" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-111" maxlength="2"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['111']['c'])) ? $parse['def']['111']['c'] : 0)?>" type="text" name="gr5-111" maxlength="2"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-111" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-111" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-111" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-111" maxlength="2"></th>

</tr>

<tr>

	<th>Лазерная технология</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['120']['c'])) ? $parse['att']['120']['c'] : 0)?>" type="text" name="gr0-120" maxlength="2"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-120" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-120" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-120" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-120" maxlength="2"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['120']['c'])) ? $parse['def']['120']['c'] : 0)?>" type="text" name="gr5-120" maxlength="2"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-120" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-120" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-120" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-120" maxlength="2"></th>

</tr>
<tr>

	<th>Ионная технология</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['121']['c'])) ? $parse['att']['121']['c'] : 0)?>" type="text" name="gr0-121" maxlength="2"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-121" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-121" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-121" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-121" maxlength="2"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['121']['c'])) ? $parse['def']['121']['c'] : 0)?>" type="text" name="gr5-121" maxlength="2"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-121" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-121" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-121" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-121" maxlength="2"></th>

</tr>
<tr>

	<th>Плазменная технология</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['122']['c'])) ? $parse['att']['122']['c'] : 0)?>" type="text" name="gr0-122" maxlength="2"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-122" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-122" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-122" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-122" maxlength="2"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['122']['c'])) ? $parse['def']['122']['c'] : 0)?>" type="text" name="gr5-122" maxlength="2"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-122" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-122" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-122" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-122" maxlength="2"></th>

</tr>

<tr>
	<td class=c colspan="12">Флот</td>
</tr>

<tr>

	<th>Малый транспорт</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['202']['c'])) ? $parse['att']['202']['c'] : 0)?>" type="text" name="gr0-202" maxlength="7"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-202" maxlength="7"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-202" maxlength="7"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-202" maxlength="7"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-202" maxlength="7"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['202']['c'])) ? $parse['def']['202']['c'] : 0)?>" type="text" name="gr5-202" maxlength="7"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-202" maxlength="7"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-202" maxlength="7"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-202" maxlength="7"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-202" maxlength="7"></th>

</tr>

<tr>

	<th>Большой транспорт</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['203']['c'])) ? $parse['att']['203']['c'] : 0)?>" type="text" name="gr0-203" maxlength="7"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-203" maxlength="7"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-203" maxlength="7"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-203" maxlength="7"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-203" maxlength="7"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['203']['c'])) ? $parse['def']['203']['c'] : 0)?>" type="text" name="gr5-203" maxlength="7"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-203" maxlength="7"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-203" maxlength="7"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-203" maxlength="7"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-203" maxlength="7"></th>

</tr>

<tr>

	<th>Лёгкий истребитель</th>
	<th id="gr0">
		<input class="number" value="<?=((isset($parse['att']['204']['c'])) ? $parse['att']['204']['c'] : 0)?>" type="text" name="gr0-204" maxlength="7"><input class="lvl" value="<?=((isset($parse['att']['204']['l'])) ? $parse['att']['204']['l'] : 0)?>" type="text" id="gr0-204-l" maxlength="2">
	</th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-204" maxlength="7"><input class="lvl" value="0" type="text" id="gr1-204-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-204" maxlength="7"><input class="lvl" value="0" type="text" id="gr2-204-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-204" maxlength="7"><input class="lvl" value="0" type="text" id="gr3-204-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-204" maxlength="7"><input class="lvl" value="0" type="text" id="gr4-204-l" maxlength="2"></th>
	<th id="gr5">
		<input class="number" value="<?=((isset($parse['def']['204']['c'])) ? $parse['def']['204']['c'] : 0)?>" type="text" name="gr5-204" maxlength="7"><input class="lvl" value="<?=((isset($parse['def']['204']['l'])) ? $parse['def']['204']['l'] : 0)?>" type="text" id="gr5-204-l" maxlength="2">
	</th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-204" maxlength="7"><input class="lvl" value="0" type="text" id="gr6-204-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-204" maxlength="7"><input class="lvl" value="0" type="text" id="gr7-204-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-204" maxlength="7"><input class="lvl" value="0" type="text" id="gr8-204-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-204" maxlength="7"><input class="lvl" value="0" type="text" id="gr9-204-l" maxlength="2"></th>

</tr>

<tr>

	<th>Тяжёлый истребитель</th>
	<th id="gr0">
		<input class="number" value="<?=((isset($parse['att']['205']['c'])) ? $parse['att']['205']['c'] : 0)?>" type="text" name="gr0-205" maxlength="7"><input class="lvl" value="<?=((isset($parse['att']['205']['l'])) ? $parse['att']['205']['l'] : 0)?>" type="text" id="gr0-205-l" maxlength="2">
	</th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-205" maxlength="7"><input class="lvl" value="0" type="text" id="gr1-205-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-205" maxlength="7"><input class="lvl" value="0" type="text" id="gr2-205-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-205" maxlength="7"><input class="lvl" value="0" type="text" id="gr3-205-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-205" maxlength="7"><input class="lvl" value="0" type="text" id="gr4-205-l" maxlength="2"></th>
	<th id="gr5">
		<input class="number" value="<?=((isset($parse['def']['205']['c'])) ? $parse['def']['205']['c'] : 0)?>" type="text" name="gr5-205" maxlength="7"><input class="lvl" value="<?=((isset($parse['def']['205']['l'])) ? $parse['def']['205']['l'] : 0)?>" type="text" id="gr5-205-l" maxlength="2">
	</th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-205" maxlength="7"><input class="lvl" value="0" type="text" id="gr6-205-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-205" maxlength="7"><input class="lvl" value="0" type="text" id="gr7-205-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-205" maxlength="7"><input class="lvl" value="0" type="text" id="gr8-205-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-205" maxlength="7"><input class="lvl" value="0" type="text" id="gr9-205-l" maxlength="2"></th>

</tr>

<tr>

	<th>Крейсер</th>
	<th id="gr0">
		<input class="number" value="<?=((isset($parse['att']['206']['c'])) ? $parse['att']['206']['c'] : 0)?>" type="text" name="gr0-206" maxlength="7"><input class="lvl" value="<?=((isset($parse['att']['206']['l'])) ? $parse['att']['206']['l'] : 0)?>" type="text" id="gr0-206-l" maxlength="2">
	</th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-206" maxlength="7"><input class="lvl" value="0" type="text" id="gr1-206-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-206" maxlength="7"><input class="lvl" value="0" type="text" id="gr2-206-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-206" maxlength="7"><input class="lvl" value="0" type="text" id="gr3-206-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-206" maxlength="7"><input class="lvl" value="0" type="text" id="gr4-206-l" maxlength="2"></th>
	<th id="gr5">
		<input class="number" value="<?=((isset($parse['def']['206']['c'])) ? $parse['def']['206']['c'] : 0)?>" type="text" name="gr5-206" maxlength="7"><input class="lvl" value="<?=((isset($parse['def']['206']['l'])) ? $parse['def']['206']['l'] : 0)?>" type="text" id="gr5-206-l" maxlength="2">
	</th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-206" maxlength="7"><input class="lvl" value="0" type="text" id="gr6-206-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-206" maxlength="7"><input class="lvl" value="0" type="text" id="gr7-206-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-206" maxlength="7"><input class="lvl" value="0" type="text" id="gr8-206-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-206" maxlength="7"><input class="lvl" value="0" type="text" id="gr9-206-l" maxlength="2"></th>

</tr>

<tr>

	<th>Линкор</th>
	<th id="gr0">
		<input class="number" value="<?=((isset($parse['att']['207']['c'])) ? $parse['att']['207']['c'] : 0)?>" type="text" name="gr0-207" maxlength="7"><input class="lvl" value="<?=((isset($parse['att']['207']['l'])) ? $parse['att']['207']['l'] : 0)?>" type="text" id="gr0-207-l" maxlength="2">
	</th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-207" maxlength="7"><input class="lvl" value="0" type="text" id="gr1-207-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-207" maxlength="7"><input class="lvl" value="0" type="text" id="gr2-207-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-207" maxlength="7"><input class="lvl" value="0" type="text" id="gr3-207-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-207" maxlength="7"><input class="lvl" value="0" type="text" id="gr4-207-l" maxlength="2"></th>
	<th id="gr5">
		<input class="number" value="<?=((isset($parse['def']['207']['c'])) ? $parse['def']['207']['c'] : 0)?>" type="text" name="gr5-207" maxlength="7"><input class="lvl" value="<?=((isset($parse['def']['207']['l'])) ? $parse['def']['207']['l'] : 0)?>" type="text" id="gr5-207-l" maxlength="2">
	</th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-207" maxlength="7"><input class="lvl" value="0" type="text" id="gr6-207-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-207" maxlength="7"><input class="lvl" value="0" type="text" id="gr7-207-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-207" maxlength="7"><input class="lvl" value="0" type="text" id="gr8-207-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-207" maxlength="7"><input class="lvl" value="0" type="text" id="gr9-207-l" maxlength="2"></th>

</tr>

<tr>

	<th>Колонизатор</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['208']['c'])) ? $parse['att']['208']['c'] : 0)?>" type="text" name="gr0-208" maxlength="7"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-208" maxlength="7"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-208" maxlength="7"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-208" maxlength="7"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-208" maxlength="7"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['208']['c'])) ? $parse['def']['208']['c'] : 0)?>" type="text" name="gr5-208" maxlength="7"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-208" maxlength="7"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-208" maxlength="7"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-208" maxlength="7"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-208" maxlength="7"></th>

</tr>

<tr>

	<th>Переработчик</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['209']['c'])) ? $parse['att']['209']['c'] : 0)?>" type="text" name="gr0-209" maxlength="7"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-209" maxlength="7"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-209" maxlength="7"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-209" maxlength="7"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-209" maxlength="7"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['209']['c'])) ? $parse['def']['209']['c'] : 0)?>" type="text" name="gr5-209" maxlength="7"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-209" maxlength="7"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-209" maxlength="7"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-209" maxlength="7"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-209" maxlength="7"></th>

</tr>

<tr>

	<th>Шпионский зонд</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['210']['c'])) ? $parse['att']['210']['c'] : 0)?>" type="text" name="gr0-210" maxlength="7"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-210" maxlength="7"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-210" maxlength="7"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-210" maxlength="7"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-210" maxlength="7"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['210']['c'])) ? $parse['def']['210']['c'] : 0)?>" type="text" name="gr5-210" maxlength="7"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-210" maxlength="7"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-210" maxlength="7"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-210" maxlength="7"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-210" maxlength="7"></th>

</tr>

<tr>

	<th>Бомбардировщик</th>
	<th id="gr0">
		<input class="number" value="<?=((isset($parse['att']['211']['c'])) ? $parse['att']['211']['c'] : 0)?>" type="text" name="gr0-211" maxlength="7"><input class="lvl" value="<?=((isset($parse['att']['211']['l'])) ? $parse['att']['211']['l'] : 0)?>" type="text" id="gr0-211-l" maxlength="2">
	</th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-211" maxlength="7"><input class="lvl" value="0" type="text" id="gr1-211-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-211" maxlength="7"><input class="lvl" value="0" type="text" id="gr2-211-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-211" maxlength="7"><input class="lvl" value="0" type="text" id="gr3-211-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-211" maxlength="7"><input class="lvl" value="0" type="text" id="gr4-211-l" maxlength="2"></th>
	<th id="gr5">
		<input class="number" value="<?=((isset($parse['def']['211']['c'])) ? $parse['def']['211']['c'] : 0)?>" type="text" name="gr5-211" maxlength="7"><input class="lvl" value="<?=((isset($parse['def']['211']['l'])) ? $parse['def']['211']['l'] : 0)?>" type="text" id="gr5-211-l" maxlength="2">
	</th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-211" maxlength="7"><input class="lvl" value="0" type="text" id="gr6-211-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-211" maxlength="7"><input class="lvl" value="0" type="text" id="gr7-211-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-211" maxlength="7"><input class="lvl" value="0" type="text" id="gr8-211-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-211" maxlength="7"><input class="lvl" value="0" type="text" id="gr9-211-l" maxlength="2"></th>

</tr>

<tr>

	<th>Солнечный спутник</th>
	<th id="gr0">-</th>
	<th id="gr1">-</th>
	<th id="gr2">-</th>
	<th id="gr3">-</th>
	<th id="gr4">-</th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['212']['c'])) ? $parse['def']['212']['c'] : 0)?>" type="text" name="gr5-212" maxlength="7"></th>
	<th id="gr6">-</th>
	<th id="gr7">-</th>
	<th id="gr8">-</th>
	<th id="gr9">-</th>

</tr>

<tr>

	<th>Уничтожитель</th>
	<th id="gr0">
		<input class="number" value="<?=((isset($parse['att']['213']['c'])) ? $parse['att']['213']['c'] : 0)?>" type="text" name="gr0-213" maxlength="7"><input class="lvl" value="<?=((isset($parse['att']['213']['l'])) ? $parse['att']['213']['l'] : 0)?>" type="text" id="gr0-213-l" maxlength="2">
	</th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-213" maxlength="7"><input class="lvl" value="0" type="text" id="gr1-213-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-213" maxlength="7"><input class="lvl" value="0" type="text" id="gr2-213-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-213" maxlength="7"><input class="lvl" value="0" type="text" id="gr3-213-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-213" maxlength="7"><input class="lvl" value="0" type="text" id="gr4-213-l" maxlength="2"></th>
	<th id="gr5">
		<input class="number" value="<?=((isset($parse['def']['213']['c'])) ? $parse['def']['213']['c'] : 0)?>" type="text" name="gr5-213" maxlength="7"><input class="lvl" value="<?=((isset($parse['def']['213']['l'])) ? $parse['def']['213']['l'] : 0)?>" type="text" id="gr5-213-l" maxlength="2">
	</th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-213" maxlength="7"><input class="lvl" value="0" type="text" id="gr6-213-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-213" maxlength="7"><input class="lvl" value="0" type="text" id="gr7-213-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-213" maxlength="7"><input class="lvl" value="0" type="text" id="gr8-213-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-213" maxlength="7"><input class="lvl" value="0" type="text" id="gr9-213-l" maxlength="2"></th>

</tr>

<tr>

	<th>Звезда смерти</th>
	<th id="gr0">
		<input class="number" value="<?=((isset($parse['att']['214']['c'])) ? $parse['att']['214']['c'] : 0)?>" type="text" name="gr0-214" maxlength="7"><input class="lvl" value="<?=((isset($parse['att']['214']['l'])) ? $parse['att']['214']['l'] : 0)?>" type="text" id="gr0-214-l" maxlength="2">
	</th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-214" maxlength="7"><input class="lvl" value="0" type="text" id="gr1-214-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-214" maxlength="7"><input class="lvl" value="0" type="text" id="gr2-214-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-214" maxlength="7"><input class="lvl" value="0" type="text" id="gr3-214-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-214" maxlength="7"><input class="lvl" value="0" type="text" id="gr4-214-l" maxlength="2"></th>
	<th id="gr5">
		<input class="number" value="<?=((isset($parse['def']['214']['c'])) ? $parse['def']['214']['c'] : 0)?>" type="text" name="gr5-214" maxlength="7"><input class="lvl" value="<?=((isset($parse['def']['214']['l'])) ? $parse['def']['214']['l'] : 0)?>" type="text" id="gr5-214-l" maxlength="2">
	</th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-214" maxlength="7"><input class="lvl" value="0" type="text" id="gr6-214-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-214" maxlength="7"><input class="lvl" value="0" type="text" id="gr7-214-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-214" maxlength="7"><input class="lvl" value="0" type="text" id="gr8-214-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-214" maxlength="7"><input class="lvl" value="0" type="text" id="gr9-214-l" maxlength="2"></th>

</tr>

<tr>

	<th>Линейный крейсер</th>
	<th id="gr0">
		<input class="number" value="<?=((isset($parse['att']['215']['c'])) ? $parse['att']['215']['c'] : 0)?>" type="text" name="gr0-215" maxlength="7"><input class="lvl" value="<?=((isset($parse['att']['215']['l'])) ? $parse['att']['215']['l'] : 0)?>" type="text" id="gr0-215-l" maxlength="2">
	</th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-215" maxlength="7"><input class="lvl" value="0" type="text" id="gr1-215-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-215" maxlength="7"><input class="lvl" value="0" type="text" id="gr2-215-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-215" maxlength="7"><input class="lvl" value="0" type="text" id="gr3-215-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-215" maxlength="7"><input class="lvl" value="0" type="text" id="gr4-215-l" maxlength="2"></th>
	<th id="gr5">
		<input class="number" value="<?=((isset($parse['def']['215']['c'])) ? $parse['def']['215']['c'] : 0)?>" type="text" name="gr5-215" maxlength="7"><input class="lvl" value="<?=((isset($parse['def']['215']['l'])) ? $parse['def']['215']['l'] : 0)?>" type="text" id="gr5-215-l" maxlength="2">
	</th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-215" maxlength="7"><input class="lvl" value="0" type="text" id="gr6-215-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-215" maxlength="7"><input class="lvl" value="0" type="text" id="gr7-215-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-215" maxlength="7"><input class="lvl" value="0" type="text" id="gr8-215-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-215" maxlength="7"><input class="lvl" value="0" type="text" id="gr9-215-l" maxlength="2"></th>

</tr>

<tr>

	<th>Передвижная база</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['216']['c'])) ? $parse['att']['216']['c'] : 0)?>" type="text" name="gr0-216" maxlength="7"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-216" maxlength="7"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-216" maxlength="7"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-216" maxlength="7"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-216" maxlength="7"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['216']['c'])) ? $parse['def']['216']['c'] : 0)?>" type="text" name="gr5-216" maxlength="7"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-216" maxlength="7"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-216" maxlength="7"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-216" maxlength="7"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-216" maxlength="7"></th>

</tr>

<tr>
	<th>Корвет</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['220']['c'])) ? $parse['att']['220']['c'] : 0)?>" type="text" name="gr0-220" maxlength="7">
		<input class="lvl" value="<?=((isset($parse['att']['220']['l'])) ? $parse['att']['220']['l'] : 0)?>" type="text" id="gr0-220-l" maxlength="2"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-220" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-220-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-220" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-220-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-220" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-220-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-220" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-220-l" maxlength="2"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['220']['c'])) ? $parse['def']['220']['c'] : 0)?>" type="text" name="gr5-220" maxlength="7">
		<input class="lvl" value="<?=((isset($parse['def']['220']['l'])) ? $parse['def']['220']['l'] : 0)?>" type="text" id="gr5-220-l" maxlength="2"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-220" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-220-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-220" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-220-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-220" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-220-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-220" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-220-l" maxlength="2"></th>
</tr>

<tr>
	<th>Перехватчик</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['221']['c'])) ? $parse['att']['221']['c'] : 0)?>" type="text" name="gr0-221" maxlength="7">
		<input class="lvl" value="<?=((isset($parse['att']['221']['l'])) ? $parse['att']['221']['l'] : 0)?>" type="text" id="gr0-221-l" maxlength="2"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-221" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-221-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-221" maxlength="7"> <input class="lvl" value="0" type="text" id="gr2-221-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-221" maxlength="7"> <input class="lvl" value="0" type="text" id="gr3-221-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-221" maxlength="7"> <input class="lvl" value="0" type="text" id="gr4-221-l" maxlength="2"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['221']['c'])) ? $parse['def']['221']['c'] : 0)?>" type="text" name="gr5-221" maxlength="7">
		<input class="lvl" value="<?=((isset($parse['def']['221']['l'])) ? $parse['def']['221']['l'] : 0)?>" type="text" id="gr5-221-l" maxlength="2"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-221" maxlength="7"> <input class="lvl" value="0" type="text" id="gr6-221-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-221" maxlength="7"> <input class="lvl" value="0" type="text" id="gr7-221-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-221" maxlength="7"> <input class="lvl" value="0" type="text" id="gr8-221-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-221" maxlength="7"> <input class="lvl" value="0" type="text" id="gr9-221-l" maxlength="2"></th>
</tr>

<tr>
	<th>Дредноут</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['222']['c'])) ? $parse['att']['222']['c'] : 0)?>" type="text" name="gr0-222" maxlength="7">
		<input class="lvl" value="<?=((isset($parse['att']['222']['l'])) ? $parse['att']['222']['l'] : 0)?>" type="text" id="gr0-222-l" maxlength="2"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-222" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-222-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-222" maxlength="7"> <input class="lvl" value="0" type="text" id="gr2-222-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-222" maxlength="7"> <input class="lvl" value="0" type="text" id="gr3-222-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-222" maxlength="7"> <input class="lvl" value="0" type="text" id="gr4-222-l" maxlength="2"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['222']['c'])) ? $parse['def']['222']['c'] : 0)?>" type="text" name="gr5-222" maxlength="7">
		<input class="lvl" value="<?=((isset($parse['def']['222']['l'])) ? $parse['def']['222']['l'] : 0)?>" type="text" id="gr5-222-l" maxlength="2"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-222" maxlength="7"> <input class="lvl" value="0" type="text" id="gr6-222-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-222" maxlength="7"> <input class="lvl" value="0" type="text" id="gr7-222-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-222" maxlength="7"> <input class="lvl" value="0" type="text" id="gr8-222-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-222" maxlength="7"> <input class="lvl" value="0" type="text" id="gr9-222-l" maxlength="2"></th>
</tr>

<tr>
	<th>Корсар</th>
	<th id="gr0"><input class="number" value="<?=((isset($parse['att']['223']['c'])) ? $parse['att']['223']['c'] : 0)?>" type="text" name="gr0-223" maxlength="7">
		<input class="lvl" value="<?=((isset($parse['att']['223']['l'])) ? $parse['att']['223']['l'] : 0)?>" type="text" id="gr0-223-l" maxlength="2"></th>
	<th id="gr1"><input class="number" value="0" type="text" name="gr1-223" maxlength="7"> <input class="lvl" value="0" type="text" id="gr1-223-l" maxlength="2"></th>
	<th id="gr2"><input class="number" value="0" type="text" name="gr2-223" maxlength="7"> <input class="lvl" value="0" type="text" id="gr2-223-l" maxlength="2"></th>
	<th id="gr3"><input class="number" value="0" type="text" name="gr3-223" maxlength="7"> <input class="lvl" value="0" type="text" id="gr3-223-l" maxlength="2"></th>
	<th id="gr4"><input class="number" value="0" type="text" name="gr4-223" maxlength="7"> <input class="lvl" value="0" type="text" id="gr4-223-l" maxlength="2"></th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['223']['c'])) ? $parse['def']['223']['c'] : 0)?>" type="text" name="gr5-223" maxlength="7">
		<input class="lvl" value="<?=((isset($parse['def']['223']['l'])) ? $parse['def']['223']['l'] : 0)?>" type="text" id="gr5-223-l" maxlength="2"></th>
	<th id="gr6"><input class="number" value="0" type="text" name="gr6-223" maxlength="7"> <input class="lvl" value="0" type="text" id="gr6-223-l" maxlength="2"></th>
	<th id="gr7"><input class="number" value="0" type="text" name="gr7-223" maxlength="7"> <input class="lvl" value="0" type="text" id="gr7-223-l" maxlength="2"></th>
	<th id="gr8"><input class="number" value="0" type="text" name="gr8-223" maxlength="7"> <input class="lvl" value="0" type="text" id="gr8-223-l" maxlength="2"></th>
	<th id="gr9"><input class="number" value="0" type="text" name="gr9-223" maxlength="7"> <input class="lvl" value="0" type="text" id="gr9-223-l" maxlength="2"></th>
</tr>


<tr>
	<td class=c colspan="12">Оборона</td>
</tr>

<tr>

	<th>Ракетная установка</th>
	<th id="gr0">-</th>
	<th id="gr1">-</th>
	<th id="gr2">-</th>
	<th id="gr3">-</th>
	<th id="gr4">-</th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['401']['c'])) ? $parse['def']['401']['c'] : 0)?>" type="text" name="gr5-401" maxlength="7"></th>
	<th id="gr6">-</th>
	<th id="gr7">-</th>
	<th id="gr8">-</th>
	<th id="gr9">-</th>

</tr>

<tr>

	<th>Легкий лазер</th>
	<th id="gr0">-</th>
	<th id="gr1">-</th>
	<th id="gr2">-</th>
	<th id="gr3">-</th>
	<th id="gr4">-</th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['402']['c'])) ? $parse['def']['402']['c'] : 0)?>" type="text" name="gr5-402" maxlength="7"></th>
	<th id="gr6">-</th>
	<th id="gr7">-</th>
	<th id="gr8">-</th>
	<th id="gr9">-</th>

</tr>

<tr>

	<th>Тяжёлый лазер</th>
	<th id="gr0">-</th>
	<th id="gr1">-</th>
	<th id="gr2">-</th>
	<th id="gr3">-</th>
	<th id="gr4">-</th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['403']['c'])) ? $parse['def']['403']['c'] : 0)?>" type="text" name="gr5-403" maxlength="7"></th>
	<th id="gr6">-</th>
	<th id="gr7">-</th>
	<th id="gr8">-</th>
	<th id="gr9">-</th>
</tr>

<tr>

	<th>Пушка Гаусса</th>
	<th id="gr0">-</th>
	<th id="gr1">-</th>
	<th id="gr2">-</th>
	<th id="gr3">-</th>
	<th id="gr4">-</th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['404']['c'])) ? $parse['def']['404']['c'] : 0)?>" type="text" name="gr5-404" maxlength="7"></th>
	<th id="gr6">-</th>
	<th id="gr7">-</th>
	<th id="gr8">-</th>
	<th id="gr9">-</th>
</tr>

<tr>

	<th>Ионное орудие</th>
	<th id="gr0">-</th>
	<th id="gr1">-</th>
	<th id="gr2">-</th>
	<th id="gr3">-</th>
	<th id="gr4">-</th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['405']['c'])) ? $parse['def']['405']['c'] : 0)?>" type="text" name="gr5-405" maxlength="7"></th>
	<th id="gr6">-</th>
	<th id="gr7">-</th>
	<th id="gr8">-</th>
	<th id="gr9">-</th>
</tr>

<tr>

	<th>Плазменное орудие</th>
	<th id="gr0">-</th>
	<th id="gr1">-</th>
	<th id="gr2">-</th>
	<th id="gr3">-</th>
	<th id="gr4">-</th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['406']['c'])) ? $parse['def']['406']['c'] : 0)?>" type="text" name="gr5-406" maxlength="7"></th>
	<th id="gr6">-</th>
	<th id="gr7">-</th>
	<th id="gr8">-</th>
	<th id="gr9">-</th>
</tr>

<tr>

	<th>Малый щитовой купол</th>
	<th id="gr0">-</th>
	<th id="gr1">-</th>
	<th id="gr2">-</th>
	<th id="gr3">-</th>
	<th id="gr4">-</th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['407']['c'])) ? $parse['def']['407']['c'] : 0)?>" type="text" name="gr5-407" maxlength="7"></th>
	<th id="gr6">-</th>
	<th id="gr7">-</th>
	<th id="gr8">-</th>
	<th id="gr9">-</th>
</tr>

<tr>

	<th>Большой щитовой купол</th>
	<th id="gr0">-</th>
	<th id="gr1">-</th>
	<th id="gr2">-</th>
	<th id="gr3">-</th>
	<th id="gr4">-</th>
	<th id="gr5"><input class="number" value="<?=((isset($parse['def']['408']['c'])) ? $parse['def']['408']['c'] : 0)?>" type="text" name="gr5-408" maxlength="7"></th>
	<th id="gr6">-</th>
	<th id="gr7">-</th>
	<th id="gr8">-</th>
	<th id="gr9">-</th>
</tr>

<tr align="center">
	<th>&nbsp;</th>
	<th id='gr0'><a href='#' onClick='gclear("0");'>Очистить</a></th>
	<th id='gr1'><a href='#' onClick='gclear("1");'>Очистить</a></th>
	<th id='gr2'><a href='#' onClick='gclear("2");'>Очистить</a></th>
	<th id='gr3'><a href='#' onClick='gclear("3");'>Очистить</a></th>
	<th id='gr4'><a href='#' onClick='gclear("4");'>Очистить</a></th>
	<th id='gr5'><a href='#' onClick='gclear("5");'>Очистить</a></th>
	<th id='gr6'><a href='#' onClick='gclear("6");'>Очистить</a></th>
	<th id='gr7'><a href='#' onClick='gclear("7");'>Очистить</a></th>
	<th id='gr8'><a href='#' onClick='gclear("8");'>Очистить</a></th>
	<th id='gr9'><a href='#' onClick='gclear("9");'>Очистить</a></th>
</tr>


<tr>
	<th colspan='12'><input class="button" type="submit" id="SendBtn" value="Расчитать!" onclick="opt()"></th>
</tr>
</table>
</form>
</center>

<script>vis_cols("TH", "gr", 0, 1);
vis_cols("TH", "gr", 5, 1);
vis_row("TR", "ts");
vis_row("TR", "sp");
vis_row("TR", "gb");
vis_row("TR", "of");</script>