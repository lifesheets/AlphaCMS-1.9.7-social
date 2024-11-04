<?php
  
/*
------------------------------------------------
AlphaCMS - универсальный движок для вашего сайта
E-mail администрации проекта: adm@alpha-cms.ru
Официальный сайт поддержки: alpha-cms.ru
Руководитель проекта: adm (ID 1)
------------------------------------------------
*/
  
require_once ($_SERVER['DOCUMENT_ROOT'].'/system/connections/core.php');
access('users');

if (isset($_FILES) && ajax() == true) {
  
  //Подключаем библиотеку getID3
  require ROOT.'/system/libs/getid3/getid3.php';
  $getID3 = new getID3();
  
  //Директория в которую будут загружены файлы
  $uploadDir = ROOT."/files/upload/videos/source/";
  
  //Останавливаем выгрузку, если нет файлов
  if (!isset($_FILES['file']['name'])) { file::error(); }
  
  //Подсчет количества отправляемых файлов
  $fileCount = count($_FILES['file']['name']);
  
  //Разрешенные форматы для выгрузки
  $AllowFileExt = explode(",", strtolower(preg_replace('/\s+/', '', config('VIDEOS_EXT'))));
  
  //Создаем анонимную папку "Вложения", если её ещё нет
  require (ROOT.'/modules/videos/plugins/param.php');
  
  //Определяем анонимную папку "Вложения"
  $dir = db::get_string("SELECT `ID` FROM `VIDEOS_DIR` WHERE `USER_ID` = ? AND `PRIVATE` = '3' AND `NAME` = 'Вложения' AND `ID_DIR` = '0' LIMIT 1", [user('ID')]);
  
  //Определяем тип
  $type = tabs(esc(get('type')));
  $id = intval(get('id'));
  
  //Определяем url откуда пришли
  $url = url_check_validate(base64_decode(get('url')));
  
  if ($fileCount > config('MAXFILEUPLOAD')){
    
    file::error(lg('Нельзя загружать более %d файлов за 1 раз', config('MAXFILEUPLOAD')));
    
  }
  
  if (config('FILE_ACCESS') == 0) {
    
    file::error('Выгрузка файлов на сайте приостановлена администрацией');
  
  }
  
  if (db::get_column("SELECT COUNT(*) FROM `VIDEOS` WHERE `USER_ID` = ?", [user('ID')]) >= config('VIDEOS_LIMIT')) {
    
    file::error('Вы исчерпали лимит на добавление видео');
    
  }
  
  if (db::get_column("SELECT COUNT(*) FROM `ATTACHMENTS` WHERE `USER_ID` = ? AND `ACT` = ? AND `TYPE_POST` = ? LIMIT 20", [user('ID'), 0, $type]) + $fileCount > 20){
    
    file::error('Нельзя прикреплять более 20 файлов к 1 сообщению');
    
  }
  
  /*
  ---------------------
  Мультивыгрузка файлов
  ---------------------
  */
  
  $error = null; 
  $s = 0;
  for ($i = 0; $i < $fileCount; $i++) {
    
    //Размер файла
    $FileSize = tabs($_FILES['file']['size'][$i]);
    
    //Оригинальное название файла
    $FileNameExt = $_FILES['file']['name'][$i];
    
    //Оригинальное название файла без расширения
    $FileName = tprcs(preg_replace('#\.[^\.]*$#', null, $FileNameExt));
    
    //Расширение файла без названия
    $Ext = strtolower(preg_replace('#^.*\.#', null, $FileNameExt));
    
    //Временные файлы
    $TempName = $_FILES['file']['tmp_name'][$i];

    if (db::get_column("SELECT COUNT(*) FROM `VIDEOS` WHERE `USER_ID` = ? AND `NAME` = ? LIMIT 1", [user('ID'), $FileName]) > 0){
      
      file::error('<b>'.$FileNameExt.'</b> - '.lg('видео с таким названием уже есть в ваших альбомах'));
    
    }elseif (!in_array($Ext, $AllowFileExt)) {

      file::error('<b>'.$FileNameExt.'</b> - '.lg('неверный формат видео. Допустимые форматы: %s', strtolower(preg_replace('/\s+/', '', config('VIDEOS_EXT')))));
    
    }elseif (str($type) == 0) {
      
      file::error('<b>'.$FileNameExt.'</b> - '.lg('неизвестная ошибка'));
    
    }elseif (filesize($TempName) > config('MAXFILESIZE')) {
      
      file::error('<b>'.$FileNameExt.'</b> - '.lg('размер превышает установленные ограничения. Размер должен быть не больше %s', size_file(config('MAXFILESIZE'))));
    
    }elseif (str($FileName) < 1) {
      
      file::error('<b>'.$FileNameExt.'</b> - '.lg('имя не должно быть менее 1 символа'));
    
    }elseif (str($FileName) > 200) {
      
      file::error('<b>'.$FileNameExt.'</b> - '.lg('имя не должно быть более 200 символов'));
    
    }elseif ($FileSize < 1024) {
      
      file::error('<b>'.$FileNameExt.'</b> - '.lg('слишком маленький файл'));
    
    }else{
      
      //Сохраняем файл
      if (@copy($TempName, $uploadDir.$FileName.'.'.$Ext)) {
        
        $getID3_file = $getID3->analyze($uploadDir.$FileName.'.'.$Ext);
        
        $ID = db::get_add("INSERT INTO `VIDEOS` (`HEIGHT`, `WIDTH`, `NAME`, `USER_ID`, `EXT`, `SIZE`, `TIME`, `ID_DIR`, `SHOW`, `DURATION`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$getID3_file['video']['resolution_x'], $getID3_file['video']['resolution_y'], $FileName, user('ID'), $Ext, filesize($TempName), TM, $dir['ID'], intval(get('show')), tabs($getID3_file['playtime_string'])]);        
        
        if ($id == 0) {
          
          db::get_add("INSERT INTO `ATTACHMENTS` (`USER_ID`, `OBJECT_ID`, `TYPE`, `TYPE_POST`, `TIME`, `ID_POST`, `ACT`) VALUES (?, ?, ?, ?, ?, ?, ?)", [user('ID'), $ID, 'videos', $type, TM, 0, 0]);
          
        }else{
          
          db::get_add("INSERT INTO `ATTACHMENTS` (`USER_ID`, `OBJECT_ID`, `TYPE`, `TYPE_POST`, `TIME`, `ID_POST`, `ACT`) VALUES (?, ?, ?, ?, ?, ?, ?)", [user('ID'), $ID, 'videos', $type, TM, $id, 1]);
          
        }
        
        rename($uploadDir.$FileName.'.'.$Ext, $uploadDir.$ID.'.'.$Ext);       
        ffmpeg::screen($uploadDir.$ID.'.'.$Ext, ROOT.'/files/upload/videos/screen/'.$ID.'.jpg', '00:00:02');
        
        if (is_file(ROOT.'/files/upload/videos/screen/'.$ID.'.jpg')){
          
          crop_image(ROOT.'/files/upload/videos/screen/'.$ID.'.jpg', ROOT.'/files/upload/videos/screen/240x240/'.$ID.'.jpg', 240, 240);
        
        }
        
        balls_add('VIDEOS');
        rating_add('VIDEOS');
        
        $s++;
      
      }else{

        file::error();
        
      }
      
    }
    
  }
  
  /*
  --------------------------------
  Действия после успешной загрузки
  --------------------------------
  */
  
  if ($s > 0) {
    
    file::update($url, '#upload-attachments-result');
    
  }

}else{
  
  file::error('Не удалось установить соединение с ресивером');

}