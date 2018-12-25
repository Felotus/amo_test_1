<?php
include 'amo_aut.php';
amo_aut();

//ищем текстовые строки
function textfield_find($type){
  switch ($type) {
    case 1:
      $type_str = "contacts";
      break;
    case 3:
      $type_str = "companies";
      break;
    case 2:
      $type_str = "leads";
      break;
    case 12:
      $type_str = "customers";
      break;
    default:
      $type_str = "contacts";
      break;
  }
  $textfield_id=0;
  $link = 'https://ko609.amocrm.ru/api/v2/account?with=custom_fields';
  $result=req_curl(0,$link);
    if (isset($result['ooops']['errors']['code'])){
      return $result;
  }
  $result = $result['_embedded']['custom_fields'][$type_str];
  foreach ($result as $key => $value) {
    if ($value['field_type']==1){
      $textfield_id=$key;
      break;
    }
  };
  return  $textfield_id;
};

//создаем текстовую строку
function textfield_create($type){
  $fields['add'] = array(
   array(
      'name' => "Текстовое поле",
      'field_type'=> 1,
      'element_type' => $type,
      'origin' => "8aa9ee7d3c33de7d873308e5f2afe4d5689f38be_".time(),
   )
  );
  $link='https://ko609.amocrm.ru/api/v2/fields';
  $result=req_curl(0,$link);
    if (isset($result['ooops']['errors']['code'])){
      return $result;
  }
  $result=$result['_embedded']['items'];
  foreach($result as $v)
    if(is_array($v))
    $output=$v['id'];
  return $output;
};

//заполняем текстовое поле
function textfield_update($type,$elem_id, $field_id, $str){
  switch ($type) {
  case 1:
    $link = "https://ko609.amocrm.ru/api/v2/contacts";
    break;
  case 3:
    $link = "https://ko609.amocrm.ru/api/v2/companies";
    break;
  case 2:
    $link = "https://ko609.amocrm.ru/api/v2/leads";
    break;
  case 12:
    $link = "https://ko609.amocrm.ru/api/v2/customers";
    break;
  default:
    $link = "https://ko609.amocrm.ru/api/v2/contacts";
    break;
  }
  $data['update'][0]['id']=$elem_id;
  $data['update'][0]['updated_at']=time();
  $data['update'][0]['custom_fields'][0]['id']=$field_id;
  $data['update'][0]['custom_fields'][0]['values'][0]['value']=$str;
  $result=req_curl(1,$link,$data);
  if (isset($result['ooops']['errors']['code'])){
    return $result;
  }
  return $result;
};
$id = textfield_find($_POST['elem_type']);
if ($id == 0){
  $id = textfield_create($_POST['elem_type']);
};

var_dump(textfield_update($_POST['elem_type'],$_POST['id'],$id,$_POST['text']));
echo "готово";


?>