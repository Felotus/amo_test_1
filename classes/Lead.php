<?php
class Lead extends AmoElem{
	private $_contacts;
	private $_company;
	const ELEM_TYPE = 2;

	public function set_contacts(array $contacts){
		$this->_contacts = $contacts;
	}
	public function set_company($company){
		$this->_company = $company;
	}

	public function get_contacts(){
		return $this->_contacts;
	}
	public function get_company(){
		return $this->_company;
	}
}

