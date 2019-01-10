<?php
class Company{
	private $_creator;
	private $_id;
	const TYPE = 3;
	const LINK = 'companies';
	public function __construct($curl){
		$this->_creator = $curl;
	}

	public function set($id){
		$this->_id = $id;
	}

	public function addTask($task_type, $text, $datetime){
		$task = new Task($this->_creator);
		$task->set($this->_id, self::TYPE, $task_type, $text, $datetime);
		return $task;
	}

	public function addField($field_type, $name, $id = NULL, array $enums_val = NULL){
		$field = new Field($this->_creator);
		$field->set(self::LINK, self::TYPE, $field_type, $name, $id, $enums_val);
		return $field;
	}

	public function addNote($note_type, $val){
		$note = new Note($this->_creator);
		$note->set($this->_id, self::TYPE, $note_type, $val);
		return $note;
	}

	public function changeFirsField($field_type, $val, $name = 'new_field'){
		$id = 0;
		$result = Acc::get($this->_creator, ['custom_fields']);
		if (is_array($result)) {
			$result = $result['_embedded']['custom_fields'][self::LINK];
			foreach ($result as $key => $value) {
				if ($value['field_type'] === $field_type) {
					$id = $key;
					$name = $value['name'];
					break;
				}
			} 
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
		if ($id === 0){
			$field = $this->addField($field_type, $name);
		} else {
			$field = $this->addField($field_type, $name, $id);
		}
		$field->changeVal($this->_id, self::LINK, $val);
	}

	public function massCreate(array $cont_id){
        $comp_id = array();
		foreach ($cont_id as $v) {
			$data['add'][] = [
				'name' => mt_rand(),
				'contacts_id'=> [
					'0'=> $v
				]
			];
		}
		$result = $this->_creator->add(self::LINK, $data);
		if (is_array($result)){
			$result = $result['_embedded']['items'];
			foreach ($result as $v) {
				$comp_id[] = $v['id'];
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 17);
		}
		return $comp_id;
	}

}
