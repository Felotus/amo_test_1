<?php
// класс управления api
class amo_user {
	private $_akk;
	private $_mail;
	private $_link;
	private $_hash;
	private $_max_row;
	const CUST_TYPE = 12;
	const LEADS_TYPE = 1;
	public function __construct($akk, $mail, $link, $hash, $max_row){
		$this->_akk = $akk;
		$this->_mail = $mail;
		$this->_link = 'https://'.$akk.'.amocrm.ru';
		$this->_hash = $hash;
		$this->_max_row = $max_row;
	}
	public function data_clean($value = ""){
    	$value = trim($value);
    	$value = htmlspecialchars($value);  
    	return $value;
	}
	private function req_curl($links, $data = NULL){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-undefined/2.0");
		curl_setopt($curl, CURLOPT_URL, $this->_link.$links);
		curl_setopt($curl, CURLOPT_HEADER,false);
		curl_setopt($curl, CURLOPT_COOKIEFILE,dirname(__FILE__)."/cookie.txt");
		curl_setopt($curl, CURLOPT_COOKIEJAR,dirname(__FILE__)."/cookie.txt");
		if (!is_null($data)) {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST,'POST');
			curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
			curl_setopt($curl, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
		}
		$out = curl_exec($curl);
		$code=curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		$code=(int)$code;
		usleep(142857);
		$errors= [
			301=>'Moved permanently',
			400=>'Bad request',
			401=>'Unauthorized',
			403=>'Forbidden',
			404=>'Not found',
			500=>'Internal server error',
			502=>'Bad gateway',
			503=>'Service unavailable'
		];
		if ($code !== 200 && $code !== 204) {
			if (isset($errors[$code])) {
				throw new Exception($errors[$code], $code);
			} else {
				throw new Exception('Undescribed error', $code);
			};
		} elseif ($code === 429) {
			sleep(3);
			return $this->req_curl($links, $data);
		}
		$result = json_decode($out,TRUE);	
		return $result;
	} 
	public function amo_auth(){
		$user = [
			'USER_LOGIN' => $this->_mail, 
			'USER_HASH' => $this->_hash
		];
		$link ='/private/api/auth.php?type=json';
		$result = $this->req_curl($link, $user);
		$result = $result['response'];
		if (!isset($result['auth'])) {
			throw new Exception('Авторизация не прошла', 666);
		}
	}
	public function create_multitext($name, array $enums_val, $elem_type){
		$fields['add'] = [
			[
				'name' => $name,
				'field_type'=> 5,
				'element_type' => $elem_type,
				'origin' => $this->_hash."_".time().mt_rand(),
				'is_editable' => 0,
				'enums' => $enums_val
			]
		];
		$links = '/api/v2/fields';
		$result = $this->req_curl($links, $fields);	
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach($result as $v){
				$multi_id = $v['id'];
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
		$links = '/api/v2/account?with=custom_fields';
		$result = $this->req_curl($links);
		switch ($elem_type) {
			case 1:
				$type_str = "contacts";
				break;
			case 3:
				$type_str = "companies";
				break;
			case 2:
				$type_str = "leads";
				break;
			case 12:
				$type_str = "customers";
				break;
			default:
				$type_str = "contacts";
				break;
		}
		if (is_array($result)) {
			$result = $result['_embedded']['custom_fields'][$type_str][$multi_id]['enums'];
		foreach ($result as $key => $value) {
			$enums[] = $key;
		}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}	
		return new field_multitext($multi_id, $name, $elem_type, $enums, $enums_val);
	}
	public function update_rand_all_multitext($field_id, array $enums, $elem_type){
		$limit_offset = 0;
		switch ($elem_type) {
			case 1:
				$type_str = "contacts";
				break;
			case 3:
				$type_str = "companies";
				break;
			case 2:
				$type_str = "leads";
				break;
			case 12:
				$type_str = "customers";
				break;
			default:
				$type_str = "contacts";
				break;
		}
		do {
			$cont_id = array();
			$links = '/api/v2/'.$type_str.'?limit_rows='.$this->_max_row.'&limit_offset='.$limit_offset;
			$result = $this->req_curl($links);
			if (is_array($result)) {
				$result = $result['_embedded']['items'];
				foreach ($result as $key => $value) {
					$enums_data = array();
					foreach ($enums as $val) {
						if (mt_rand(0, 1) === 1) {
							$enums_data[] = $val;
						}	
					}
					$data['update'][] = [
						'id' => $value['id'],
						'updated_at'=> time(),
						'custom_fields' => [
							'0' => [
								'id' => $field_id,
								'values' => $enums_data
							]
						]
					];
					$links = "/api/v2/".$type_str;
					$result = $this->req_curl($links, $data);
				};
			}
			$limit_offset += $this->$_max_row;
		} while (is_array($result));	
	}
	public function mass_create($num, $field_id, array $enums){
		for ($i = $num, $n = 0; $i > 0; $i -= $this->_max_row, $n++) {
			$cont_id = array();
			$data = array();
			if ($i > $this->_max_row) {
				$col = $this->_max_row;
			} else {
				$col = $i;
			}
			for ($j = 0; $j < $col; $j++) {
				foreach ($enums as $v) {
					if (mt_rand(0, 1) === 1) {
						$enums_data[] = $v;
					}
				}
				$data['add'][] = [
					'name' => mt_rand(),
					'custom_fields' => [
						[
							'id' => $field_id,
							'values' => $enums_data
						]
					]

				];
			};
			$links = '/api/v2/contacts/';
			$result = $this->req_curl($links, $data);
			if (is_array($result)) {
				$result = $result['_embedded']['items'];
				foreach ($result as $v) {
					$cont_id[] = $v['id'];
				}
				$comp_id = $this->mass_create_companies($cont_id);
				$this->mass_create_leads_cust($cont_id, $comp_id, CUST_TYPE);
				$this->mass_create_leads_cust($cont_id, $comp_id, LEADS_TYPE);
			} else {
				throw new Exception('Сервер прислал неожиданный ответ', 007);
			}
		}
	}
	private function  mass_create_companies(array $cont_id){
		foreach ($cont_id as $v) {
			$data['add'][] = [
				'name' => mt_rand(),
				'contacts_id'=> [
					'0'=> $v
							]
					];
		}
		$links = '/api/v2/companies';
		$result = $this->req_curl($links, $data);
		if (is_array($result)){
			$result = $result['_embedded']['items'];
			foreach ($result as $v) {
				$comp_id[] = $v['id'];
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
		return $comp_id;
	}
	private function mass_create_leads_cust(array $cont_id, array $comp_id, $elem_type){
		if ($type === LEADS_TYPE) {
			$links = "/api/v2/leads";
		} else {
			$links = "/api/v2/customers";
		}
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
		$result = $this->req_curl($links, $data);
		if (!is_array($result)){
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
	}
}
