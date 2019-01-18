<?php
class Task{
	private $_creator;
	private $_task_type;
	private $_val;
	private $_date;
	const LINK = 'tasks';

	public function __construct($curl){
		$this->_creator = $curl;
	}

	public function close($id, $text){
		$data['update'][] = [
			'id' => $id,
			'is_completed' => 1,
			'updated_at' => time(),
			'text' => $text
		];
		$result = $this->_creator->add(self::LINK, $data);
	}

	public function set($elem_id, $elem_type, $task_type, $text, $datetime){
		$this->_val = $text;
		$this->_task_type = $task_type;
		$this->_date = $datetime;

		$data['add'][] = [
			'element_id' => $elem_id,
			'element_type' => $elem_type,
			'task_type' => $this->_task_type,
			'complete_till' => $this->_date,
			'text' => $this->_val
		];
		$result = $this->_creator->add(self::LINK, $data);
	}

	public function getTaskTypes(){
        $task = [];
		$result = Acc::get($this->_creator, ['task_types']);
		$result = $result['_embedded']['task_types'];
		foreach ($result as $key => $value) {
			$task[$value['id']] = $value['name'];
		}
		return $task;
	}
}

