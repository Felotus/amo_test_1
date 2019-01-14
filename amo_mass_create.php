<?php
function __autoload($className){
	$className = str_replace( "..", "", $className );
	require_once( "classes/$className.php" );
}
define('MULTI_TYPE', 5);
try {
	if (!file_exists('../config.php')) {
		throw new Exception('Файл конфига не найден', 69);	
	}
	$config = include('../config.php');
	$enums_val = [
		'значение 1',
		'значение 2',
		'значение 3',
		'значение 4',
		'значение 5',
		'значение 6',
		'значение 7',
		'значение 8',
		'значение 9',
		'значение 10',
	];
	$field_name = 'Мультиполе';
	$num = DataFilter::clear($_POST['num']);

	$amo_us = new AmoConstruct($config['api']);
	$amo_us->auth($config['akk'], $config['mail'], $config['hash'], $config['max_row']);
	$multi = new Field();	
	$multi->set_type(MULTI_TYPE);
	$multi->set_name($field_name);
	$multi->set_enums($enums_val);
	$multi = $amo_us->createField(Contact::ELEM_TYPE, $multi);
	$max_row = $amo_us->get_max_row();
	for ($i = $num, $n = 0; $i > 0; $i -= $max_row, $n++) {
	$contacts = [];
	$companies = [];
	$customers = [];
	$leads = [];
	$elem = '';
	$name = '';
		if ($i > $max_row) {
			$col = $max_row;
		} else {
			$col = $i;
		}
		for ($j = 0; $j < $col; $j++) {
			$elem = new Contact();
			$name = mt_rand();
			$data['add'][] = [
				'name' => $name,
			];
			$elem->set_name($name);
			$contacts[] = $elem;
		};
		$contacts = $amo_us->createContacts($contacts);
		foreach ($contacts as $k => $v) {
			$companies[$k] = new Company();
			$companies[$k]->set_name(mt_rand());
			$companies[$k]->set_contacts([$contacts[$k]->get_id()]);
		}
		$companies = $amo_us->createCompanies($companies);
		foreach ($contacts as $k => $v) {
			$leads[$k] = new lead();
			$leads[$k]->set_name(mt_rand());
			$leads[$k]->set_contacts([$contacts[$k]->get_id()]);
			$leads[$k]->set_company($companies[$k]->get_id());
		}
		$amo_us->createCustLeads($leads);
		foreach ($contacts as $k => $v) {
			$customers[$k] = new Customer();
			$customers[$k]->set_name(mt_rand());
			$customers[$k]->set_contacts([$contacts[$k]->get_id()]);
			$customers[$k]->set_company($companies[$k]->get_id());
		}
		$amo_us->createCustLeads($customers);
	}
	$amo_us->massChangeMultisVal(Contact::ELEM_TYPE, $multi);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}

