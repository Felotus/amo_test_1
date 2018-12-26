<?php
include 'amo_aut.php';
function event_add($elem_id, $elem_type, $note_type, $text, $hash, $link){
  $data['add'][0]['element_id']=$elem_id;
  $data['add'][0]['element_type']=$elem_type;
  $data['add'][0]['note_type']=$note_type;
  switch ($note_type) {
    case '4':
      $data['add'][0]['text']=$text;
      break;
    case '10':
      $data['add'][0]['params']['UNIQ']=$hash."_".time().mt_rand();
      $data['add'][0]['params']['DURATION']="30";
      $data['add'][0]['params']['SRC']="http://example.com/calls/1.mp3";
      $data['add'][0]['params']['LINK']="http://example.com/calls/1.mp3";
      $data['add'][0]['params']['PHONE']=$text;
      break;
    default:
      $data['add'][0]['text']=$text;
      break;
  }
  $links=$link.'/api/v2/notes';
  
  $result=req_curl(POST_REQ, $links, $data);

};

try {
  amo_aut($link, $mail, $hash);
  event_add($_POST['id'], $_POST['elem_type'], $_POST['note_type'], $_POST['text'], $hash, $link);
  echo "готово";
} catch ( Exception $e ) {
  echo "Произошла ошибка: ".$e->getMessage().PHP_EOL." Код: ".$e->getCode();
}
