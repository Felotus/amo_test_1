<?php
class Note{
	private $_note_type;
	private $_val;
	private $_creator;
	const LINK = 'notes';

	public function __construct($curl){
		$this->_creator = $curl;
	}

	public function set($elem_id, $elem_type, $note_type, $val){
		$this->_note_type = $note_type;
		$this->_val = $val;

		$data['add'][] = [
			'element_id' => $elem_id,
			'element_type' => $elem_type,
			'note_type' => $this->_note_type
		];
		switch ($this->_note_type) {
			case '4':
				$data['add'][0]['text'] = $this->_val;
				break;
			case '10':
				$data['add'][0]['params']['UNIQ'] = $this->_creator->get_hash()."_".time().mt_rand();
				$data['add'][0]['params']['DURATION'] = 30;
				$data['add'][0]['params']['SRC'] = "http://example.com/calls/1.mp3";
				$data['add'][0]['params']['LINK'] = "http://example.com/calls/1.mp3";
				$data['add'][0]['params']['PHONE'] = $this->_val;
				break;
			default:
				$data['add'][0]['text'] = $this->_val;
				break;
		}
		$result = $this->_creator->add(self::LINK, $data);
		if (isset($result['_embedded']['items'][0]['id']) && $result['_embedded']['items'][0]['id'] === 0) {		
			throw new Exception('не удалось найти элемент', 006);
		} elseif (!isset($result['_embedded']['items'])) {
			throw new Exception('Сервер прислал неожиданный ответ', 007);
		}
	}
}

