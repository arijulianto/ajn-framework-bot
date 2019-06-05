<?php
return [
	'mode'=>'manual', // rule|manual
	'source'=>'file', // db|file
	'source_name'=>'rule.json', // db: nama tabel; file: nama file
	'db' => [
		'host' => '127.0.0.1',
		'user' => 'root',
		'password' => '',
		'name' => 'bot_db',
	],
	// config bot platform
	'bot' => [
		'telegram'=>array(
			'telebot'=>['name'=>'Nama Bot', 'token'=>'token'],
		),/*
		'line'=>array(
			'linebot'=>['name'=>'Nama Bot', 'token'=>'access_token, 'secret'=>'secret'],
		),
		'messenger'=>array(
			'fb.page'=>['id'=>'12345054321', 'name'=>'Nama Fans Page', 'token'=>'token_akses_halaman'],
		)*/
	],
	'debug'=>true,
];
