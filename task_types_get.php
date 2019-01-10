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
	echo json_encode($task->getTaskTypes());
} catch ( Exception $e ) {
	echo json_encode([$e->getCode() => $e->getMessage()]);
}
