<?php
include 'amo_aut.php';


function task_add($elem_id, $elem_type, $task_type, $text, $datetime, $link){
  $dates = explode("T", $datetime);
  $timeGMT = ($dates[2]-1)*3600;
  $ymd = explode("-", $dates[0]);
  $hm = explode(":", $dates[1]);
  $data['add'][0]['element_id']=$elem_id;
  $data['add'][0]['element_type']=$elem_type;
  $data['add'][0]['task_type']=$task_type;
  $data['add'][0]['complete_till']=strtotime($ymd[2]."-".$ymd[1]."-".$ymd[0]." ".$hm[0].":".$hm[1])-$timeGMT;
  $data['add'][0]['text']=$text;
  $links=$link.'/api/v2/tasks';
  
  $result=req_curl(POST_REQ, $links, $data);
};
try {
  amo_aut($link, $mail, $hash);
  task_add($_POST['id'], $_POST['elem_type'], $_POST['task_type'], $_POST['text'], $_POST['date'], $link);
  echo "готово";
} catch ( Exception $e ) {
  echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}
