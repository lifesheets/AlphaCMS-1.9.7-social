<?php  
require ($_SERVER['DOCUMENT_ROOT'].'/system/connections/core.php');
access('users');
get_check_valid();

if (ajax() == true){

  $type = tabs(get('type'));
  $url = tabs(get('url'));
  $id = intval(get('id'));
  
  ?><div class="modal_bottom_title"><?
  
  if (config('PRIVATE_PHOTOS')){
    
    ?>
    <div class="modal_bottom_icons_optimize modal_bottom_title_active"><span class="modal_bottom_icons_circle"><?=icons('image', 20)?></span><span class='modal_bottom_title_text'><?=lg('Фото')?></span></div>
    <?
    
  }
  
  if (config('PRIVATE_VIDEOS')){
    
    ?>
    <div id="modal_bottom_open_set" onclick="upload('/system/AJAX/php/attachments/videos.php?type=<?=$type?>&id=<?=$id?>&url=<?=$url?>&show=<?=intval(get('show'))?>&<?=TOKEN_URL?>', 'attachments_upload')" class="modal_bottom_icons_optimize"><span class="modal_bottom_icons_circle"><?=icons('film', 20)?></span><span class="modal_bottom_title_text"><?=lg('Видео')?></span></div>
    <?
    
  }
  
  if (config('PRIVATE_MUSIC')){
    
    ?>
    <div id="modal_bottom_open_set" onclick="upload('/system/AJAX/php/attachments/music.php?type=<?=$type?>&id=<?=$id?>&url=<?=$url?>&show=<?=intval(get('show'))?>&<?=TOKEN_URL?>', 'attachments_upload')" class="modal_bottom_icons_optimize"><span class="modal_bottom_icons_circle"><?=icons('music', 20)?></span><span class="modal_bottom_title_text"><?=lg('Музыка')?></span></div>
    <?
    
  }
  
  if (config('PRIVATE_FILES')){
    
    ?>
    <div id="modal_bottom_open_set" onclick="upload('/system/AJAX/php/attachments/files.php?type=<?=$type?>&id=<?=$id?>&url=<?=$url?>&show=<?=intval(get('show'))?>&<?=TOKEN_URL?>', 'attachments_upload')" class="modal_bottom_icons_optimize"><span class="modal_bottom_icons_circle"><?=icons('file', 20)?></span><span class="modal_bottom_title_text"><?=lg('Файлы')?></span></div>
    <?
    
  }
  
  hooks::challenge('attachments_ajax', 'attachments_ajax');
  hooks::run('attachments_ajax');
  
  ?></div><?
    
  if (config('PRIVATE_PHOTOS')){
    
    ?><div class="modal-bottom-container"><?
    
    file::upload('/files/receiver/attachments_photos.php?type='.$type.'&id='.$id.'&url='.$url.'&show='.intval(get('show')));
    
    ?><div id="photos_list" class="modal-bottom-container2"><?                                  
      
    $data = db::get_string_all("SELECT `SHIF`,`ID`,`EXT` FROM `PHOTOS` WHERE `USER_ID` = ? ORDER BY `TIME` DESC LIMIT 24", [user('ID')]);
    while ($list = $data->fetch()){
      
      ?><label onclick="checkbox(<?=$list['ID']?>)" class="checkbox-optimize"><span class="checkbox<?=$list['ID']?> checkbox-op-img check-close"><?=icons('check', 20)?></span><input type='checkbox' class='attachments-photos-checkbox' name='photos' value='<?=$list['ID']?>' id='chset<?=$list['ID']?>'><img src='/files/upload/photos/240x240/<?=$list['SHIF']?>.jpg' class='attachments-photos-img'></label><?
      
    }
    
    ?></div><?
      
    if (db::get_column("SELECT COUNT(*) FROM `PHOTOS` WHERE `USER_ID` = ? LIMIT 1", [user('ID')]) > 24) {
      
      ?><center><span onclick="show_more('/system/AJAX/php/attachments/photos_list.php', '#show_more', '#photos_list', 24, 'append')" class="button" id="show_more" count_show="24" count_add="24" name_show="<?=lg('Показать еще')?>" name_hide="<?=lg('Конец')?>"><?=lg('Показать еще')?></span></center><br /><? 
      
    }
    
    ?>
    </div>
      
    <center><span onclick='modal_bottom_close()' class='modal-bottom-button'><?=lg('Отменить')?></span> <span class="bclose modal-bottom-button-at" id="get_check" idt="<?=$id?>" type="photos" ptype="<?=$type?>" action="<?=base64_decode($url)?>"><?=lg('Прикрепить')?></span></center>
      
    <script type="text/javascript" src="/system/AJAX/attachments/checkbox.js?version=<?=config('ACMS_VERSION')?>"></script>
    <?
      
  }else{
    
    ?><div class='list-menu'><?
    echo lg('Модуль отключен администратором');
    ?></div><?
    
  }
  
}else{
  
  echo lg('Не удалось установить соединение');
  
}