<?php

function event_add($elem_id, $elem_type, $note_type, $text, $hash, $link){
	$data['add'][] = [
		'element_id' => $elem_id,
		'element_type' => $elem_type,
		'note_type' => $note_type
	];
	switch ($note_type) {
		case '4':
			$data['add'][0]['text'] = $text;
			break;
		case '10':
			$data['add'][0]['params']['UNIQ'] = $hash."_".time().mt_rand();
			$data['add'][0]['params']['DURATION'] = 30;
			$data['add'][0]['params']['SRC'] = "http://example.com/calls/1.mp3";
			$data['add'][0]['params']['LINK'] = "http://example.com/calls/1.mp3";
			$data['add'][0]['params']['PHONE'] = $text;
			break;
		default:
			$data['add'][0]['text'] = $text;
			break;
	}
	$links = $link.'/api/v2/notes';
	$result = req_curl($links, $data);
	if (isset($result['_embedded']['items'][0]['id']) && $result['_embedded']['items'][0]['id'] === 0) {		
		throw new Exception('не удалось найти элемент', 006);
	} elseif (!isset($result['_embedded']['items'])) {
		throw new Exception('Сервер прислал неожиданный ответ', 007);
	}

};

try {
	include 'amo_aut.php';
	amo_aut($config['link'], $config['mail'], $config['hash']);
	event_add(data_clean($_POST['id']), data_clean($_POST['elem_type']), data_clean($_POST['note_type']), data_clean($_POST['text']), 
		$config['hash'], $config['link']);
	echo "готово";
} catch ( Exception $e ) {
	echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}
