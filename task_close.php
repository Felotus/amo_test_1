<?php
include 'amo_aut.php';
function task_close($task_id, $text, $link){
	$data['update'][0]['id']=$task_id;
	$data['update'][0]['is_completed']=1;
	$data['update'][0]['updated_at']=time();
	$data['update'][0]['text']=$text;
	$links=$link.'/api/v2/tasks'; 
	$result=req_curl(POST_REQ, $links, $data);
};
try {
	amo_aut($link, $mail, $hash);
	task_close($_POST['id'], $_POST['text'], $link);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
};
