<?php
date_default_timezone_set('id_ID');
setlocale(LC_ALL, 'Asia/Jakarta');
ini_set('date.timezone', 'Asia/Jakarta');

define('APP', 'AJN Bot 1.0');
define('BASE_PATH', str_replace('\\','/',__DIR__).'/');
define('APP_PATH', str_replace('\\','/',__DIR__).'/inc/');
define('APP_DATA', BASE_PATH.'data/');

$config = require_once  BASE_PATH.'config.php';
include APP_PATH . 'library.php';
include APP_PATH . 'AJN.php';
include APP_PATH . 'AJNBot.php';

echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8" /></head><body>';
$bot = new AJNBot;
$bot->load($config);
$bot->listen();
$pesan = $bot->parse();
$bot->send($pesan);
echo '</body></html>';