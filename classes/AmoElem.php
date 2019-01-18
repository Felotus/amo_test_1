<?php
abstract class AmoElem {
	protected $_id;
	protected $_name;
	protected $_custom_fields;

	public function get_custom_fields(){
		return $this->_custom_fields;
	}

	public function set_custom_fields(array $custom_fields){
		$this->_custom_fields = $custom_fields;
		return $this;
	}

	public function set_id($id){
		$this->_id = $id;
		return $this;
	}

	public function set_name($name){
		$this->_name = $name;
		return $this;
	}

	public function get_id(){
		return $this->_id;
	}

	public function get_name(){
		return $this->_name;
	}

	public function get_type(){
		return static::ELEM_TYPE;
	}
}