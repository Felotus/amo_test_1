<?php
function task_close($task_id, $text, $link){
	$data['update'][] = [
		'id' => $task_id,
		'is_completed' => 1,
		'updated_at' => time(),
		'text' => $text
	];
	$links  = $link.'/api/v2/tasks'; 
	$result = req_curl($links, $data);
};
try {
	include 'amo_aut.php';
	amo_aut($config['link'], $config['mail'], $config['hash']);
	task_close(data_clean($_POST['id']), data_clean($_POST['text']), $config['link']);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
};
