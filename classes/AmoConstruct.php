<?php
class AmoConstruct extends CurlReq {
	private $_link;
	private $_hash;
	private $_mail;
	private $_elem_links = [
		1 => 'contacts',
		2 => 'leads',
		3 => 'companies',
		12 => 'customers',
	];


    /**
     * @param sting $akk
     * @param sting $mail
     * @param sting $hash
     * @throws Exception
     */
    public function auth($akk, $mail, $hash){
		$this->_mail = $mail;
		$this->_hash = $hash;
		$this->_link = "https://".$akk.".amocrm.ru/";
		$data = [
			'USER_LOGIN' => $this->_mail, 
			'USER_HASH' => $this->_hash
		];
		$link = $this->_link."private/api/auth.php?type=json";
		$result = $this->post($link, $data);
		$result = $result['response'];
		if (!isset($result['auth'])) {
			return FALSE;
		} else {
			return TRUE;
		}

	}

	/**
	 * @param int $elem_type
	 * @param array Field $fields
	 * @return Field
	 * @throws Exception
	 */
	public function createFields($elem_type, array $fields){
		foreach ($fields as $value) {
			$data['add'][] = [
				'name' => $value->get_name(),
				'field_type'=> $value->get_type(),
				'element_type' => $elem_type,
				'origin' => $value->get_origin(),
				'enums' => $value->get_enums(),
				'is_editable' => $value->get_editable()
			];
		}
		$result = $this->post($this->_link.'api/v2/fields', $data);
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $k => $v) {
				$fields[$k]->set_id($v['id']);
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
		return $fields;
	}

	/**
	 * @param array|null $params
	 * @return mixed
	 */
	private function accReq(array $params = null){
		$link = $this->_link.'api/v2/account';
		if (!is_null($params)){
			$link .= '?with='.implode(',', $params);
		}
		implode(',', $params);
		return $this->get($link);
	}

    /**
     * @param int $first_row
     * @param int $max_row
     * @return array AmoElem
     */
    public function getContacts($first_row, $max_row){
		$elems = [];
		$link = $this->_link.'api/v2/contacts?limit_rows='.$max_row.'&limit_offset='.$first_row;
		$result = $this->get($link);

		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $key => $value) {
				$elem = new Contact();
				$elem->set_id($value['id']);
				$elems[] = $elem;
			}
			return $elems;
		} else {
			return FALSE;
		}
	}

    /**
     * @param int $first_row
     * @param int $max_row
     * @return array AmoElem
     */
    public function getleads($first_row, $max_row){
		$elems = [];
		$link = $this->_link.'api/v2/leads?limit_rows='.$max_row.'&limit_offset='.$first_row;
		$result = $this->get($link);

		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $key => $value) {
				$elem = new lead();
				$elem->set_id($value['id']);
				$elems[] = $elem;
			}
			return $elems;
		} else {
			return FALSE;
		}
	}

    /**
     * @param int $first_row
     * @param int $max_row
     * @return array AmoElem
     */
    public function getCustomer($first_row, $max_row){
		$elems = [];
		$link = $this->_link.'api/v2/customers?limit_rows='.$max_row.'&limit_offset='.$first_row;
		$result = $this->get($link);

		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $key => $value) {
				$elem = new Customer();
				$elem->set_id($value['id']);
				$elems[] = $elem;
			}
			return $elems;
		} else {
			return FALSE;
		}
	}

    /**
     * @param int $first_row
     * @param int $max_row
     * @return array AmoElem
     */
    public function getCompanies($first_row, $max_row){
		$elems = [];
		$link = $this->_link.'api/v2/companies?limit_rows='.$max_row.'&limit_offset='.$first_row;
		$result = $this->get($link);

		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach ($result as $key => $value) {
				$elem = new Company();
				$elem->set_id($value['id']);
				$elems[] = $elem;
			}
			return $elems;
		} else {
			return FALSE;
		}
	}


    /**
     * @param array $elems AmoElem
     * @throws Exception
     */
    public function updateElems(array $elems){
		foreach ($elems as $k => $v) {
            $custom_fields = [];
			foreach ($v->get_custom_fields() as $val){
				$values = $val->get_values();
				switch ($val->get_type()) {
					case Field::TEXT:
						$values[] = ['value' => $values[0]];
						break;
					
					case Field::MULTISELECT:
						break;

					default:
						throw new Exception('Неизвестный тип поля');
						break;
				}
				$custom_fields[] = [
					'id' => $val->get_id(),
					'values' => $values
				];
			}
			$data['update'][] = [
				'id' => $v->get_id(),
				'updated_at'=> time(),
				'custom_fields' => $custom_fields
			];
		}
		$this->post($this->_link.'api/v2/'.$this->_elem_links[$elems[0]->get_type()], $data);
	}

	/**
	 * @param array $elems AmoElem
	 * @return array AmoElem
	 * @throws Exception
	 */
	private function createElems(array $elems, array $data){
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
	 * @return array AmoElem
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
		return $this->createElems($elems, $data);
	}

	/**
	 * @param array $elems AmoElem
	 * @return array AmoElem
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
		return $this->createElems($elems, $data);
	}

	/**
	 * @param array $elems AmoElem
	 * @return array AmoElem
	 * @throws Exception
	 */
	public function createContacts(array $elems){
		$data = [];
		foreach ($elems as $k => $v) {
			$data['add'][] = [
				'name' => $elems[$k]->get_name()
			];
		}
		return $this->createElems($elems, $data);
	}



	public function getFields($elem_type){
		$id = NULL;
		$enums = NULL;
		$fields = [];
		$result = $this->accReq(['custom_fields']);
		if (is_array($result)) {
			$result = $result['_embedded']['custom_fields'][$this->_elem_links[$elem_type]];
			foreach ($result as $key => $value) {
				$name = $value['name'];
				if (isset($value['enums'])){
					$enums = $value['enums'];
				}
				$field = new Field();
				$field->set_name($value['name']);
				$field->set_enums($enums);
				$field->set_id($key);
				$field->set_type($value['field_type']);
				$fields[] = $field;
			} 
		} else {
			throw new Exception('Сервер прислал неожиданный ответ',7);
		}
		return $fields;
	}

	/**
	 * @param AmoElem $elem
	 * @param Note $note
	 * @throws Exception
	 */
	public function createNote(AmoElem $elem, Note $note){
		$data['add'][] = [
			'element_id' => $elem->get_id(),
			'element_type' => $elem->get_type(),
			'note_type' => $note->get_type()
		];
		switch ($note->get_type()) {
			case Note::TYPE_NOTE:
				$data['add'][0]['text'] = $note->get_val();
				break;
			case Note::TYPE_IN_CALL:
				$data['add'][0]['params']['UNIQ'] = $note->get_origin();
				$data['add'][0]['params']['DURATION'] = $note->get_call_duration();
				$data['add'][0]['params']['SRC'] = $note->get_call_link();
				$data['add'][0]['params']['LINK'] = $note->get_call_link();;
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
	 * @throws Exception
	 */
	public function createTask(AmoElem $elem, Task $task){
		$data['add'][] = [
			'element_id' => $elem-> get_id(),
			'element_type' => $elem->get_type(),
			'task_type' => $task->get_type(),
			'complete_till' => $task->get_date(),
			'text' => $task->get_val()
		];
		$result = $this->post($this->_link.'api/v2/tasks', $data);
		if (isset($result['_embedded']['items'][0]['id']) && $result['_embedded']['items'][0]['id'] === 0) {        
			throw new Exception('не удалось найти элемент', 6);
		} elseif (!isset($result['_embedded']['items'])) {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
	}


    /**
     * @param Task $task
     * @return bool
     * @throws Exception
     */
    public function updateTask(Task $task){
		$data['update'][] = [
			'id' => $task->get_id(),
			'is_completed' => Task::COMPLITED,
			'updated_at' => time(),
			'text' => $task->get_val()
		];
		$result = $this->post($this->_link.'api/v2/tasks', $data);
		if (isset($result['_embedded']['items'][0]['id']) && $result['_embedded']['items'][0]['id'] === 0) {        
			throw new Exception('не удалось найти элемент', 6);
		} elseif (!isset($result['_embedded']['items'])) {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		} else {
			return TRUE;
		}
	}

    /**
     * @param array $elems AmoElem
     * @return array AmoElem
     * @throws Exception
     */
    public function createElemsNEW(array $elems){
		$data = [];		
		$elem_type = $elems[0]->get_type();
		foreach ($elems as $k => $v) {
			$data['add'][$k]['name'] = $elems[$k]->get_name();
			switch (TRUE) {
				case ($elem_type === Lead::ELEM_TYPE || $elem_type === Customer::ELEM_TYPE):
					$data['add'][$k]['company_id'] = $elems[$k]->get_company();
				case ($elem_type !== Contact::ELEM_TYPE):
					$data['add'][$k]['contacts_id'] = $elems[$k]->get_contacts();
					break;

				default:
					break;
			}
		}
		$result = $this->post($this->_link.'api/v2/'.$this->_elem_links[$elem_type], $data);
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
}
