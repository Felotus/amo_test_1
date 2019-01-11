<?php
try {
	function __autoload($className){
		$className = str_replace( "..", "", $className );
		require_once( "classes/$className.php" );
	}
	$config = include('../config.php');
	$elem_name = 'случайное имя';
	$elem_id = $_POST['id'];
	$amo_us = new AmoConstruct($config['api']);
	$amo_us->auth($config['akk'], $config['mail'], $config['hash'], $config['max_row']);
	switch ($_POST['elem_type']) {
		case Contact::ELEM_TYPE:
			$elem = new Contact($elem_name, $elem_id);
			break;
		case Company::ELEM_TYPE:
			$elem = new Company($elem_name, $elem_id);
			break;
		case Lead::ELEM_TYPE:
			$elem = new Lead($elem_name, $elem_id);
			break;
		case Customer::ELEM_TYPE:
			$elem = new Customer($elem_name, $elem_id);
			break;
		default:
			throw new Exception('Элемент не найден', 88);
			break;
	};
	$note = new Note($_POST['note_type'], $_POST['text']);
	$amo_us->createNote($elem, $note);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}

