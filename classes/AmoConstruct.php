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
	private $_field_types = [
		'text' => 1,
		'multiselect' => 5
	];


	/**
	 * @param string $akk
	 * @param string $mail
	 * @param string $hash
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
			throw new Exception('Авторизация не прошла', 666);
		}
		
	}

	/**
	 * @param int $elem_type
	 * @param Field $field
	 * @return Field
	 * @throws Exception
	 */
	public function createField($elem_type, Field $field){
		$field_id = NULL;
		$enums = NULL;
		$data['add'][] = [
			'name' => $field->get_name(),
			'field_type'=> $field->get_type(),
			'element_type' => $elem_type,
			'origin' => $field->get_origin(),
			'enums' => $field->get_enums(),
			'is_editable' => $field->get_editable()
		];
		$result = $this->post($this->_link.'api/v2/fields', $data);
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach($result as $v){
				$field->set_id($v['id']);
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
		return $field;
	}

	public function getFieldEnums($elem_type, Field $field){
		$result = $this->accReq(['custom_fields']);
		if (is_array($result)) {
			$result = $result['_embedded']['custom_fields'][$this->_elem_links[$elem_type]][$field->get_id()]['enums'];
			foreach ($result as $key => $value) {
				$enums[$key] = $value;
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
		$field->set_enums($enums);
		return $field;
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
     * @param $max_row
     * @return array
     * @throws Exception
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
     * @param $int max_row
     * @return array Company
     * @throws Exception
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


	public function updateElems(array $elems){
		foreach ($elems as $k => $v) {
            $custom_fields = [];
			foreach ($v->get_custom_fields() as $val){
				$values = $val->get_values();
				switch ($val->get_type()) {
					case Field::TEXT:
						$values = ['value' => $values[0]];
						break;
					
					case Field::MULTISELECT:
						break;

					default:
						throw new Exception('Не указан тип поля');
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
		$result = $this->post($this->_link.'api/v2/'.$this->_elem_links[$elems[0]->get_type()], $data);
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

	/**
	 * @param AmoElem $elem
	 * @param Field $field
	 * @return Field
	 * @throws Exception
	 */
	public function findFieldOnType(AmoElem $elem, Field $field){
		$id = NULL;
		$enums = NULL;
		$found = FALSE;
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
					$found = TRUE;
					break;
				}
			} 
		} else {
			throw new Exception('Сервер прислал неожиданный ответ',7);
		}
		if ($found === FOLSE) {
			 
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
			'element_id' => $elem->get_id(),
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
	 * @throws Exception
	 */
	public function closeTask(Task $task){
		$data['update'][] = [
			'id' => $task->get_id(),
			'is_completed' => 1,
			'updated_at' => time(),
			'text' => $task->get_val()
		];
		$result = $this->post($this->_link.'api/v2/tasks', $data);
		if (isset($result['_embedded']['items'][0]['id']) && $result['_embedded']['items'][0]['id'] === 0) {        
			throw new Exception('не удалось найти элемент', 6);
		} elseif (!isset($result['_embedded']['items'])) {
			throw new Exception('Сервер прислал неожиданный ответ', 7);
		}
	}
}
