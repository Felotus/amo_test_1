<?php
include 'amo_aut.php';
amo_aut();
function task_types(){ 
  $link = 'https://ko609.amocrm.ru/api/v2/account?with=task_types';
  
  $result=req_curl(0,$link);
  if (isset($result['ooops']['errors']['code'])){
    return $result;
  }
  $result = $result['_embedded']['task_types'];
  foreach ($result as $key => $value) {
    $task[$value['id']]=$value['name'];
  }
  return $task;
}
echo json_encode(task_types());
?>