<?php
class Company extends AmoElem{
	private $_contacts;
	const ELEM_TYPE = 3;

	public function set_contacts(array $contacts){
		$this->_contacts = $contacts;
		return $this;
	}
	public function get_contacts(){
		return $this->_contacts;
	}
}

