<?php
if (!file_exists('../config.php')) {
	throw new Exception('Файл конфига не найден', 69);	
}
$config = include('../config.php');



//очитка данных
function data_clean($value = ""){
    $value = trim($value);
    $value = htmlspecialchars($value);  
    return $value;
}

//Curl запрос отдельной функцией и шаг времени, для ограничений сервера
function req_curl($link, $data = null){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-undefined/2.0");
	curl_setopt($curl, CURLOPT_URL, $link);
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
		return req_curl($link, $data);
	}
			

	$result = json_decode($out,TRUE);	
	return $result;
};

// авторизация
function amo_aut($link, $mail, $hash){
	$user= [
		'USER_LOGIN'=>$mail, 
		'USER_HASH'=> $hash
	];
	$link=$link.'/private/api/auth.php?type=json';
	$result=req_curl($link, $user);
	$result=$result['response'];
	if (isset($result['auth'])) {
	} else {
		throw new Exception('Авторизация не прошла', 666);
	} 
};




