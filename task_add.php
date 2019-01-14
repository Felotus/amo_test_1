<?php
try {
	function __autoload($className){
		$className = str_replace( "..", "", $className );
		require_once( "classes/$className.php" );
	}
	$config = include('../config.php');
	$elem_name = 'случайное имя';
	$elem_id = DataFilter::clear($_POST['id']);
	$amo_us = new AmoConstruct($config['api']);
	$amo_us->auth($config['akk'], $config['mail'], $config['hash'], $config['max_row']);
	switch ($_POST['elem_type']) {
		case Contact::ELEM_TYPE:
			$elem = new Contact($elem_id);
			break;
		case Company::ELEM_TYPE:
			$elem = new Company();
			break;
		case Lead::ELEM_TYPE:
			$elem = new Lead();
			break;
		case Customer::ELEM_TYPE:
			$elem = new Customer();
			break;
		default:
			throw new Exception('Элемент не найден', 88);
			break;
	};
	$elem->set_id($elem_id);
	$task = new Task();
	$task->get_type(DataFilter::clear($_POST['task_type']));
	$task->get_val(DataFilter::clear($_POST['text']));
	$task->get_date(DataFilter::clear($_POST['date']));
	$amo_us->createTask($elem, $task);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}

