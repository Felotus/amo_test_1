<?php
include 'amo_aut.php';
amo_aut();
function textfield_find($type=0){
  switch ($type) {
    case 0:
      $type_str = "contacts";
      break;
    case 1:
      $type_str = "companies";
      break;
    case 2:
      $type_str = "leads";
      break;
    case 3:
      $type_str = "customers";
      break;
    default:
      $type_str = "contacts";
      break;
  }
  $textfield_id=0;
  $link = 'https://ko609.amocrm.ru/api/v2/account?with=custom_fields';
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
  $result = $result['_embedded']['custom_fields'][$type_str];
  foreach ($result as $key => $value) {
    if ($value['field_type']==1){
      $textfield_id=$key;
      break;
    }
  };
  echo  $textfield_id;
};

textfield_find(0);
?>