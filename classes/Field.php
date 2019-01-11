<?php
class Field{
	private $_id;
	private $_name;
	private $_enums;
	private $_origin;
	private $_type;
	const ELEM_TYPE = 30;

	public function __construct($field_type, $name = null, array $enums_val = NULL, $id = NULL, $origin = NULL) {
		$this->_type = $field_type;
		$this->_name = $name;
		$this->_id = $id;
		$this->_enums = $enums_val;
		$this->_origin = $origin;
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

	public function get_elem_type(){
		return self::ELEM_TYPE;
	}
}


