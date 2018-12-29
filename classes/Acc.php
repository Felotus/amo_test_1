<?php
class Acc{
	const _LINK = 'account';
	static public function get($curl, array $params = null){
		$links = self::_LINK;
		if (!is_null($params)){
			$links .= '?with=';
			foreach ($params as $key => $value) {
				$links .= $value.',';
			}
			$links = substr($links, 0, -1);
		}
		return $curl->get($links);
	}
}