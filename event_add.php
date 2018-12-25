<?php
include 'amo_aut.php';
amo_aut();
function event_add($elem_id, $elem_type, $note_type, $text){
  $data['add'][0]['element_id']=$elem_id;
  $data['add'][0]['element_type']=$elem_type;
  $data['add'][0]['note_type']=$note_type;
  switch ($note_type) {
    case '4':
      $data['add'][0]['text']=$text;
      break;
    case '10':
      $data['add'][0]['params']['UNIQ']="8aa9ee7d3c33de7d873308e5f2afe4d5689f38be_".time().mt_rand();
      $data['add'][0]['params']['DURATION']="30";
      $data['add'][0]['params']['SRC']="http://example.com/calls/1.mp3";
      $data['add'][0]['params']['LINK']="http://example.com/calls/1.mp3";
      $data['add'][0]['params']['PHONE']=$text;
      break;
    default:
      $data['add'][0]['text']=$text;
      break;
  }
  $data['add'][0]['text']=$text;
  $link='https://ko609.amocrm.ru/api/v2/notes';
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

event_add($_POST['id'],$_POST['elem_type'],$_POST['note_type'],$_POST['text']);
echo "готово";

?>