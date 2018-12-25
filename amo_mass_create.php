<?php
include 'amo_aut.php';
//ищем Enums мультиселекта
function multitext_find($id){
  $link = 'https://ko609.amocrm.ru/api/v2/account?with=custom_fields';
  $result=req_curl(0,$link);
  if (isset($result['ooops']['errors']['code'])){
    return $result;
  }
  $result = $result['_embedded']['custom_fields']['contacts'][$id]['enums'];
  foreach ($result as $key => $value) {
    $enums[]=$key;;
  };
  return $enums;
};

//создаем мультиселект
function multitext_add(){
  $fields['add'] = array(
   array(
      'name' => "Выбери значение",
      'field_type'=> 5,
      'element_type' => 1,
      'origin' => "8aa9ee7d3c33de7d873308e5f2afe4d5689f38be_".time().mt_rand(),
      'is_editable' => 0,
      'enums' => array(
         "1 значение",
         "2 значение",
         "3 значение",
         "4 значение",
         "5 значение",
         "6 значение",
         "7 значение",
         "8 значение",
         "9 значение",
         "10 значение"
      )
   )
  );
  $link='https://ko609.amocrm.ru/api/v2/fields';
  $result=req_curl(1,$link,$fields);
  if (isset($result['ooops']['errors']['code'])){
    return $result;
  }
  $result=$result['_embedded']['items'];
  foreach($result as $v)
    if(is_array($v))
    $output=$v['id'];
  return $output;
};

//обновляем мультиселекты в контактах
function contacts_upd($cont_id,$field_id, $enums){
  $max_size=250;
  for ($i=count($cont_id),$n=0;$i>0;$i-=$max_size, $n++){
    $data= array();
    if ($i>$max_size) {
      $col = $max_size;
    }
    else {
      $col = $i;
    }
    for ($j=0; $j<$col; $j++){
      $data['update'][($j+($n*$max_size))]['id']=$cont_id[($j+($n*$max_size))];
      $data['update'][($j+($n*$max_size))]['updated_at']=time();
      $data['update'][($j+($n*$max_size))]['custom_fields'][0]['id']=$field_id;
      foreach ($enums as $v) {
        if(mt_rand(0,1)==1){
          $data['add'][($j+($n*$max_size))]['custom_fields'][0]['values'][]=$v;
        }
      }
    };
    $link = "https://ko609.amocrm.ru/api/v2/contacts";
    $result=req_curl(1,$link,$data);
    if (isset($result['ooops']['errors']['code'])){
      return $result;
    }
  };
};

//добавляем заданное количество контактов и возвращаем их id
function contacts_add($num = "1",$field_id, $enums){
  $max_size=250;
  for ($i=$num,$n=0;$i>0;$i-=$max_size,$n++){
    $data= array();
    if ($i>$max_size) {
      $col = $max_size;
    }
    else {
      $col = $i;
    }
    for ($j=0; $j<$col; $j++){
      $data['add'][($j+($n*$max_size))]['name']=mt_rand();
      $data['add'][($j+($n*$max_size))]['custom_fields'][0]['id']=$field_id;
      foreach ($enums as $v) {
        if(mt_rand(0,1)==1){
          $data['add'][($j+($n*$max_size))]['custom_fields'][0]['values'][]=$v;
        }
      }
    };

    $link='https://ko609.amocrm.ru/api/v2/contacts/';
    $result=req_curl(1,$link,$data);
    if (isset($result['ooops']['errors']['code'])){
      return $result;
    }
    $result=$result['_embedded']['items'];
    foreach($result as $v)
      $cont_id[]=$v['id'];
    };
    return $cont_id;
};

//добавляем заданное количество компаний и возвращаем их id
function companies_add($num = "1", $cont_id){   
  $max_size=250;
  for ($i=$num, $n=0;$i>0;$i-=$max_size, $n++){
    $data= array();
    if ($i>$max_size) {
      $col = $max_size;
    }
    else {
      $col = $i;
    }
    for ($j=0; $j<$col; $j++){
      $data['add'][$j+($n*$max_size)]['name']=mt_rand();
      $data['add'][$j+($n*$max_size)]['contacts_id'][]=$cont_id[$j+($n*$max_size)];
    };

    $link='https://ko609.amocrm.ru/api/v2/companies';
    $result=req_curl(1,$link,$data);
    if (isset($result['ooops']['errors']['code'])){
      return $result;
    }
    $result=$result['_embedded']['items'];
    foreach($result as $v)
      $comp_id[]=$v['id'];
  };
    return $comp_id;
};

//добавляет сделки или покупателей в зависимости от выбранного типа
function leads_custom_add($num = "1", $cont_id, $comp_id, $type){
  $data= array();
  $max_size=250;
  if ($type){
    $link="https://ko609.amocrm.ru/api/v2/leads";
  }
  else {
    $link="https://ko609.amocrm.ru/api/v2/customers";
  }
  for ($i=$num, $n=0;$i>0;$i-=$max_size, $n++){
    $data= array();
    if ($i>$max_size) {
      $col = $max_size;
    }
    else {
      $col = $i;
    }
    for ($j=0; $j<$col; $j++){
      $data['add'][$j+($n*$max_size)]['name']=mt_rand();
      $data['add'][$j+($n*$max_size)]['contacts_id'][]=$cont_id[$j+($n*$max_size)];
      $data['add'][$j+($n*$max_size)]['company_id']=$comp_id[$j+($n*$max_size)];
    };

    $result=req_curl(1,$link,$data);
    if (isset($result['ooops']['errors']['code'])){
      return $result;
    }
  };
};

//получаем id всех существующих контактов
function contacts_get($field_id, $enums){
  $limit_offset=0;
  $rows_range=500;
  $cont_id=array();
  do {
    $link = 'https://ko609.amocrm.ru/api/v2/leads?limit_rows=500&limit_offset='.$limit_offset;
    $result=req_curl(0,$link);
    if (isset($result['ooops']['errors']['code'])){
      return $result;
    }
    if (is_array($result)){
      $result = $result['_embedded']['items'];
      foreach ($result as $key => $value) {
        $cont_id[]=$value["id"];
      };
    };
    $limit_offset+=$rows_range;
  }while(is_array($result));
  
  if(count($cont_id)>0){
    contacts_upd($cont_id,$field_id, $enums);
  };
    
};

//основной код скрипта
function amo_mass_create($num){
  amo_aut();
  $multi_id= multitext_add();
  $enums_id=multitext_find($multi_id);
  contacts_get($multi_id,$enums_id);
  $cont_id = contacts_add($num,$multi_id,$enums_id);
  $comp_id = companies_add($num,$cont_id);
  leads_custom_add($num,$cont_id,$comp_id,0);
  leads_custom_add($num,$cont_id,$comp_id,1);
  echo "готово";
};

//amo_mass_create($_POST['num']);
amo_mass_create(3);
?>


