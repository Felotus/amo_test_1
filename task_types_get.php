<?php


function task_types($link){ //Достаем все типы задач
	$links = $link.'/api/v2/account?with=task_types';
	$result = req_curl($links);
	$result = $result['_embedded']['task_types'];
	foreach ($result as $key => $value) {
		$task[$value['id']] = $value['name'];
	}
	return $task;
}
try {
	include 'amo_aut.php';
	amo_aut($config['link'], $config['mail'], $config['hash']);
	echo json_encode(task_types($config['link']));
} catch ( Exception $e ) {
	echo json_encode([$e->getCode() => $e->getMessage()]);
}
