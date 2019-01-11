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
	$amo_us = new AmoConstruct($config['api']);
	$amo_us->auth($config['akk'], $config['mail'], $config['hash'], $config['max_row']);
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
	$multi = new Field(MULTI_TYPE, 'Мультиполе333', $enums_val);
	$multi = $amo_us->createField(Contact::ELEM_TYPE, $multi);
	$amo_us->massCreateElem($_POST['num']);
	$amo_us->massChangeMultisVal(Contact::ELEM_TYPE, $multi);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}

