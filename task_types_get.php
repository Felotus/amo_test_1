<?php
include 'amo_aut.php';
amo_aut();
function task_types(){ 
  $link = 'https://ko609.amocrm.ru/api/v2/account?with=task_types';
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
  curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-undefined/2.0");
  curl_setopt($curl, CURLOPT_URL, $link);
  curl_setopt($curl, CURLOPT_HEADER,false);
  curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__)."/cookie.txt");
  curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__)."/cookie.txt");
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
    die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
  }
  $result = json_decode($out,TRUE);
  $result = $result['_embedded']['task_types'];
  foreach ($result as $key => $value) {
    $task[$value['id']]=$value['name'];
  }
  return $task;
}
echo json_encode(task_types());
?>