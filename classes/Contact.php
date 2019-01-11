<?php
class Contact{
	private $_id;
	private $_name;
	const ELEM_TYPE = 1;

	public function __construct($name =NULL, $id = NULL){
		$this->_id = $id;
		$this->_name = $name;
	}

	public function get_type(){
		return self::ELEM_TYPE;
	}

	public function get_id(){
		return $this->_id;
	}

	public function get_name(){
		return $this->_name;
	}

}


