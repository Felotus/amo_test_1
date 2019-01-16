<?php
function __autoload($className){
	$className = str_replace( "..", "", $className );
	require_once( "classes/$className.php" );
}
define('ROW_START', 0);
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
	$max_row = $config['max_row'];
	$field_name = 'Мультиполе';
	$num = DataFilter::clear($_POST['num']);
	$amo_us = new AmoConstruct($config['api']);
	$amo_us->auth($config['akk'], $config['mail'], $config['hash']);
	$multi = new Field();	
	$multi->set_type(Field::MULTISELECT);
	$multi->set_name($field_name);
	$multi->set_enums($enums_val);
	$multi->set_origin($config['origin']);
	$multi = $amo_us->createField(Contact::ELEM_TYPE, $multi);
	$multi = $amo_us->getFieldEnums(Contact::ELEM_TYPE, $multi);
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
		$contacts = $amo_us->createElemsNEW($contacts);
		foreach ($contacts as $k => $v) {
			$companies[$k] = new Company();
			$companies[$k]->set_name(mt_rand());
			$companies[$k]->set_contacts([$contacts[$k]->get_id()]);
		}
		$companies = $amo_us->createElemsNEW($companies);
		foreach ($contacts as $k => $v) {
			$leads[$k] = new lead();
			$leads[$k]->set_name(mt_rand());
			$leads[$k]->set_contacts([$contacts[$k]->get_id()]);
			$leads[$k]->set_company($companies[$k]->get_id());
		}
		$amo_us->createElemsNEW($leads);
		foreach ($contacts as $k => $v) {
			$customers[$k] = new Customer();
			$customers[$k]->set_name(mt_rand());
			$customers[$k]->set_contacts([$contacts[$k]->get_id()]);
			$customers[$k]->set_company($companies[$k]->get_id());
		}
		$amo_us->createElemsNEW($customers);
	}
	$start_row = ROW_START;
	do {
		$result = $amo_us->getContacts($start_row, $max_row);
		if (is_array($result)) {
			foreach ($result as $key => $value) {
				$enums_data = [];
				$multiCont = clone($multi);
				foreach ($multi->get_enums() as $val) {	
					if (mt_rand(0, 1) === 1) {
						$enums_data[] = $val;
					}			
				}
				$multiCont->set_values($enums_data);
				$result[$key]->set_custom_fields([$multiCont]);
			}
			$amo_us->updateElems($result);
		}
		$start_row += $max_row;
	} while (is_array($result));
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}

