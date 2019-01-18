<?php
class Router{
	private $_routes;


	public function __construct(){
		$routPath = $_SERVER['DOCUMENT_ROOT'].'/rout_config.php';
		$this->_routes = include($routPath);
	}

	private function getURI(){
		$uri = trim($_SERVER['REQUEST_URI'], '/');
		if (!empty($uri)) {
			return $uri;
		} else {
			return 'index';
		}
	}

	public function run(){
		$uri = $this->getURI();
		foreach ($this->_routes as $uriPattern => $path) {
			if (preg_match("~$uriPattern~", $uri)){
				$segments = explode('/', $path);
				$controllerName = ucfirst(array_shift($segments)).'Controller';
				$actionName = 'action'.ucfirst(array_shift($segments));
				$controller = new $controllerName;
				$controller->$actionName();
			}
		}
	}
}