<?php


define("LEADS_TYPE", 1);
define(" _TYPE", 0);


class multitext{
	private $link;
	private $hash;
	private $id;
	private $enums;
	function  __construct($link, $hash){
		$this->link = $link;
		$this->hash = $hash;
		$fields['add'] = [
			[
				'name' => "Выбери значение",
				'field_type'=> 5,
				'element_type' => 1,
				'origin' => $this->hash."_".time().mt_rand(),
				'is_editable' => 0,
				'enums' => [
					 "1 значение",
					 "2 значение",
					 "3 значение",
					 "4 значение",
					 "5 значение",
					 "6 значение",
					 "7 значение",
					 "8 значение",
					 "9 значение",
					 "10 значение"
				]
			]
		];
		$links = $this->link.'/api/v2/fields';
		$result = req_curl($links, $fields);
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach($result as $v){
				$this->id = $v['id'];
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
		$links = $this->link.'/api/v2/account?with=custom_fields';
		$result = req_curl($links);
		if (is_array($result)) {
			$result = $result['_embedded']['custom_fields']['contacts'][$this->id]['enums'];
			foreach ($result as $key => $value) {
				$enums[] = $key;
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}	
		$this->enums = $enums;
	}
	public function get_id(){
		return $this->id;
	}
	public function get_enums(){
		return $this->enums;
	}
}


//ищем Enums мультиселекта
function multitext_find($id, $link){
	$links = $link.'/api/v2/account?with=custom_fields';
	$result = req_curl($links);
	if (is_array($result)) {
		$result = $result['_embedded']['custom_fields']['contacts'][$id]['enums'];
		foreach ($result as $key => $value) {
			$enums[] = $key;
		}
	} else {
		throw new Exception('Сервер прислал неожиданный ответ', 007);
	}	
	return $enums;
};

//создаем мультиселект
function multitext_add($link, $hash){
	$fields['add'] = [
		[
			'name' => "Выбери значение",
			'field_type'=> 5,
			'element_type' => 1,
			'origin' => $hash."_".time().mt_rand(),
			'is_editable' => 0,
			'enums' => [
				 "1 значение",
				 "2 значение",
				 "3 значение",
				 "4 значение",
				 "5 значение",
				 "6 значение",
				 "7 значение",
				 "8 значение",
				 "9 значение",
				 "10 значение"
			]
		]
	];
	$links = $link.'/api/v2/fields';
	$result = req_curl($links, $fields);
	
	if (is_array($result)) {
		$result = $result['_embedded']['items'];
		foreach($result as $v){
			$output = $v['id'];
		}
	} else {
		throw new Exception('Сервер прислал неожиданный ответ', 007);
	}
	return $output;
};

//получаем id всех существующих контактов
function contacts_get($field_id, array $enums, $link, $max_row){
	$limit_offset = 0;
	do {
		$cont_id = array();
		$links = $link.'/api/v2/contacts?limit_rows='.$max_row.'&limit_offset='.$limit_offset;
		$result = req_curl($links);
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $key => $value) {
				$cont_id[] = $value["id"];
			};
			contacts_upd($cont_id, $field_id, $enums, $link);
		}
				$limit_offset += $max_row;
	} while (is_array($result));	
};


//обновляем мультиселекты в контактах
function contacts_upd(array $cont_id, $field_id, array $enums, $link){
	foreach ($cont_id as $v) {
		$enums_data = array();
		foreach ($enums as $val) {
			if (mt_rand(0, 1) === 1) {
				$enums_data[] = $val;
			}
		}
		$data['update'][] = [
			'id' => $v,
			'updated_at'=> time(),
			'custom_fields' => [
				'0' => [
					'id' => $field_id,
					'values' => $enums_data
				]
			]
		];
	}
	$links = $link."/api/v2/contacts";
	$result = req_curl($links, $data);
};

//добавляем заданное количество контактов и возвращаем их id
function contacts_add($num, $field_id, array $enums, $link, $max_row){
	for ($i = $num, $n = 0; $i > 0; $i -= $max_row, $n++) {
		$cont_id = array();
		$data = array();
		if ($i > $max_row) {
			$col = $max_row;
		} else {
			$col = $i;
		}
		for ($j = 0; $j < $col; $j++) {
			$data['add'][($j+($n*$max_row))]['name'] = mt_rand();
			$data['add'][($j+($n*$max_row))]['custom_fields'][0]['id'] = $field_id;
			foreach ($enums as $v) {
				if (mt_rand(0, 1) === 1) {
					$data['add'][($j+($n*$max_row))]['custom_fields'][0]['values'][] = $v;
				}
			}
		};

		$links = $link.'/api/v2/contacts/';
		$result = req_curl($links, $data);
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $v) {
				$cont_id[] = $v['id'];
			}
			$comp_id = companies_add($cont_id,$link);
			leads_cust_add($cont_id, $comp_id, CUST_TYPE, $link);
			leads_cust_add($cont_id, $comp_id, LEADS_TYPE, $link);
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
	}
};


//добавляем заданное количество компаний и возвращаем их id
function companies_add(array $cont_id, $link){ 
	foreach ($cont_id as $v) {
		$data['add'][] = [
			'name' => mt_rand(),
			'contacts_id'=> [
				'0'=> $v
						]
				];
	}
	$links = $link.'/api/v2/companies';
	$result = req_curl($links, $data);
	if (is_array($result)){
		$result = $result['_embedded']['items'];
		foreach ($result as $v) {
			$comp_id[] = $v['id'];
		}
	} else {

		throw new Exception('Сервер прислал неожиданный ответ', 007);
	}
	return $comp_id;

};

//добавляет сделки или покупателей в зависимости от выбранного типа
function leads_cust_add(array $cont_id, array $comp_id, $type,$link){
	if ($type === LEADS_TYPE) {
		$links = $link."/api/v2/leads";
	} else {
		$links = $link."/api/v2/customers";
	}
	$data = array();
	foreach ($cont_id as $k => $v) {
		$data['add'][] = [
			'name' => mt_rand(),
			'company_id' => $comp_id[$k],
			'contacts_id'=> [
				'0' => $v
			]
		];
	}
	$result = req_curl($links, $data);
};


//основной код скрипта
function amo_mass_create($num,$link, $mail, $hash, $max_row){
	amo_aut($link, $mail, $hash);
	$multi_id = multitext_add($link,$hash);
	$enums_id = multitext_find($multi_id, $link);
	contacts_get($multi_id, $enums_id, $link, $max_row);
	contacts_add($num, $multi_id, $enums_id, $link, $max_row);
	echo "готово";
};

try {
	include 'amo_aut.php';
	$multitext = new multitext($config['link'],$config['hash']);
	echo $multitext->get_id().PHP_EOL;
	echo var_dump($multitext->get_enums()).PHP_EOL;
	//amo_mass_create(data_clean($_POST['num']), $config['link'], $config['mail'], $config['hash'], $config['max_row']);
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}






