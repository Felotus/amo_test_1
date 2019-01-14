<?php
class Note{
	private $_type;
	private $_val;
	private $_id;
	private $_origin;

	public function set_origin($origin){
		$this->_origin = $origin;
	}

	public function set_id($id){
		$this->_id = $id;
	}
	public function set_val($val){
		$this->_val = $val;
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
	public function get_type(){
		return $this->_type;
	}

	public function get_origin($origin){
		return $this->_origin;
	}
}

