<?php
class Router{
	private $_routes;

	public function runControl($conrollName){
		$conrollName = str_replace('..', '', $conrollName);
		require_once("$conrollName.php");
	}

	//ниже тестовые методы
	public function __construct(){
		$routPath = $_SERVER['DOCUMENT_ROOT'].'/rout_config.php';
		$this->_routes = include($routPath);
	}

	private function getURI(){
		if (!empty($_SERVER['REQUEST_URI'])) {
			return trim($_SERVER['REQUEST_URI'], '/');
		}
	}

	public function run(){
		$uri = $this->getURI();
		foreach ($this->_routes as $uriPattern => $path) {
			if (preg_match("~$uriPattern~", $uri)){
				$segments = explode('/', $path);
				$controllerName = ucfirst(array_shift($segments)).'Controller';
				$actionName = 'action'.ucfirst(array_shift($segments));
				echo 'Класс: '.$controllerName.'</br>';
				echo 'Метод: '.$actionName;
			}
		}

	}

}