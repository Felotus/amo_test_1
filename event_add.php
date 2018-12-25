<?php
include 'amo_aut.php';
amo_aut();
function event_add($elem_id, $elem_type, $note_type, $text){
  $data['add'][0]['element_id']=$elem_id;
  $data['add'][0]['element_type']=$elem_type;
  $data['add'][0]['note_type']=$note_type;
  switch ($note_type) {
    case '4':
      $data['add'][0]['text']=$text;
      break;
    case '10':
      $data['add'][0]['params']['UNIQ']="8aa9ee7d3c33de7d873308e5f2afe4d5689f38be_".time().mt_rand();
      $data['add'][0]['params']['DURATION']="30";
      $data['add'][0]['params']['SRC']="http://example.com/calls/1.mp3";
      $data['add'][0]['params']['LINK']="http://example.com/calls/1.mp3";
      $data['add'][0]['params']['PHONE']=$text;
      break;
    default:
      $data['add'][0]['text']=$text;
      break;
  }
  $data['add'][0]['text']=$text;
  $link='https://ko609.amocrm.ru/api/v2/notes';
  
  $result=req_curl(1,$link,$data);
  if (isset($result['ooops']['errors']['code'])){
    return $result;
  }
};

event_add($_POST['id'],$_POST['elem_type'],$_POST['note_type'],$_POST['text']);
echo "готово";

?>