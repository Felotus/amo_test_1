<?php
include 'amo_aut.php';

function task_types($link){ 
  $links = $link.'/api/v2/account?with=task_types';
  $result=req_curl(GET_REQ, $links);
  $result = $result['_embedded']['task_types'];
  foreach ($result as $key => $value) {
    $task[$value['id']]=$value['name'];
  }
  return $task;
}

amo_aut($link, $mail, $hash);
echo json_encode(task_types($link));
