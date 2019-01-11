<?php
class Company{
	private $_id;
	private $_name;
	private $_contacts;
	const ELEM_TYPE = 3;

	public function __construct($name = NULL, $id = NULL, array $contacts = NULL){
		$this->_id = $id;
		$this->_name = $name;
		$this->_contacts = $contacts;
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

