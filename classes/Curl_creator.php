<?php
class Curl_creator{
	private $_link;
	private $_mail;
	private $_hash;
	private $_max_row;

	public function __construct($akk, $mail, $hash, $max_row){
		$this->_link = "https://".$akk.".amocrm.ru";
		$this->_mail =  $mail;
		$this->_hash = $hash;
		$this->_max_row = $max_row;
	}
	
	public function get_hash(){
		return $this->_hash;
	}
	public function get_max_row(){
		return $this->_max_row;
	}
	private function reqCurl($link, array $data = NULL){
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
			return $this->reqCurl($link, $data);
		}
		$result = json_decode($out,TRUE);	
		return $result;
	}

	function cleanData($value = ""){
	    $value = trim($value);
	    $value = htmlspecialchars($value);  
	    return $value;
	}

	public function auth(){
		$data = [
			'USER_LOGIN' => $this->_mail, 
			'USER_HASH' => $this->_hash
		];
		$link = $this->_link."/private/api/auth.php?type=json";
        $result = $this->reqCurl($link, $data);
		$result = $result['response'];
		if (!isset($result['auth'])) {
			throw new Exception('Авторизация не прошла', 666);
		}
	}

	public function add($link, array $data){
		$links = $this->_link."/api/v2/".$link;
		return $this->reqCurl($links, $data);			
	}

	public function get($link){
		$links = $this->_link."/api/v2/".$link;
		return $this->reqCurl($links);			
	}
}

