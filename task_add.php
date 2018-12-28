<?php

function task_add($elem_id, $elem_type, $task_type, $text, $datetime, $link){
	$data['add'][] = [
		'element_id' => $elem_id,
		'element_type' => $elem_type,
		'task_type' => $task_type,
		'complete_till' => $datetime,
		'text' => $text
	];
	$links = $link.'/api/v2/tasks';
	$result = req_curl($links, $data);
};


try {
	include 'amo_aut.php';
	amo_aut($config['link'], $config['mail'], $config['hash']);
	task_add(data_clean($_POST['id']), data_clean($_POST['elem_type']), data_clean($_POST['task_type']), data_clean($_POST['text']), data_clean($_POST['date']), $config['link']);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}

