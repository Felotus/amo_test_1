<?php
class Note{
	private $_type;
	private $_val;
	private $_id;
	const ELEM_TYPE = 22;

	public function __construct($type = NULL, $val = NULL, $id = NULL){
		$this->_id = $id;
		$this->_val = $val;
		$this->_type = $type;
	}

	public function get_elem_type(){
		return self::ELEM_TYPE;
	}

	public function get_id(){
		return $this->_id;
	}

	public function get_val(){
		return $this->_val;
	}
	public function get_type(){
		return $this->_type;
	}
}

