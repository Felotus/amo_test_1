<?php
class Company{
	private $_creator;
	const TYPE = 3;
	const LINK = 'companies';
	public function __construct($curl){
		$this->_creator = $curl;
	}

	public function mass_create(array $cont_id){
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
			throw new Exception('Сервер прислал неожиданный ответ', 017);
		}
		return $comp_id;
	}

}
