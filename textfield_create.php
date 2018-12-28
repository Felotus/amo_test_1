<?php

//ищем текстовые строки
function textfield_find($type, $link){
	switch ($type) {
		case 1:
			$type_str = "contacts";
			break;
		case 3:
			$type_str = "companies";
			break;
		case 2:
			$type_str = "leads";
			break;
		case 12:
			$type_str = "customers";
			break;
		default:
			$type_str = "contacts";
			break;
	}
	$textfield_id = 0;
	$links = $link.'/api/v2/account?with=custom_fields';
	$result = req_curl($links);
	if (is_array($result)) {
		$result = $result['_embedded']['custom_fields'][$type_str];
		foreach ($result as $key => $value) {
			if ($value['field_type'] === 1) {
				$textfield_id = $key;
				break;
			}
		};   
	} else {
		throw new Exception('Сервер прислал неожиданный ответ', 007);
	}
	return  $textfield_id;
};

//создаем текстовую строку
function textfield_create($type, $link, $hash){
	$fields['add'] = [
	    [
			'name' => "Текстовое поле",
			'field_type'=> 1,
			'element_type' => $type,
			'origin' => $hash."_".time(),
        ]
	];
	$links = $link.'/api/v2/fields';
	$result = req_curl($links, $fields);
	if (is_array($result)) {
		$result = $result['_embedded']['items'];
		foreach ($result as $v) {
			if (is_array($v)) {
				$output = $v['id'];
			}
		}
	} else {
		throw new Exception('Сервер прислал неожиданный ответ', 007);
	}
	return $output;
};

//заполняем текстовое поле
function textfield_update($type,$elem_id, $field_id, $str, $link){
	switch ($type) {
	case 1:
		$links = $link."/api/v2/contacts";
		break;
	case 3:
		$links = $link."/api/v2/companies";
		break;
	case 2:
		$links = $link."/api/v2/leads";
		break;
	case 12:
		$links = $link."/api/v2/customers";
		break;
	default:
		$links = $link."/api/v2/contacts";
		break;
	}
	$data['update'][] = [
		'id' => $elem_id,
		'updated_at' => time(),
		'custom_fields' => [
			[
				'id' => $field_id,
				'values' => [
					[
						'value' => $str
					]
				]
			]
		]
	];
	$result = req_curl($links, $data);
	if (isset($result['_embedded']['errors'])) {		
		throw new Exception($result['_embedded']['errors']['update'][$elem_id], 006);
	} elseif (!isset($result['_embedded']['items'])) {
		throw new Exception('Сервер прислал неожиданный ответ', 007);
	}
};

try {
	include 'amo_aut.php';
	$elem_type = data_clean($_POST['elem_type']);
	amo_aut($config['link'], $config['mail'], $config['hash']);
	$id = textfield_find($elem_type, $config['link']);
	if ($id === 0){
		$id = textfield_create($elem_type, $config['link'], $config['hash']);
	};
	textfield_update($elem_type, data_clean($_POST['id']), $id, data_clean($_POST['text']), $config['link']);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}

	