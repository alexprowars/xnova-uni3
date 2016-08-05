<?php

if (!defined('INSIDE')) {
	die("attemp hacking");
}

$lang['arrow']['cols'] = array(
	12  => 2,
	15  => 2,
	21  => 0,
	33  => 4,
	110 => 2,
	111 => 0,
	113 => 0,
	115 => 0,
	117 => 2,
	120 => 0,
	121 => 2,
	202 => 2,
	203 => 2,
	204 => 2,
	205 => 4,
	206 => 4,
	211 => 8,
	212 => 0,
	214 => 6,
	407 => 4,
	408 => 4,
	502 => 2,
	503 => 4
	);

$lang['arrow']['12'] = array(
	'12_12'   => array(array(0,1,0,0)),
	'113_12'  => array(array(2,0,0,0),array(1,0,1,2,'NE'),array(1,1,8,1)),
	'31_113'  => array(array(4,0,0,0),array(3,0,6,1,'SE')),
	'3_12'    => array(array(2,2,0,0),array(1,2,2,2,'NW'),array(1,1,18,1)),
	);

$lang['arrow']['15'] = array(
	'15_15'   => array(array(0,1,0,0)),
	'108_15'  => array(array(2,0,0,0),array(1,0,1,2,'NE'),array(1,1,8,1)),
	'31_108'  => array(array(4,0,0,0),array(3,0,6,1,'SE')),
	'14_15'   => array(array(2,2,0,0),array(1,2,2,2,'NW'),array(1,1,18,1)),
	);

$lang['arrow']['21'] = array(
	'21_21'   => array(array(0,0,0,0)),
	'14_21'   => array(array(2,0,0,0),array(1,0,6,1,'SE')),
	);

$lang['arrow']['33'] = array(
	'33_33'   => array(array(0,2,0,0)),
	'108_15'  => array(array(4,0,0,0),array(3,0,1,2,'NE'),array(3,1,8,1)),
	'14_15'   => array(array(4,2,0,0),array(3,2,2,2,'NW'),array(3,1,18,1)),
	'15_33'   => array(array(2,1,0,0),array(1,1,1,2,'NE'),array(1,2,8,1)),
	'31_108'  => array(array(6,2,0,0),array(6,1,5,0),array(6,0,3,0),array(5,0,6,1,'E')),
	'113_33'  => array(array(2,4,0,0),array(1,4,2,0),array(1,3,5,0,'N'),array(1,2,18,1)),
	'31_113'  => array(array(6,3,5,0),array(6,4,4,0),array(5,4,6,0),array(4,4,6,0,'E'),array(3,4,6,1))
	);

$lang['arrow']['110'] = array(
	'110_110' => array(array(0,1,0,0)),
	'113_110' => array(array(2,0,0,0),array(1,0,1,2,'SE'),array(1,1,8,1)),
	'31_113'  => array(array(4,1,0,0),array(4,0,3,0),array(3,0,6,1,'E')),
	'31_110'  => array(array(4,2,4,0),array(3,2,6,0),array(2,2,6,2,'E'),array(1,2,2,0),array(1,1,18,1))
	);

$lang['arrow']['111'] = array(
	'111_111' => array(array(0,0,0,0)),
	'31_111'  => array(array(4,0,0,0),array(3,0,6,0),array(2,0,6,2,'E'),array(1,0,6,1))
	);

$lang['arrow']['113'] = array(
	'113_113' => array(array(0,0,0,0)),
	'31_113'  => array(array(4,0,0,0),array(3,0,6,0),array(2,0,6,2,'E'),array(1,0,6,1))
	);

$lang['arrow']['115'] = array(
	'115_115' => array(array(0,0,0,0)),
	'113_115' => array(array(3,0,0,0),array(2,0,6,2,'NE'),array(1,0,6,1)),
	'31_113'  => array(array(6,0,0,0),array(5,0,6,2,'NE'),array(4,0,6,1))
	);

$lang['arrow']['117'] = array(
	'117_117' => array(array(0,1,0,0)),
	'113_117' => array(array(2,0,0,0),array(1,0,1,2,'SE'),array(1,1,8,1)),
	'31_113'  => array(array(4,1,0,0),array(4,0,3,0),array(3,0,6,1,'E')),
	'31_117'  => array(array(4,2,4,0),array(3,2,6,0),array(2,2,6,2,'E'),array(1,2,2,0),array(1,1,18,1))
	);

$lang['arrow']['120'] = array(
	'120_120' => array(array(0,0,0,0)),
	'113_120' => array(array(3,0,0,0),array(2,0,6,2,'NE'),array(1,0,6,1)),
	'31_113'  => array(array(6,0,0,0),array(5,0,6,2,'NE'),array(4,0,6,1))
	);

$lang['arrow']['121'] = array(
	'121_121' => array(array(0,1,0,0)),
	'31_121'  => array(array(6,1,0,0),array(6,0,3,0),array(5,0,6,0),array(4,0,6,0),array(3,0,6,2,'W'),array(2,0,6,0),array(1,0,1,0),array(1,1,8,1)),
	'31_113'  => array(array(5,1,6,1,'E')),
	'113_121' => array(array(4,1,0,0),array(3,1,6,0),array(2,1,6,2,'W'),array(1,1,6,1)),
	'120_121' => array(array(2,2,0,0),array(1,2,2,2,'SE'),array(1,1,18,1)),
	'113_120' => array(array(3,1,9,0),array(3,2,8,1,'SW')),
	'31_120'  => array(array(6,2,4,0),array(5,2,6,0),array(4,2,6,2,'E'),array(3,2,6,0))
	);

$lang['arrow']['214'] = array(
	'199_214' => array(array(0,0,1,2,'SE'),array(0,1,5,0),array(0,2,5,1),array(1,0,6,0),array(2,0,0,0)),
	'214_214' => array(array(0,3,0,0)),
	'21_214'  => array(array(2,6,0,0),array(1,6,6,0),array(0,6,2,2,'SE'),array(0,5,5,0),array(0,4,15,1)),
	'14_21'   => array(array(5,6,0,0),array(4,6,6,2,'E'),array(3,6,6,1)),
	'31_199'  => array(array(10,3,0,0),array(10,2,5,0),array(10,1,5,0),array(10,0,3,0),array(9,0,6,0),array(8,0,6,0),array(7,0,6,0),array(6,0,6,0),array(5,0,6,2,'W'),array(4,0,6,0),array(3,0,6,1)),
	'114_214' => array(array(4,2,0,0),array(3,2,6,0),array(2,2,6,2,'NW'),array(1,2,1,0),array(1,3,8,1)),
	'118_214' => array(array(2,4,0,0),array(1,4,2,2,'SW'),array(1,3,18,1)),
	'114_118' => array(array(3,2,9,0),array(3,3,5,2,'N'),array(3,4,14,1)),
	'31_114'  => array(array(9,3,13,0),array(9,2,3,0),array(8,2,6,0),array(7,2,6,2,'NW'),array(6,2,6,0),array(5,2,6,1)),
	'31_110'  => array(array(9,3,6,0),array(8,3,6,2,'NW'),array(7,3,6,1)),
	'31_113'  => array(array(10,4,4,2,'SE'),array(9,4,6,1)),
	'113_110' => array(array(8,4,0,0),array(7,4,13,2,'NW'),array(7,3,18,1)),
	'113_114' => array(array(7,4,6,0),array(6,4,6,0),array(5,4,6,2,'E'),array(4,4,2,0),array(4,3,15,1)),
	'110_114' => array(array(6,3,0,0),array(5,3,6,2,'NE'),array(4,3,7,1))
	);

$lang['arrow']['202'] = array(
	'202_202' => array(array(0,1,0,0)),
	'31_113'  => array(array(6,0,0,0),array(5,0,6,1,'E')),
	'113_115' => array(array(4,0,0,0),array(3,0,6,1,'E')),
	'115_202' => array(array(2,0,0,0),array(1,0,1,2,'NE'),array(1,1,8,1)),
	'14_21'   => array(array(4,2,0,0),array(3,2,6,1,'E')),
	'21_202'  => array(array(2,2,0,0),array(1,2,2,2,'NW'),array(1,1,18,1))
	);

$lang['arrow']['203'] = array(
	'203_203' => array(array(0,1,0,0)),
	'31_113'  => array(array(6,0,0,0),array(5,0,6,1,'E')),
	'113_115' => array(array(4,0,0,0),array(3,0,6,1,'E')),
	'115_203' => array(array(2,0,0,0),array(1,0,1,2,'NE'),array(1,1,8,1)),
	'14_21'   => array(array(4,2,0,0),array(3,2,6,1,'E')),
	'21_203'  => array(array(2,2,0,0),array(1,2,2,2,'NW'),array(1,1,18,1))
	);

$lang['arrow']['204'] = array(
	'204_204' => array(array(0,1,0,0)),
	'31_113'  => array(array(6,0,0,0),array(5,0,6,1,'E')),
	'113_115' => array(array(4,0,0,0),array(3,0,6,1,'E')),
	'115_204' => array(array(2,0,0,0),array(1,0,1,2,'NE'),array(1,1,8,1)),
	'14_21'   => array(array(4,2,0,0),array(3,2,6,1,'E')),
	'21_204'  => array(array(2,2,0,0),array(1,2,2,2,'NW'),array(1,1,18,1))
	);

$lang['arrow']['205'] = array(
	'205_205' => array(array(0,2,0,0)),
	'21_205'  => array(array(2,4,0,0),array(1,4,2,0),array(1,3,5,2,'N'),array(1,2,18,1)),
	'14_21'   => array(array(4,4,0,0),array(3,4,6,1,'E')),
	'111_205' => array(array(4,2,0,0),array(3,2,6,0),array(2,2,6,2,'E'),array(1,2,6,1)),
	'31_111'  => array(array(8,1,0,0),array(8,2,4,0),array(7,2,6,0),array(6,2,6,2,'E'),array(5,2,6,1)),
	'117_205' => array(array(2,1,0,0),array(1,1,1,2,'NE'),array(1,2,8,1)),
	'31_117'  => array(array(7,1,6,0),array(6,1,6,0),array(5,1,6,2,'E'),array(4,1,6,0),array(3,1,6,1)),
	'31_113'  => array(array(8,0,3,0),array(7,0,6,1,'E')),
	'113_117' => array(array(6,0,0,0),array(5,0,6,0),array(4,0,6,2,'E'),array(3,0,1,0),array(3,1,8,1))
	);

$lang['arrow']['206'] = array(
	'206_206' => array(array(0,2,0,0)),
	'21_206'  => array(array(2,4,0,0),array(1,4,2,0),array(1,3,5,2,'N'),array(1,2,18,1)),
	'14_21'   => array(array(4,4,0,0),array(3,4,6,1,'E')),
	'121_206' => array(array(2,2,0,0),array(1,2,6,1,'SE')),
	'117_206' => array(array(4,0,0,0),array(3,0,6,0),array(2,0,6,0),array(1,0,1,0),array(1,1,5,2,'N'),array(1,2,8,1)),
	'31_117'  => array(array(8,2,0,0),array(8,1,5,0),array(8,0,3,0),array(7,0,6,0),array(6,0,6,2,'E'),array(5,0,6,1)),
	'31_121'  => array(array(8,3,4,0),array(7,3,6,0),array(6,3,6,0),array(5,3,6,2,'E'),array(4,3,6,0),array(3,3,2,0),array(3,2,18,1)),
	'120_121' => array(array(4,2,0,0),array(3,2,6,1,'SE')),
	'113_121' => array(array(6,1,0,0),array(5,1,6,0),array(4,1,6,2,'E'),array(3,1,1,0),array(3,2,8,1)),
	'31_113'  => array(array(7,2,2,2,'SE'),array(7,1,3,1)),
	'113_120' => array(array(6,2,4,0),array(5,2,6,1,'E'))

	);

$lang['arrow']['211'] = array(
	'211_211' => array(array(0,4,0,0)),
	'117_211' => array(array(4,0,0,0),array(3,0,6,0),array(2,0,6,0),array(1,0,1,0),array(1,1,5,0),array(1,2,5,2,'N'),array(1,3,5,0),array(1,4,8,1)),
	'122_211' => array(array(2,4,0,0),array(1,4,6,2,'SE')),
	'21_211'  => array(array(2,8,0,0),array(1,8,2,0),array(1,7,5,0),array(1,6,5,2,'N'),array(1,5,5,0),array(1,4,18,1)),
	'14_21'   => array(array(4,8,0,0),array(3,8,6,1,'E')),
	'31_117'  => array(array(10,4,0,0),array(10,3,5,0),array(10,2,5,0),array(10,1,5,0),array(10,0,3,0),array(9,0,6,6),array(8,0,6,0),array(7,0,6,0,'E'),array(6,0,6,0),array(5,0,6,1)),
	'31_121'  => array(array(10,5,5,0),array(10,6,4,0),array(9,6,6,0),array(8,6,6,0),array(7,6,6,0,'E'),array(6,6,6,0),array(5,6,6,1)),
	'31_113'  => array(array(9,4,2,0),array(9,3,5,2,'N'),array(9,2,3,1)),
	'113_117' => array(array(8,2,0,0),array(7,2,13,0),array(7,1,3,0),array(6,1,6,0),array(5,1,6,2,'E'),array(4,1,12,1)),
	'121_122' => array(array(4,6,0,0),array(3,6,2,0),array(3,5,5,2,'N'),array(3,4,18,1)),
	'113_121' => array(array(8,3,5,0),array(8,4,5,0),array(8,5,4,0),array(7,5,6,0),array(6,5,6,2,'E'),array(5,5,6,0),array(4,5,1,1)),
	'113_122' => array(array(7,2,6,0),array(6,2,6,0),array(5,2,6,0),array(4,2,6,0,'E'),array(3,2,1,0),array(3,3,5,0),array(3,4,8,1)),
	'113_120' => array(array(7,2,9,0),array(7,3,5,2,'S'),array(7,4,14,1)),
	'120_122' => array(array(6,4,0,0),array(5,4,6,2,'NE'),array(4,4,6,0),array(3,4,6,1))
	);

$lang['arrow']['212'] = array(
	'212_212' => array(array(0,0,0,0)),
	'21_212'  => array(array(2,0,0,0),array(1,0,6,1,'SE')),
	'14_21'   => array(array(4,0,0,0),array(3,0,6,1,'SE')),
	);

$lang['arrow']['407'] = array(
	'407_407' => array(array(0,2,0,0)),
	'21_407'  => array(array(2,4,0,0),array(1,4,2,0),array(1,3,5,2,'N'),array(1,2,18,1)),
	'14_21'   => array(array(4,4,0,0),array(3,4,6,1,'E')),
	'110_407' => array(array(2,0,0,0),array(1,0,9,0),array(1,1,5,2,'N'),array(1,2,8,1)),
	'31_110'  => array(array(6,2,0,0),array(6,1,5,0),array(6,0,3,0),array(5,0,6,0),array(4,0,6,0,'E'),array(3,0,6,1)),
	'31_113'  => array(array(5,2,6,1,'E')),
	'113_110' => array(array(4,2,0,0),array(3,2,2,0),array(3,1,5,2,'N'),array(3,0,18,1))
	);

$lang['arrow']['408'] = array(
	'408_408' => array(array(0,2,0,0)),
	'21_408'  => array(array(2,4,0,0),array(1,4,2,0),array(1,3,5,2,'N'),array(1,2,18,1)),
	'14_21'   => array(array(4,4,0,0),array(3,4,6,1,'E')),
	'110_408' => array(array(2,0,0,0),array(1,0,9,0),array(1,1,5,2,'N'),array(1,2,8,1)),
	'31_110'  => array(array(6,2,0,0),array(6,1,5,0),array(6,0,3,0),array(5,0,6,0),array(4,0,6,0,'E'),array(3,0,6,1)),
	'31_113'  => array(array(5,2,6,1,'E')),
	'113_110' => array(array(4,2,0,0),array(3,2,2,0),array(3,1,5,2,'N'),array(3,0,18,1))
	);

$lang['arrow']['502'] = array(
	'502_502' => array(array(0,1,0,0)),
	'21_502'  => array(array(2,2,0,0),array(1,2,2,2,'NW'),array(1,1,18,1)),
	'14_21'   => array(array(4,2,0,0),array(3,2,6,1,'E')),
	'44_502'  => array(array(2,0,0,0),array(1,0,1,2,'NE'),array(1,1,8,1)),
	);

$lang['arrow']['503'] = array(
	'503_503' => array(array(0,2,0,0)),
	'44_503'  => array(array(2,0,0,0),array(1,0,6,0),array(0,0,1,2,'NE'),array(0,1,5,1)),
	'117_503' => array(array(2,2,0,0),array(1,2,6,1,'E')),
	'21_503'  => array(array(2,4,0,0),array(1,4,6,0),array(0,4,2,2,'NW'),array(0,3,15,1)),
	'14_21'   => array(array(4,4,0,0),array(3,4,6,1,'E')),
	);

?>