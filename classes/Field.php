<?php
class Field{
	private $_id;
	private $_name;
	private $_enums;
	private $_origin;
	private $_type;
	
	public function set_id($id){
		$this->_id = $id;
	}
	public function set_name($name){
		$this->_name = $name;
	}
	public function set_enums($enums){
		$this->_enums = $enums;
	}
	public function set_origin($origin){
		$this->_origin = $origin;
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


