<?php
class AmoConstruct extends CurlReq {
	private $_link;
	private $_hash;
	private $_max_row;
	private $_elem_links = [1 => 'contacts',
							2 => 'leads',
							3 => 'companies',
							12 => 'customers',
							20 => 'account',
							22 => 'notes',
							25 => 'tasks',
							30 => 'fields'
	];
	private $_field_types = ['text' => 1,
							'multiselect' => 5
	];


	public function auth($akk, $mail, $hash, $max_row){
		$this->_hash = $hash;
		$this->_link = "https://".$akk.".amocrm.ru/";
		$this->_max_row = $max_row;
		$data = [
			'USER_LOGIN' => $mail, 
			'USER_HASH' => $this->_hash
		];
		$link = $this->_link."private/api/auth.php?type=json";
		$result = $this->add($link, $data);
		$result = $result['response'];
		if (!isset($result['auth'])) {
			throw new Exception('Авторизация не прошла', 666);
		}
	}

	public function createField($elem_type, Field $field){
		$origin = $this->_hash.'_'.time().'_'.mt_rand();
		$field_id = NULL;
		$enums = NULL;
		$data['add'] = [
			[
				'name' => $field->get_name(),
				'field_type'=> $field->get_type(),
				'element_type' => $elem_type,
				'origin' => $origin,
				'enums' => $field->get_enums(),
				'is_editable' => 1
			]
		];
		$result = $this->add($this->_link.'api/v2/'.$this->_elem_links[$field->get_elem_type()], $data);
		$field_id = NULL;
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach($result as $v){
				$field_id = $v['id'];
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
		if ($field->get_type() === $this->_field_types['multiselect']){
			$result = $this->accReq(['custom_fields']);
			if (is_array($result)) {
				$result = $result['_embedded']['custom_fields'][$this->_elem_links[$elem_type]][$field_id]['enums'];
				foreach ($result as $key => $value) {
					$enums[$key] = $value;
				}
			} else {
				throw new Exception('Сервер прислал неожиданный ответ', 7);
			}
		}
		return $field = new Field($field->get_type(), $field->get_name(), $enums, $field_id, $origin);
	}

	private function accReq(array $params = null){
		$link = $this->_link.'api/v2/'.$this->_elem_links[Acc::ELEM_TYPE];
		if (!is_null($params)){
			$link .= '?with=';
			foreach ($params as $key => $value) {
				$link .= $value.',';
			}
			$link = substr($link, 0, -1);
		}
		return $this->get($link);
	}

	public function changeFieldVal($elem, $val, Field $field){
		if ($field->get_type() !== $this->_field_types['multiselect']) {
			$val = [
				'value' => $val
			];
		}
		$data['update'][] = [
			'id' => $elem->get_id(),
			'updated_at' => time(),
			'custom_fields' => [
				[
					'id' => $field->get_id(),
					'values' => [
						$val
					]
				]
			]
		];
		$result = $this->add($this->_link.'api/v2/'.$this->_elem_links[$elem->get_type()], $data);
		if (isset($result['_embedded']['errors']['update'][$elem->get_id()])) {
			throw new Exception($result['_embedded']['errors']['update'][$elem->get_id()], 6);
		} elseif (!isset($result['_embedded']['items'])) {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
	}

	public function massChangeMultisVal($elem_type, Field $field){
		$limit_offset = 0;
		do {
			$cont_id = [];
			$link = $this->_link.'api/v2/'.$this->_elem_links[$elem_type].'?limit_rows='.$this->_max_row.'&limit_offset='.$limit_offset;
			$result = $this->get($link);
			if (is_array($result)) {
				$result = $result['_embedded']['items'];
				foreach ($result as $key => $value) {
					$enums_data = [];
					foreach ($field->get_enums() as $val) {
						if (mt_rand(0, 1) === 1) {
							$enums_data[] = $val;
						}	
					}
					$data['update'][] = [
						'id' => $value['id'],
						'updated_at'=> time(),
						'custom_fields' => [
							'0' => [
								'id' => $field->get_id(),
								'values' => $enums_data
							]
						]
					];
					$result = $this->add($this->_link.'api/v2/'.$this->_elem_links[$elem_type], $data);
				};
			}
			$limit_offset += $this->_max_row;
		} while (is_array($result));
	}

	public function massCreateElem($num){
		$max_row = $this->_max_row;
		for ($i = $num, $n = 0; $i > 0; $i -= $max_row, $n++) {
			$cont = [];
			$data = [];
			$name = [];
			if ($i > $max_row) {
				$col = $max_row;
			} else {
				$col = $i;
			}
			for ($j = 0; $j < $col; $j++) {
				$name[]=mt_rand();
				$data['add'][] = [
					'name' => end($name),
				];
			};
			$result = $this->add($this->_link.'api/v2/'.$this->_elem_links[Contact::ELEM_TYPE], $data);
			if (is_array($result)) {
				$result = $result['_embedded']['items'];
				foreach ($result as $k => $v) {
					$cont_id[] = new Contact($name[$k], $v['id']);
				}
				$company = $this->massCreateCompany($cont_id);
				$this->massCreateLead($cont_id, $company);
				$this->massCreateCustomer($cont_id, $company);
			} else {
				throw new Exception('Сервер прислал неожиданный ответ1', 007);
			}
		}
	}

	private function massCreateCompany(array $cont_id){
		$comp_id = [];
		$name = [];
		foreach ($cont_id as $v) {
			$name[] = mt_rand();
			$data['add'][] = [
				'name' => end($name),
				'contacts_id'=> [
					'0'=> $v->get_id()
				]
			];
		}
		$result = $this->add($this->_link.'api/v2/'.$this->_elem_links[Company::ELEM_TYPE], $data);
		if (is_array($result)){
			$result = $result['_embedded']['items'];
			foreach ($result as $k => $v) {
				$comp_id[] = new Company($name[$k], $v['id'], [$cont_id[$k]->get_id()]);
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 17);
		}
		return $comp_id;
	}

	private function massCreateCustLead(array $cont_id, array $comp_id, $elem_type){
		$data = [];
		foreach ($cont_id as $k => $v) {
			$data['add'][] = [
				'name' => mt_rand(),
				'company_id' => $comp_id[$k]->get_id(),
				'contacts_id'=> [
					'0' => $v->get_id()
				]
			];
		}
		$result = $this->add($this->_link.'api/v2/'.$this->_elem_links[$elem_type], $data);
		if (!is_array($result)){
			throw new Exception('Сервер прислал неожиданный ответ', 037);
		}
	}

	private function massCreateLead(array $cont_id, array $comp_id){
		$this->massCreateCustLead($cont_id, $comp_id, Lead::ELEM_TYPE);
	}

	private function massCreateCustomer(array $cont_id, array $comp_id){
		$this->massCreateCustLead($cont_id, $comp_id, Customer::ELEM_TYPE);
	}

	public function findFirsField($elem, Field $field){
		$id = NULL;
		$enums = NULL;
		$result = $this->accReq(['custom_fields']);
		if (is_array($result)) {
			$result = $result['_embedded']['custom_fields'][$this->_elem_links[$elem->get_type()]];
			foreach ($result as $key => $value) {
				if ($value['field_type'] === $field->get_type()) {
					$id = $key;
					$name = $value['name'];
					if (isset($value['enums'])){
						$enums = $value['enums'];
					}
					return $field = new Field($field ->get_type(), $name, $enums, $id);
					break;
				}
			} 
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
		if (is_null($id)){
		return $field = $this->createField($field->get_type(), $field);
		} 
	}

	public function createNote($elem, Note $note){
		$data['add'][] = [
			'element_id' => $elem->get_id(),
			'element_type' => $elem->get_type(),
			'note_type' => $note->get_type()
		];
		switch ($note->get_type()) {
			case '4':
				$data['add'][0]['text'] = $note->get_val();
				break;
			case '10':
				$data['add'][0]['params']['UNIQ'] = $this->_hash.'_'.time().'_'.mt_rand();
				$data['add'][0]['params']['DURATION'] = 30;
				$data['add'][0]['params']['SRC'] = 'http://example.com/calls/1.mp3';
				$data['add'][0]['params']['LINK'] = 'http://example.com/calls/1.mp3';
				$data['add'][0]['params']['PHONE'] = $note->get_val();
				break;
			default:
				$data['add'][0]['text'] = $note->get_val();
				break;
		}
		$result = $this->add($this->_link.'api/v2/'.$this->_elem_links[$note->get_elem_type()], $data);
		if (isset($result['_embedded']['items'][0]['id']) && $result['_embedded']['items'][0]['id'] === 0) {		
			throw new Exception('не удалось найти элемент', 006);
		} elseif (!isset($result['_embedded']['items'])) {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
	}

	public function getTaskTypes(){
		$tasks = [];
		$result = $this->accReq(['task_types']);
		$result = $result['_embedded']['task_types'];
		foreach ($result as $key => $value) {
			$tasks[$value['id']] = $value['name'];
		}
		return $tasks;
	}

	public function createTask($elem, Task $task){
		$data['add'][] = [
			'element_id' => $elem->get_id(),
			'element_type' => $elem->get_type(),
			'task_type' => $task->get_type(),
			'complete_till' => $task->get_date(),
			'text' => $task->get_val()
		];
		$result = $this->add($this->_link.'api/v2/'.$this->_elem_links[$task->get_elem_type()], $data);
	}

	public function closeTask(Task $task){
		$data['update'][] = [
			'id' => $task->get_id(),
			'is_completed' => 1,
			'updated_at' => time(),
			'text' => $task->get_val()
		];
		$result = $this->add($this->_link.'api/v2/'.$this->_elem_links[$task->get_elem_type()], $data);
	}
}
