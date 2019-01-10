<?php
try {
	function __autoload($className){
		$className = str_replace( "..", "", $className );
		require_once( "classes/$className.php" );
	}
	$config = include('../config.php');
	$amo_us = new Curl_creator($config['akk'], $config['mail'], $config['hash'], $config['max_row']);
	$amo_us->auth();
	$task = new Task($amo_us);
	$task->close($amo_us->cleanData($_POST['id']), $amo_us->cleanData($_POST['text']));
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
};
