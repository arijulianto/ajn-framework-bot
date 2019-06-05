<?php
function tanggal($format,$input=NULL){
	$long_bulan_en  = array('January','February','March','April','May','June','July','August','September','October','November','December');
	$short_bulan_en = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	$long_bulan_id  = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$short_bulan_id = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Des');
	$bulan_en = array_merge($long_bulan_en,$short_bulan_en);
	$bulan_id = array_merge($long_bulan_id,$short_bulan_id);
	$long_hari_en  = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$short_hari_en = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
	$long_hari_id  = array('Minggu','Senin','Selasa','Rabu','Kamis','Jum\'at','Sabtu');
	$short_hari_id = array('Mgu','Sen','Sel','Rab','Kam','Jum','Sab');
	$hari_en = array_merge($long_hari_en,$short_hari_en);
	$hari_id = array_merge($long_hari_id,$short_hari_id);
	
	$input = $input ? $input : time();
	$input = !is_numeric($input) ? strtotime($input) : $input;
	
	$output = date($format,$input);
	$output = str_replace($bulan_en,$bulan_id,$output);
	$output = str_replace($hari_en,$hari_id,$output);
	
	return $output;
}

function format_angka($angka){
	$num = (string)$angka;
	$num = explode('.', $num);
	$numb = number_format($num[0], 0, ',', '.');
	return $num[1] ? $numb.','.$num[1] : $numb;
}

function array2flat($array, $lem=':'){
	$output = [];
	foreach($array as $key=>$val){
		$key = str_replace('_', ' ', $key);
		$key = ucwords($key);
		$output[] = $key.' '.$lem.' '.$val;
	}
	return $output;
}