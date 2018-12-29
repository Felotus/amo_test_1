<?php
class Contact{
	private $_creator;
	private $_id;
	private $_name;
	const TYPE = 1;
	const LINK = 'contacts';
	public function __construct($curl){
		$this->_creator = $curl;
	}

	public function addField($field_type, $name, array $enums_val = NULL){
		$field = new Field();
		$field->set($this->_creator, self::LINK, self::TYPE, $field_type, $name, $enums_val);
		return $field;
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
				$comp_id = $company->mass_create($cont_id);
				$lead->mass_create($cont_id, $comp_id);
				$cust->mass_create($cont_id, $comp_id);
			} else {
				throw new Exception('Сервер прислал неожиданный ответ1', 007);
			}
		}
	}
}

