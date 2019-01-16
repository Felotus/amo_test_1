<?php
spl_autoload_register(function ($className) {
	$className = str_replace( "..", "", $className);
	require_once( "classes/$className.php" );
});
$req_types = [
	'task_types_get',
	'amo_mass_create',
	'textfield_create',
	'event_add',
	'task_add',
	'task_close'
];
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
$router = new Router();
$router->runControl(DataFilter::clear($req_types[$_POST['req_type']]));

