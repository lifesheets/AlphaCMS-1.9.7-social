<?php  
$account = db::get_string("SELECT `ID`,`LOGIN` FROM `USERS` WHERE `ID` = ? LIMIT 1", [$list['OBJECT_ID']]);
?>
<div class='notif-avatar'>
<a href='/id<?=$account['ID']?>'>
<?=user::avatar($account['ID'], 50)?>
</a>
<span class='notif-icon' style='background-color: #2196F3;'><?=icons('comment', 11, 'fa-fw')?></span>  
<?php   
if ($list['TIME'] + 3600 > TM){
  
  ?><br /><span class='notif-count'><?=lg('новое')?></span><?
  
}
?>  
</div>
  
<div class='notif-info'>
  
<a href='/id<?=$account['ID']?>'><b><?=$account['LOGIN']?></b></a> <?=lg('прокомментировал(-а) ваш файл')?> -   
  
<?php

$file = db::get_string("SELECT `ID`,`NAME` FROM `FILES` WHERE `ID` = ? LIMIT 1", [$list['OBJECT_ID_LIST']]);  
if (!isset($file['ID'])){
  
  ?><font color='red'><?=lg('объект был удален')?></font><?
  
}else{
  
  ?>
  <a href='/m/files/show/?id=<?=$file['ID']?>'>  
  <?=tabs(crop_text($file['NAME'], 0, 40))?>
  </a>
  <?
  
}
  
?>
  
<br /><br />
<span class='time'><?=ftime($list['TIME'])?></span>
  
</div>