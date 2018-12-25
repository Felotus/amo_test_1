<?php
include 'amo_aut.php';
amo_aut();

//ищем текстовые строки
function textfield_find($type="1"){
  switch ($type) {
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
  return  $textfield_id;
};

//создаем текстовую строку
function textfield_create($type="1"){
  $fields['add'] = array(
   array(
      'name' => "Текстовое поле",
      'field_type'=> 1,
      'element_type' => $type,
      'origin' => "8aa9ee7d3c33de7d873308e5f2afe4d5689f38be_".time(),
   )
  );
  $link='https://ko609.amocrm.ru/api/v2/fields';
  $curl=curl_init(); 
  curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client-undefined/2.0');
  curl_setopt($curl,CURLOPT_URL,$link);
  curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
  curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($fields));
  curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
  curl_setopt($curl,CURLOPT_HEADER,false);
  curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); 
  curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); 
  curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
  curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
  $out=curl_exec($curl); 
  $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
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
  $Response=json_decode($out,true);
  $Response=$Response['_embedded']['items'];
  foreach($Response as $v)
    if(is_array($v))
    $output=$v['id'];
  amo_stop_time();
  return $output;
};

//заполняем текстовое поле
function textfield_update($type,$elem_id, $field_id, $str){
  switch ($type) {
  case 1:
    $link = "https://ko609.amocrm.ru/api/v2/contacts";
    break;
  case 3:
    $link = "https://ko609.amocrm.ru/api/v2/companies";
    break;
  case 2:
    $link = "https://ko609.amocrm.ru/api/v2/leads";
    break;
  case 12:
    $link = "https://ko609.amocrm.ru/api/v2/customers";
    break;
  default:
    $link = "https://ko609.amocrm.ru/api/v2/contacts";
    break;
  }
  $data['update'][0]['id']=$elem_id;
  $data['update'][0]['updated_at']=time();
  $data['update'][0]['custom_fields'][0]['id']=$field_id;
  $data['update'][0]['custom_fields'][0]['values'][0]['value']=$str;
  $curl=curl_init();
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
  $out=curl_exec($curl);
  $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
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
  $result[] = json_decode($out,TRUE);
  amo_stop_time();
};



$id = textfield_find($_POST['elem_type']);
if ($id == 0){
  $id = textfield_create($_POST['elem_type']);
};
textfield_update($_POST['elem_type'],$_POST['id'],$id,$_POST['text']);
echo "готово";
?>