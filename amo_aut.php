<?php
// шаг времени, для ограничений сервера
function amo_stop_time(){usleep(142857);};

//Curl запрос отдельной функцией и шаг времени, для ограничений сервера
function req_curl($type,$link,$data = null){
  $curl = curl_init();
  if($type){
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client-undefined/2.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($data));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); 
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); 
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
  }
  else{
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-undefined/2.0");
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__)."/cookie.txt");
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__)."/cookie.txt");
  };
  $out = curl_exec($curl);
  $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
  curl_close($curl);
  $code=(int)$code;
  $errors=array(
    301=>'Moved permanently',
    400=>'Bad request',
    401=>'Unauthorized',
    403=>'Forbidden',
    404=>'Not found',
    500=>'Internal server error',
    502=>'Bad gateway',
    503=>'Service unavailable'
  );
  try
  {
  if($code!=200 && $code!=204)
      throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
  }
  catch(Exception $E)
  {
    die('Ошибка, что то пошло не так'.$E->getCode().'  '.$E->getMessage());
    //$result['ooops']['errors']['code']=$E->getCode();
    //$result['ooops']['errors']['text']=$E->getMessage();
    //usleep(142857);
    //return $result;
  }
  $result = json_decode($out,TRUE);
  usleep(142857);
  return $result;
}

// авторизация
function amo_aut(){
  $user=array(
    'USER_LOGIN'=>'ko609@mail.ru', 
    'USER_HASH'=>'8aa9ee7d3c33de7d873308e5f2afe4d5689f38be'
  );
  $link='https://ko609.amocrm.ru/private/api/auth.php?type=json';
  $result=req_curl(1,$link,$user);
  if (isset($result['ooops']['errors']['code'])){
    return $result;
  }
  $result=$result['response'];
  if(!isset($result['auth'])){
    $result['ooops']['errors']['code']='666';
    $result['ooops']['errors']['text']='авторизация не прошла';
    return $result;
  };
};
?>