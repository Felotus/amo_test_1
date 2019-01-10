<?php
function __autoload($className){
	$className = str_replace( "..", "", $className );
	require_once( "classes/$className.php" );
}
define('MULTI_TYPE', 5);
try {
	if (!file_exists('../config.php')) {
		throw new Exception('Файл конфига не найден', 69);	
	}
	$config = include('../config.php');
	$amo_us = new Curl_creator($config['akk'], $config['mail'], $config['hash'], $config['max_row']);
	$amo_us->auth();
	$enums_val = [
		'значение 1',
		'значение 2',
		'значение 3',
		'значение 4',
		'значение 5',
		'значение 6',
		'значение 7',
		'значение 8',
		'значение 9',
		'значение 10',
	];
	$cont = new Contact($amo_us);
	$multi = $cont->addField(MULTI_TYPE, 'Мультиполе333', NULL, $enums_val);
	$cont->massCreate($amo_us->cleanData($_POST['num']));
	$cont->massChangeFieldVal($multi);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}






