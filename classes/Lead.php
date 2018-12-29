<?php
class Lead{
	private $_creator;
	const TYPE = 2;
	const LINK = 'leads';
	public function __construct($curl){
		$this->_creator = $curl;
	}

	public function mass_create(array $cont_id, array $comp_id){
		$data = array();
		foreach ($cont_id as $k => $v) {
			$data['add'][] = [
				'name' => mt_rand(),
				'company_id' => $comp_id[$k],
				'contacts_id'=> [
					'0' => $v
				]
			];
		}
		$result = $this->_creator->add(self::LINK, $data);
		if (!is_array($result)){
			throw new Exception('Сервер прислал неожиданный ответ', 027);
		}
	}
}
