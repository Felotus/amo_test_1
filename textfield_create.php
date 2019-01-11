<?php
try {
	function __autoload($className){
		$className = str_replace( "..", "", $className );
		require_once( "classes/$className.php" );
	}
	$config = include('../config.php');
	$field_name = 'новое поле';
	define('TEXTFIELD_TYPE', 1);
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
	$field = new Field(TEXTFIELD_TYPE, $field_name);
	$field = $amo_us->findFirsField($elem, $field);
	$amo_us->changeFieldVal($elem, $_POST['text'], $field);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}
