<?php
class Task{
	private $_id;
	private $_type;
	private $_val;
	private $_date;
	const ELEM_TYPE = 25;

	public function __construct($type = NULL, $val = NULL, $date = NULL, $id  = NULL){
		$this->_id = $id;
		$this->_type = $type;
		$this->_date = $date;
		$this->_val = $val;
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

	public function get_date(){
		return $this->_date;
	}

	public function get_type(){
		return $this->_type;
	}

}

