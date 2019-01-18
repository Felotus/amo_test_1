<?php
try {
	$config = include('../config.php');
	$amo_us = new AmoConstruct($config['api']);
	$amo_us->auth($config['akk'], $config['mail'], $config['hash'], $config['max_row']);
	echo json_encode($amo_us->getTaskTypes());
} catch ( Exception $e ) {
	echo json_encode([$e->getCode() => $e->getMessage()]);
}

