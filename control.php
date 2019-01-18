<?php
include_once('autoload.php');
try {	
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		$control = new MainController();
		$control->actionGetView();
	} else {
		$router = new Router();
		$router->run();
	}
} catch ( Exception $e ) {
	echo json_encode([$e->getCode() => $e->getMessage()]);
}
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//$router->runControl(DataFilter::clear($req_types[$_POST['req_type']]));


