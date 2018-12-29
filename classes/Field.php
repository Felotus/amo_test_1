<?php
class Field{
	private $_id;
	private $_elem_type;
	private $_name;
	private $_enums;
	private $_origin;
	private $_type;
	private $_creator;
	const LINK = 'fields';
	public function set($curl, $elem_link, $elem_type, $field_type, $name, array $enums_val = null){
		$this->_creator = $curl;
		$this->_type = $field_type;
		$this->_elem_type = $elem_type;
        $this->_origin = $this->_creator->get_hash()."_".time().mt_rand();
		$fields['add'] = [
			[
				'name' => $name,
				'field_type'=> $this->_type,
				'element_type' => $this->_elem_type,
				'origin' => $this->_origin,
				'is_editable' => 0,
				'enums' => $enums_val
            ]
		];
		$result = $this->_creator->add(self::LINK, $fields);
        $multi_id=0;
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach($result as $v){
                $multi_id = $v['id'];
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
		if (!is_null($enums_val)){
			$result = Acc::get($this->_creator, ['custom_fields']);
			if (is_array($result)) {
				$result = $result['_embedded']['custom_fields'][$elem_link][$multi_id]['enums'];
			foreach ($result as $key => $value) {
				$enums[$key] = $value;
			}
			} else {
				throw new Exception('Сервер прислал неожиданный ответ', 007);
			}
			$this->_enums = $enums;
		}
		$this->_id = $multi_id;
		$this->_name = $name;	
	}
	public function get_elem_type(){
		return $this->_elem_type;
	}
	public function get_id(){
		return $this->_id;
	}
	public function get_enums(){
		return $this->_enums;
	}
	public function get_name(){
		return $this->_name;
	}
	public function get_origin(){
		return $this->_origin;
	}
	public function get_type(){
		return $this->_type;
	}
}