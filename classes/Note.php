<?php
class Note{
	private $_type;
	private $_val;
	private $_id;
	private $_origin;
	private $_call_link = 'http://example.com/calls/1.mp3';
	private $_call_duration = 30;

	public function get_call_link(){
		return $this->_call_link;
	}

	public function get_call_duration(){
		return $this->_call_duration;
	}

	public function set_origin($origin){
		$this->_origin = $origin;
	}
	public function get_origin(){
		return $this->_origin;
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
}

