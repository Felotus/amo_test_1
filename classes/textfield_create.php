<?php
try {
	function __autoload($className){
		$className = str_replace( "..", "", $className );
		require_once( "classes/$className.php" );
	}
	$config = include('../config.php');
	$field_name = 'новое поле';
	define('TEXTFIELD_TYPE', 1);
	$elem_id = DataFilter::clear($_POST['id']);
	$amo_us = new AmoConstruct($config['api']);
	$amo_us->auth($config['akk'], $config['mail'], $config['hash'], $config['max_row']);
	switch (DataFilter::clear($_POST['elem_type'])) {
		case Contact::ELEM_TYPE:
			$elem = new Contact();
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
	$field = new Field();
	$field->set_type(TEXTFIELD_TYPE);
	$field->set_name($field_name);
	$field->set_values([DataFilter::clear($_POST['text'])]);
	$field = $amo_us->findFieldOnType($elem->get_type(), $field);
	if (is_null($field->get_id())){
		$field = $amo_us->createField($elem->get_type(), $field);
	}
	$elem->set_custom_fields([$field]);
	$amo_us->updateElems([$elem]);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}
