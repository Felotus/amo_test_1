<?php
class Field{
	private $_id;
	private $_name;
	private $_enums;
	private $_origin;
	private $_type;
	private $_editable = 1;
	private $_values;
	const TEXT = 1;
	const MULTISELECT = 5;



	public function set_values(array $values){
		$this->_values = $values;
	}
	
	public function get_values(){
		return $this->_values;
	}

	public function set_origin($origin){
		$this->_origin = $origin;
	}
	
	public function set_editable($editable){
		$this->_editable = $editable;
	}
	public function get_editable(){
		return $this->_editable;
	}

	public function set_id($id){
		$this->_id = $id;
	}
	public function set_name($name){
		$this->_name = $name;
	}
	public function set_enums($enums){
		$this->_enums = $enums;
	}
	public function set_type($type){
		$this->_type = $type;
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


