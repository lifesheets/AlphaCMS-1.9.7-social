<?php  
require ($_SERVER['DOCUMENT_ROOT'].'/system/connections/core.php');
access('management');

if (ajax() == true){
  
  ?>
  <div class="modal_bottom_title2">
  <?=lg('Загрузить zip архив')?>
  </div>
    
  <div class="modal-bottom-container">      
  <?=file::upload('/files/receiver/alpha_installer.php')?>
  </div>
    
  <div class="modal_bottom_foot">
  <button onclick='modal_bottom_close()' class='modal-bottom-button2'><?=lg('Отменить')?></button>
  </div>
  <?
  
}else{
  
  echo lg('Не удалось установить соединение');
  
}