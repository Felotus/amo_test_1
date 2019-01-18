<?php
class Note{
	private $_type;
	private $_val;
	private $_id;
	private $_origin;
	private $_call_link;
	private $_call_duration;
	const TYPE_NOTE = 4;
	const TYPE_IN_CALL = 10;

	public function set_call_link($value){
		$this->_call_link = $value;
		return $this;
	}

	public function set_call_duration($value){
		$this->_call_duration = $value;
		return $this;
	}

	public function get_call_link(){
		return $this->_call_link;
	}

	public function get_call_duration(){
		return $this->_call_duration;
	}

	public function set_origin($origin){
		$this->_origin = $origin;
		return $this;
	}
	public function get_origin(){
		return $this->_origin;
	}

	public function set_id($id){
		$this->_id = $id;
		return $this;
	}
	public function set_val($val){
		$this->_val = $val;
		return $this;
	}
	
	public function set_type($type){
		$this->_type = $type;
		return $this;
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

