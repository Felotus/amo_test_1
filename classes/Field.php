<?php
class Field{
	private $_id;
	private $_elem_type;
	private $_elem_link;
	private $_name;
	private $_enums;
	private $_origin;
	private $_type;
	private $_creator;
	private $_val;
	const LINK = 'fields';

	public function __construct($curl){
		$this->_creator = $curl;
	}

	public function get_id(){
		return $this->_id;
	}

	public function get_type(){
		return $this->_type;
	}

	public function set($elem_link, $elem_type, $field_type, $name, $id = NULL, array $enums_val = NULL){
		$this->_type = $field_type;
		$this->_elem_link = $elem_link;
		$this->_elem_type = $elem_type;
		$this->_name = $name;
        $this->_origin = $this->_creator->get_hash()."_".time().mt_rand();
        if (is_null($id)) {
			$fields['add'] = [
				[
					'name' => $this->_name,
					'field_type'=> $this->_type,
					'element_type' => $this->_elem_type,
					'origin' => $this->_origin,
					'enums' => $enums_val,
					'is_editable' => 1
	            ]
			];
			$result = $this->_creator->add(self::LINK, $fields);
	        $field_id=0;
			if (is_array($result)) {
				$result = $result['_embedded']['items'];
				foreach($result as $v){
	                $field_id = $v['id'];
				}
			} else {
				throw new Exception('Сервер прислал неожиданный ответ', 7);
			}
			if (!is_null($enums_val)){
				$result = Acc::get($this->_creator, ['custom_fields']);
				if (is_array($result)) {
					$result = $result['_embedded']['custom_fields'][$this->_elem_link][$field_id]['enums'];
				foreach ($result as $key => $value) {
					$enums[$key] = $value;
				}
				} else {
					throw new Exception('Сервер прислал неожиданный ответ', 7);
				}
				$this->_enums = $enums;
			}
			$this->_id = $field_id;
		} else {
			$this->_id = $id;
		}
	}

	public function changeVal($id, $link, $val){
		$this->_val = $val;
		if ($this->_type !== 5) {
			$val = array('value' => $val);
		}
		$data['update'][] = [
			'id' => $id,
			'updated_at' => time(),
			'custom_fields' => [
				[
					'id' => $this->_id,
					'values' => [
						$val
				    ]
                ]
			]
		];
		$result = $this->_creator->add($link, $data);
		if (isset($result['_embedded']['errors']['update'][$id])) {		
			throw new Exception($result['_embedded']['errors']['update'][$id], 6);
		} elseif (!isset($result['_embedded']['items'])) {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
	}

	public function massChangeVal(){
		$limit_offset = 0;
		do {
			$cont_id = array();
			$links = $this->_elem_link.'?limit_rows='.$this->_creator->get_max_row().'&limit_offset='.$limit_offset;
			$result = $this->_creator->get($links);
			if (is_array($result)) {
				$result = $result['_embedded']['items'];
				foreach ($result as $key => $value) {
					$enums_data = array();
					foreach ($this->_enums as $val) {
						if (mt_rand(0, 1) === 1) {
							$enums_data[] = $val;
						}	
					}
					$data['update'][] = [
						'id' => $value['id'],
						'updated_at'=> time(),
						'custom_fields' => [
							'0' => [
								'id' => $this->_id,
								'values' => $enums_data
							]
						]
					];
					$result = $this->_creator->add($this->_elem_link, $data);
				};
			}
			$limit_offset += $this->_creator->get_max_row();
		} while (is_array($result));	
	}
}