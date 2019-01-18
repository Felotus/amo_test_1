<?php
try {

	$config = include('../config.php');
	$elem_name = 'случайное имя';
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
	$note = new Note();
	$note->set_type(DataFilter::clear($_POST['note_type']));
	$note->set_val(DataFilter::clear($_POST['text']));
	$note->set_call_link('http://example.com/calls/1.mp3');
	$note->set_call_duration(30);
	$note->set_origin($config['origin']);

	$amo_us->createNote($elem, $note);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}

