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
  
  $result=req_curl(1,$link,$data);
  if (isset($result['ooops']['errors']['code'])){
    return $result;
  }
};

task_add($_POST['id'],$_POST['elem_type'],$_POST['task_type'],$_POST['text'],client_time($_POST['date']));
echo "готово";

?>