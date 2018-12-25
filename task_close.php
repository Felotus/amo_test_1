<?php
include 'amo_aut.php';
amo_aut();
function task_add($task_id, $text){
  $data['update'][0]['id']=$task_id;
  $data['update'][0]['is_completed']=1;
  $data['update'][0]['updated_at']=time();
  $data['update'][0]['text']=$text;
  $link='https://ko609.amocrm.ru/api/v2/tasks';
  
  $result=req_curl(1,$link,$data);

  return $result;
}
var_dump(task_add($_POST['id'], $_POST['text']));
echo "готово";
?>