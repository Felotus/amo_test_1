<?php
//ЗАПРОСЫ и обработка ответов
//мультитекст добаление
$fields['add'] = [
      [
        'name' => $name,
        'field_type'=> 5,
        'element_type' => $elem_type,
        'origin' => $this->_hash."_".time().mt_rand(),
        'is_editable' => 0,
        'enums' => $enums_val
      ]
    ];
    $links = '/api/v2/fields';
    $result = $this->req_curl($links, $fields); 
    if (is_array($result)) {
      $result = $result['_embedded']['items'];
      foreach($result as $v){
        $multi_id = $v['id'];
      }
    } else {
      throw new Exception('Сервер прислал неожиданный ответ', 007);
    }
//поиск полей мультиполя
    $result = $result['_embedded']['custom_fields'][$type_str][$multi_id]['enums'];

//

