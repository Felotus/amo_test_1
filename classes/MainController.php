<?php
class MainController{

	const ROW_START = 0;

	private function get_config(){
		if (!file_exists('config.php')) {
			throw new Exception('Файл конфига не найден');	
		} else {
			return include ('config.php');
		}
	}

	public function actionMassCreate(){
		define('ROW_START', 0);
		$config = $this->get_config();
		$enums_val = [
			'значение 1',
			'значение 2',
			'значение 3',
			'значение 4',
			'значение 5',
			'значение 6',
			'значение 7',
			'значение 8',
			'значение 9',
			'значение 10',
		];
		$max_row = $config['max_row'];
		$field_name = 'Мультиполе';
		$num = DataFilter::clear($_POST['num']);
		$amo_us = new AmoConstruct($config['api']);
		$amo_us->auth($config['akk'], $config['mail'], $config['hash']);
		$multi = new Field();	
		$multi->set_type(Field::MULTISELECT);
		$multi->set_name($field_name);
		$multi->set_enums($enums_val);
		$multi->set_origin($config['origin']);
		$fields = $amo_us->createFields(Contact::ELEM_TYPE, [$multi]);
		$multi = $fields[0];
		foreach ($amo_us->getFields(Contact::ELEM_TYPE) as $k => $v) {
			if ($k === $multi->get_id()) {
				$multi->set_enums($v->get_enums());
			}
		}
		for ($i = $num, $n = 0; $i > 0; $i -= $max_row, $n++) {
			if ($i > $max_row) {
				$col = $max_row;
			} else {
				$col = $i;
			}
			for ($j = 0; $j < $col; $j++) {
				$elem = new Contact();
				$name = mt_rand();
				$data['add'][] = [
					'name' => $name
				];
				$elem->set_name($name);
				$contacts[] = $elem;
			};
			$contacts = $amo_us->createElemsNEW($contacts);
			foreach ($contacts as $k => $v) {
				$companies[$k] = new Company();
				$companies[$k]->set_name(mt_rand());
				$companies[$k]->set_contacts([$contacts[$k]->get_id()]);
			}
			$companies = $amo_us->createElemsNEW($companies);
			foreach ($contacts as $k => $v) {
				$leads[$k] = new lead();
				$leads[$k]->set_name(mt_rand());
				$leads[$k]->set_contacts([$contacts[$k]->get_id()]);
				$leads[$k]->set_company($companies[$k]->get_id());
			}
			$amo_us->createElemsNEW($leads);
			foreach ($contacts as $k => $v) {
				$customers[$k] = new Customer();
				$customers[$k]->set_name(mt_rand());
				$customers[$k]->set_contacts([$contacts[$k]->get_id()]);
				$customers[$k]->set_company($companies[$k]->get_id());
			}
			$amo_us->createElemsNEW($customers);
		}
		$start_row = ROW_START;
		do {
			$result = $amo_us->getContacts($start_row, $max_row);
			if (is_array($result)) {
				foreach ($result as $key => $value) {
					$enums_data = [];
					$multiCont = clone($multi);
					foreach ($multi->get_enums() as $val) {	
						if (mt_rand(0, 1) === 1) {
							$enums_data[] = $val;
						}			
					}
					$multiCont->set_values($enums_data);
					$result[$key]->set_custom_fields([$multiCont]);
				}
				$amo_us->updateElems($result);
			}
			$start_row += $max_row;
		} while (is_array($result));
		echo 'готово';
		return TRUE;	
	}

	public function actionChangeTextfield(){
		$config = $this->get_config();
		$field_name = 'новое поле';
		$elem_id = DataFilter::clear($_POST['id']);
		$amo_us = new AmoConstruct($config['api']);
		$amo_us->auth($config['akk'], $config['mail'], $config['hash']);
		switch (DataFilter::clear($_POST['elem_type'])) {
			case Contact::ELEM_TYPE:
				$elem = new Contact();
				break;
			case Company::ELEM_TYPE:
				$elem = new Company();
				break;
			case Lead::ELEM_TYPE:
				$elem = new Lead();
				break;
			case Customer::ELEM_TYPE:
				$elem = new Customer();
				break;
			default:
				throw new Exception('Элемент не найден', 88);
				break;
		};
		$elem->set_id($elem_id);
		$field = new Field();
		$field->set_id(NULL);
		$field->set_type(Field::TEXT);
		$field->set_name($field_name);
		$field->set_values([DataFilter::clear($_POST['text'])]);
		foreach($amo_us->getFields($elem->get_type()) as $k => $v){
			if ($v->get_type() === $field->get_type()) {
				$field->set_id($v->get_id());
				break;
			}
		};
		if (is_null($field->get_id())){
			$fields = $amo_us->createFields($elem->get_type(), [$field]);
			$field = $fields[0];
		}
		$elem->set_custom_fields([$field]);
		$amo_us->updateElems([$elem]);
		echo 'готово';
		return TRUE;
	}

	public function actionAddEvent(){
		$config = $this->get_config();
		$elem_name = 'случайное имя';
		$elem_id = DataFilter::clear($_POST['id']);
		$amo_us = new AmoConstruct($config['api']);
		$amo_us->auth($config['akk'], $config['mail'], $config['hash']);
		switch (DataFilter::clear($_POST['elem_type'])) {
			case Contact::ELEM_TYPE:
				$elem = new Contact();
				break;
			case Company::ELEM_TYPE:
				$elem = new Company();
				break;
			case Lead::ELEM_TYPE:
				$elem = new Lead();
				break;
			case Customer::ELEM_TYPE:
				$elem = new Customer();
				break;
			default:
				throw new Exception('Элемент не найден', 88);
				break;
		};
		$elem->set_id($elem_id);
		$note = new Note();
		$note->set_type(DataFilter::clear($_POST['note_type']));
		$note->set_val(DataFilter::clear($_POST['text']));
		$note->set_call_link('http://example.com/calls/1.mp3');
		$note->set_call_duration(30);
		$note->set_origin($config['origin']);

		$amo_us->createNote($elem, $note);
		echo 'готово';
		return TRUE;
	}

	public function actionAddTask(){
		$config = $this->get_config();
		$elem_name = 'случайное имя';
		$elem_id = DataFilter::clear($_POST['id']);
		$amo_us = new AmoConstruct($config['api']);
		$amo_us->auth($config['akk'], $config['mail'], $config['hash']);
		switch (DataFilter::clear($_POST['elem_type'])) {
			case Contact::ELEM_TYPE:
				$elem = new Contact();
				break;
			case Company::ELEM_TYPE:
				$elem = new Company();
				break;
			case Lead::ELEM_TYPE:
				$elem = new Lead();
				break;
			case Customer::ELEM_TYPE:
				$elem = new Customer();
				break;
			default:
				throw new Exception('Элемент не найден', 88);
				break;
		};
		$elem->set_id($elem_id);
		$task = new Task();
		$task->set_type(DataFilter::clear($_POST['task_type']));
		$task->set_val(DataFilter::clear($_POST['text']));
		$task->set_date(DataFilter::clear($_POST['date']));
		$amo_us->createTask($elem, $task);
		echo 'готово';
		return TRUE;
	}

	public function actionCloseTask(){
		$config = $this->get_config();
		$amo_us = new AmoConstruct($config['api']);
		$amo_us->auth($config['akk'], $config['mail'], $config['hash']);
		$task = new Task();
		$task->set_val(DataFilter::clear($_POST['text']));
		$task->set_id(DataFilter::clear($_POST['id']));
		$amo_us->updateTask($task);
		echo 'готово';
		return TRUE;
	}
	public function actionGetTaskType(){
		$config = $this->get_config();
		$amo_us = new AmoConstruct($config['api']);
		$amo_us->auth($config['akk'], $config['mail'], $config['hash']);	 
		return $amo_us->getTaskTypes();
	}

	private function createSelect($id, array $values, $title = NULL, $dis_opt = NULL){
		$select = '';
		$select .= '<select id="'.$id.'" title="'.$title.'">';
		if (!is_null($dis_opt)){
			$select .= '<option disabled>'.$dis_opt.'</option>';
		}
		foreach ($values as $k => $v) {
			$select .= '<option value="'.$k.'">'.$v.'</option>';
		}
		$select .= 	'</select>';
		return $select;

	}

	public function actionGetView() {
		echo '<!DOCTYPE html>
		<html>
			<head>
				<meta charset="utf-8">
				<title>Первое задание</title>
				<style>
					#cont {
						width: 5px;
					}
					input[type="number"] {
						-moz-appearance:textfield;
					}

					input::-webkit-outer-spin-button,
					input::-webkit-inner-spin-button {
						-webkit-appearance: none;
					}
				</style>
			</head>
			<body>
				<div id="cont" position = "relative" text-align= "right">
					<select id="sel1" title="тип операции">
						<option disabled>Выберите запрос</option>
						<option value="1">создание сущностей</option>
						<option value="2">заполнить текстовое поле</option>
						<option value="3">добавить примечание/звонок</option>
						<option value="4">добавить задачу </option>
						<option value="5">закрыть задачу</option>
					</select>
					<select id="sel2" title="тип сыщности">
						<option disabled>тип сущности</option>
						<option value="1">контакт</option>
						<option value="2">сделка</option>
						<option value="3">компания</option>
						<option value="12">покупатель</option>
					</select>
					<select id="sel3" title="тип примечания">
						<option disabled>тип примечания</option>
						<option value="4">примечание</option>
						<option value="10">входящий звонок</option>
					</select>
		';
		echo $this->createSelect('sel4', $this->actionGetTaskType(), 'тип задачи', 'тип задачи');
		echo '
					<input type="number" id="num1" placeholder="количество элементов">
					<textarea id="textar1" placeholder="ваш комментарий" ></textarea>
					<input type="text" id="phone1" placeholder="телефон">
					<input type="datetime-local" id="date1"  title="крайний срок">
					<button id="but1">Отправить</button>        
				</div>
				<script src="/js/jquery.js"></script>
				<script src="/js/test.js"></script>
			</body>
		</html>
		';
	}
}