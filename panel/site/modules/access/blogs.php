<?php

$type = 'private_blogs';
$type_tumb = 'pb';
$type_ini = 'PRIVATE_BLOGS';
$type_text = "Блоги";

?>

<div id='<?=$type?>'>
  
<?php

if (config($type_ini) == 0){
  
  $value = 1;
  $tumb = "tumb";
  
}else{
  
  $value = 0;
  $tumb = "tumb2";
  
}
  
if (get('private') == $type){
  
  ini::upgrade(ROOT.'/system/config/global/settings.ini', $type_ini, $value);
  
}

?>

</div>

<div class='list-menu'>
  
<b><?=icons('book', 18, 'fa-fw')?> <?=lg($type_text)?></b>
<input onclick="request('/admin/site/modules/?get=main&mod=index&private=<?=$type?>', '#<?=$type?>')" class="input-tumb" type="checkbox" id="<?=$type_tumb?>"><label class="<?=$tumb?> thumb-optimize" for="<?=$type_tumb?>"></label></input>
  
</div>