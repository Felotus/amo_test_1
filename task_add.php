<?php
include 'amo_aut.php';
amo_aut();
function client_time($datetime){
  $dates = explode("T", $datetime);
  $timeGMT = ($dates[2]-1)*3600;
  $ymd = explode("-", $dates[0]);
  $hm = explode(":", $dates[1]);
  return strtotime($ymd[2]."-".$ymd[1]."-".$ymd[0]." ".$hm[0].":".$hm[1])-$timeGMT;
}
function task_add($elem_id, $elem_type, $task_type, $text, $datetime){
  $data['add'][0]['element_id']=$elem_id;
  $data['add'][0]['element_type']=$elem_type;
  $data['add'][0]['task_type']=$task_type;
  $data['add'][0]['complete_till']=$datetime;
  $data['add'][0]['text']=$text;
  $link='https://ko609.amocrm.ru/api/v2/tasks';
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

task_add($_POST['id'],$_POST['elem_type'],$_POST['task_type'],$_POST['text'],client_time($_POST['date']));
echo "готово";

?>