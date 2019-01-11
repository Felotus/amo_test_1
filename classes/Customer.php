<?php
class Customer{
	private $_name;
	private $_id;
	private $_contacts;
	private $_company;
	const ELEM_TYPE = 12;

	public function __construct($name = NULL, $id = NULL, $contacts = NULL, $company = NULL){
		$this->_id = $id;
		$this->_name = $name;
		$this->_contacts = $contacts;
		$this->_company = $company;
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

