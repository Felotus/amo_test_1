<?php
try {
	function __autoload($className){
		$className = str_replace( "..", "", $className );
		require_once( "classes/$className.php" );
	}
	$config = include('../config.php');
	$amo_us = new Curl_creator($config['akk'], $config['mail'], $config['hash'], $config['max_row']);
	$amo_us->auth();
	switch ($_POST['elem_type']) {
		case 1:
			$elem = new Contact($amo_us);
			break;
		case 3:
			$elem = new Company($amo_us);
			break;
		case 2:
			$elem = new Lead($amo_us);
			break;
		case 12:
			$elem = new Customer($amo_us);
			break;
		default:
			throw new Exception('Элемент не найден', 88);
			break;
	};
	$elem->set($amo_us->cleanData($_POST['id']);
	$elem->addTask($amo_us->cleanData($_POST['task_type']), $amo_us->cleanData($_POST['text']), $amo_us->cleanData($_POST['date']);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}

