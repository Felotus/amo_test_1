<?php
class Task{
	private $_id;
	private $_type;
	private $_val;
	private $_date;
	const COMPLITED = 1;

	public function set_id($id){
		$this->_id = $id;
	}
	public function set_val($val){
		$this->_val = $val;
	}
	public function set_date($date){
		$this->_date = $date;
	}
	public function set_type($type){
		$this->_type = $type;
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

