<?php
class Contact{
	private $_creator;
	private $_id;
	const TYPE = 1;
	const LINK = 'contacts';
	public function __construct($curl){
		$this->_creator = $curl;
	}

	public function set($id){
		$this->_id = $id;
	}

    public function massChangeFieldVal($field){
		if (is_object($field)){
			$field->massChangeVal();
		} else {
			throw new Exception('При изменении поля объект не получен', 077);
		}
	}

	public function addTask($task_type, $text, $datetime){
		$task = new Task($this->_creator);
		$task->set($this->_id, self::TYPE, $task_type, $text, $datetime);
		return $task;
	}

	public function addNote($note_type, $val){
		$note = new Note($this->_creator);
		$note->set($this->_id, self::TYPE, $note_type, $val);
		return $note;
	}

	public function addField($field_type, $name, $id = NULL, array $enums_val = NULL){
		$field = new Field($this->_creator);
		$field->set(self::LINK, self::TYPE, $field_type, $name, $id, $enums_val);
		return $field;
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

	public function massCreate($num){
		$company = new Company($this->_creator);
		$lead = new Lead($this->_creator);
		$cust = new Customer($this->_creator);

		$max_row = $this->_creator->get_max_row();
		for ($i = $num, $n = 0; $i > 0; $i -= $this->_creator->get_max_row(), $n++) {
			$cont_id = array();
			$data = array();
			$name = array();
			if ($i > $max_row) {
				$col = $max_row;
			} else {
				$col = $i;
			}
			for ($j = 0; $j < $col; $j++) {
				$data['add'][] = [
					'name' => mt_rand(),
				];
			};
			$result = $this->_creator->add(self::LINK, $data);
			if (is_array($result)) {
				$result = $result['_embedded']['items'];
				foreach ($result as $k => $v) {
					$cont_id[] = $v['id'];
				}
				$comp_id = $company->massCreate($cont_id);
				$lead->massCreate($cont_id, $comp_id);
				$cust->massCreate($cont_id, $comp_id);
			} else {
				throw new Exception('Сервер прислал неожиданный ответ1', 007);
			}
		}
	}
}


