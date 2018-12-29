<?php
// Curl обработка
class Curl_creator{
	private $_link;
	private $_mail;
	private function req_curl($link, array $data = NULL){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-undefined/2.0");
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie.txt");
		curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__)."/cookie.txt");
		if (!is_null($data)) {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST,'POST');
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
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
			}
		} elseif ($code === 429) {
			sleep(3);
			return $this->req_curl($link, $data);
		}
		$result = json_decode($out,TRUE);	
		return $result;
	}
	public function auth($akk, $mail, $hash){
		$this->_link = "https://".$akk.".amocrm.ru";
		$this->_mail =  $mail;
		$data = [
			'USER_LOGIN' => $this->_mail, 
			'USER_HASH' => $hash
		];
		$links = $this->_link."/private/api/auth.php?type=json";
        $result = $this->req_curl($links, $data);
		$result = $result['response'];
		if (!isset($result['auth'])) {
			throw new Exception('Авторизация не прошла', 666);
		}
	}
	public function add($link, array $data){
		$links = $this->_link."/api/v2/".$link;
		return $this->req_curl($links, $data);			
	}
	public function get($link){
		$links = $this->_link."/api/v2/".$link;
		return $this->req_curl($links);			
	}
};

// контакт
class Contact{
	const _TYPE = 1;
	const _LINK = 'contacts';
	public function add_field($curl, $field_type, $name, array $enums_val){
		$field = new Field();
		$field->set($curl, $this::_TYPE, $field_type, $name, $enums_val);
		return $field;
	}
}

// компания


// сделка

// покупатель


// поле
class Field{
	private $_id;
	private $_elem_type;
	private $_name;
	private $_enums;
	private $_origin;
	private $_type;
	const _LINK = 'fields';
	public function set($curl, $elem_type, $field_type, $name, array $enums_val = null){
		$this->_type = $field_type;
        $this->_origin = $curl->_hash."_".time().mt_rand();
		$fields['add'] = [
			[
				'name' => $name,
				'field_type'=> $this->_type,
				'element_type' => $elem_type,
				'origin' => $this->_origin,
				'is_editable' => 0,
				'enums' => $enums_val
            ]
		];
		$result = $curl->add($this::_LINK, $fields);
        $multi_id=0;
		if (is_array($result)) {
			$result = $result['_embedded']['items'];
			foreach($result as $v){
                $multi_id = $v['id'];
			}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
		$result = Acc::get($curl, ['custom_fields']);
		if (is_array($result)) {
			$result = $result['_embedded']['custom_fields'][Field::_LINK][$multi_id]['enums'];
		foreach ($result as $key => $value) {
			$enums[$key] = $value;
		}
		} else {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
		$this->_id = $multi_id;
		$this->_elem_type = $elem_type;
		$this->_name = $name;
		$this->_enums = $enums;
	}
	public function get_elem_type(){
		return $this->_elem_type;
	}
	public function get_id(){
		return $this->_id;
	}
	public function get_enums(){
		return $this->_enums;
	}
	public function get_name(){
		return $this->_name;
	}
}


//аккаунт ???
class Acc{
	const _LINK = 'account';
	static public function get($curl, array $params = null){
		$links = Acc::_LINK;
		if (!is_null($params)){
			$links .= '?with=';
			foreach ($params as $key => $value) {
				$links .= $value.',';
			}
			substr($links, 0, -1);
		}
		return $curl->get($links);
	}
}
