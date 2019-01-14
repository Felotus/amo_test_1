<?php
try {
	function __autoload($className){
		$className = str_replace( "..", "", $className );
		require_once( "classes/$className.php" );
	}
	$config = include('../config.php');
	$amo_us = new AmoConstruct($config['api']);
	$amo_us->auth($config['akk'], $config['mail'], $config['hash'], $config['max_row']);
	$task = new Task();
	$task->set_val(DataFilter::clear($_POST['text']));
	$task->set_id(DataFilter::clear($_POST['id']));
	$amo_us->closeTask($task);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
};

