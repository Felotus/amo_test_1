<?php
include 'amo_aut.php';


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
	$textfield_id=0;
	$links = $link.'/api/v2/account?with=custom_fields';
	$result=req_curl(GET_REQ, $links);
	if (is_array($result)) {
		$result = $result['_embedded']['custom_fields'][$type_str];
		foreach ($result as $key => $value) {
			if ($value['field_type']==1) {
				$textfield_id=$key;
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
	$fields['add'] = array(
	 array(
			'name' => "Текстовое поле",
			'field_type'=> 1,
			'element_type' => $type,
			'origin' => $hash."_".time(),
	 )
	);
	$links=$link.'/api/v2/fields';
	$result=req_curl(POST_REQ, $links, $fields);
	if (is_array($result)) {
		$result=$result['_embedded']['items'];
		foreach ($result as $v) {
			if (is_array($v)) {
				$output=$v['id'];
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
	$data['update'][0]['id']=$elem_id;
	$data['update'][0]['updated_at']=time();
	$data['update'][0]['custom_fields'][0]['id']=$field_id;
	$data['update'][0]['custom_fields'][0]['values'][0]['value']=$str;
	$result=req_curl(POST_REQ, $links, $data);
};

try {
	amo_aut($link, $mail, $hash);
	$id = textfield_find($_POST['elem_type'], $link);
	if ($id == 0){
		$id = textfield_create($_POST['elem_type'], $link,$hash);
	};
	textfield_update($_POST['elem_type'], $_POST['id'], $id,$_POST['text'], $link);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}

	