<?php
class AmoConstruct extends CurlReq {
	private $_link;
	private $_hash;
	private $_max_row;
	private $_elem_links = [
		1 => 'contacts',
		2 => 'leads',
		3 => 'companies',
		12 => 'customers',
	];
	private $_field_types = [
		'text' => 1,
		'multiselect' => 5
	];

    /**
     * @return mixed
     */
    public function get_max_row(){
		return $this->_max_row;
	}

    /**
     * @param $akk
     * @param $mail
     * @param $hash
     * @param $max_row
     * @throws Exception
     */
    public function auth($akk, $mail, $hash, $max_row){
		$this->_hash = $hash;
		$this->_link = "https://".$akk.".amocrm.ru/";
		$this->_max_row = $max_row;
		$data = [
			'USER_LOGIN' => $mail, 
			'USER_HASH' => $this->_hash
		];
		$link = $this->_link."private/api/auth.php?type=json";
		$result = $this->post($link, $data);
		$result = $result['response'];
		if (!isset($result['auth'])) {
			throw new Exception('Авторизация не прошла', 666);
		}
	}

    /**
     * @param $elem_type
     * @param Field $field
     * @return Field
     * @throws Exception
     */
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
		$result = $this->post($this->_link.'api/v2/fields', $data);
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
		$field->set_enums($enums);
		$field->set_id($field_id);
		$field->set_origin($origin);
		return $field;
	}

    /**
     * @param array|null $params
     * @return mixed
     */
    private function accReq(array $params = null){
		$link = $this->_link.'api/v2/account';
		if (!is_null($params)){
			$link .= '?with=';
			foreach ($params as $key => $value) {
				$link .= $value.',';
			}
			$link = substr($link, 0, -1);
		}
		return $this->get($link);
	}

    /**
     * @param AmoElem $elem
     * @param $val
     * @param Field $field
     * @throws Exception
     */
    public function changeFieldVal(AmoElem $elem, $val, Field $field){
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
		$result = $this->post($this->_link.'api/v2/'.$this->_elem_links[$elem->get_type()], $data);
		if (isset($result['_embedded']['errors']['update'][$elem->get_id()])) {
			throw new Exception($result['_embedded']['errors']['update'][$elem->get_id()], 6);
		} elseif (!isset($result['_embedded']['items'])) {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
	}

    /**
     * @param $elem_type
     * @param Field $field
     */
    public function massChangeMultisVal($elem_type, Field $field){
		$limit_offset = 0;
		do {
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
					$result = $this->post($this->_link.'api/v2/'.$this->_elem_links[$elem_type], $data);
				};
			}
			$limit_offset += $this->_max_row;
		} while (is_array($result));
	}

    /**
     * @param array $elems AmoElem
     * @return array
     * @throws Exception
     */
    public function createElems(array $elems){
		$data = [];
		foreach ($elems as $k => $v) {
			$data['add'][$k]['name'] = $elems[$k]->get_name();
			if (property_exists($elems[$k], '_company')){
				$data['add'][$k]['company_id'] = $elems[$k]->get_company();
			}
			if (property_exists($elems[$k], '_contacts')){
				$data['add'][$k]['contacts_id'] = $elems[$k]->get_contacts();
			}
		}
		$result = $this->post($this->_link.'api/v2/'.$this->_elem_links[$elems[0]->get_type()], $data);
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $k => $v) {
				$elems[$k]->set_id($v['id']);
			}
			return $elems;
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
	}


    /**
     * @param array $elems AmoElem
     * @return array
     * @throws Exception
     */
    public function createCustLeads(array $elems){
		$data = [];
		foreach ($elems as $k => $v) {
			$data['add'][$k] = [
				'name' => $elems[$k]->get_name(),
				'company_id' => $elems[$k]->get_company(),
				'contacts_id'=> $elems[$k]->get_contacts()
			];
		}
		$result = $this->post($this->_link.'api/v2/'.$this->_elem_links[$elems[0]->get_type()], $data);
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $k => $v) {
				$elems[$k]->set_id($v['id']);
			}
			return $elems;
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
	}

    /**
     * @param array $elems AmoElem
     * @return array
     * @throws Exception
     */
    public function createCompanies(array $elems){
		$data = [];
		foreach ($elems as $k => $v) {
			$data['add'][] = [
				'name' => $elems[$k]->get_name(),
				'contacts_id'=> $elems[$k]->get_contacts()
			];
		}
		$result = $this->post($this->_link.'api/v2/companies', $data);
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $k => $v) {
				$elems[$k]->set_id($v['id']);
			}
			return $elems;
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
	}

    /**
     * @param array $elems AmoElem
     * @return array
     * @throws Exception
     */
    public function createContacts(array $elems){
		$data = [];
		foreach ($elems as $k => $v) {
			$data['add'][] = [
				'name' => $elems[$k]->get_name()
			];
		}
		$result = $this->post($this->_link.'api/v2/contacts', $data);
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $k => $v) {
				$elems[$k]->set_id($v['id']);
			}
			return $elems;
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
	}

    /**
     * @param AmoElem $elem
     * @param Field $field
     * @return Field
     * @throws Exception
     */
    public function findFirsField(AmoElem $elem, Field $field){
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
					$field->set_name($name);
					$field->set_enums($enums);
					$field->set_id($id);
					return $field;
					break;
				}
			} 
		} else {
			throw new Exception('Сервер прислал неожиданный ответ',7);
		}
		if (is_null($id)){
			return $field = $this->createField($elem->get_type(), $field);
		} 
	}

    /**
     * @param AmoElem $elem
     * @param Note $note
     * @throws Exception
     */
    public function createNote(AmoElem $elem, Note $note){
		$note->set_origin($this->_hash.'_'.time().'_'.mt_rand());
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
                $data['add'][0]['params']['UNIQ'] = $note->get_origin();
				$data['add'][0]['params']['DURATION'] = 30;
				$data['add'][0]['params']['SRC'] = 'http://example.com/calls/1.mp3';
				$data['add'][0]['params']['LINK'] = 'http://example.com/calls/1.mp3';
				$data['add'][0]['params']['PHONE'] = $note->get_val();
				break;
			default:
				$data['add'][0]['text'] = $note->get_val();
				break;
		}
		$result = $this->post($this->_link.'api/v2/notes', $data);
		if (isset($result['_embedded']['items'][0]['id']) && $result['_embedded']['items'][0]['id'] === 0) {		
			throw new Exception('не удалось найти элемент', 6);
		} elseif (!isset($result['_embedded']['items'])) {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
	}

    /**
     * @return array
     */
    public function getTaskTypes(){
		$tasks = [];
		$result = $this->accReq(['task_types']);
		$result = $result['_embedded']['task_types'];
		foreach ($result as $key => $value) {
			$tasks[$value['id']] = $value['name'];
		}
		return $tasks;
	}

    /**
     * @param AmoElem $elem
     * @param Task $task
     */
    public function createTask(AmoElem $elem, Task $task){
		$data['add'][] = [
			'element_id' => $elem->get_id(),
			'element_type' => $elem->get_type(),
			'task_type' => $task->get_type(),
			'complete_till' => $task->get_date(),
			'text' => $task->get_val()
		];
		$this->post($this->_link.'api/v2/tasks', $data);
	}

    /**
     * @param Task $task
     */
    public function closeTask(Task $task){
        $data['update'][] = [
			'id' => $task->get_id(),
			'is_completed' => 1,
			'updated_at' => time(),
			'text' => $task->get_val()
		];
        $this->post($this->_link.'api/v2/tasks', $data);
	}
}
