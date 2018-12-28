<?php
define('CONT_TYPE', 1);
function __autoload($className){
	$className = str_replace( "..", "", $className );
	require_once( "classes/$className.php" );
}
try {
	if (!file_exists('../config.php')) {
		throw new Exception('Файл конфига не найден', 69);	
	}
	$config = include('../config.php');
	$amo_us = new amo_user($config['akk'], $config['mail'], $config['link'], $config['hash'],$config['max_row']);
	$amo_us->amo_auth();
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
	$multi = $amo_us->create_multitext('Мультиполе2', $enums_val, CONT_TYPE);
	echo $multi->get_id().PHP_EOL;
	$amo_us->update_rand_all_multitext($multi->get_id(), $multi->get_enums(), CONT_TYPE);
	$amo_us->mass_create($amo_us->data_clean($_POST['num']), $multi->get_id(), $multi->get_enums());
	echo 'готово';
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}






