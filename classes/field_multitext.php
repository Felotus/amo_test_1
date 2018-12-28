<?php
class field_multitext {
	private $_id;
	private $_elem_type;
	private $_value;
	private $_enums;
	private $_enums_val;
	const FIELD_TYPE = 5;
	function  __construct($id, $name, $elem_type, array $enums, array $enums_val){
		$this->_id = $id;
		$this->_elem_type = $elem_type;
		$this->_name = $name;
		$this->_enums = $enums;
		$this->_enums_val = $enums_val;
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
	public function get_enums_val(){
		return $this->_enums_val;
	}
}
