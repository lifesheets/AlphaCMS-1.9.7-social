<?php

$column = db::get_column("SELECT COUNT(*) FROM `BLOGS` WHERE `PRIVATE` = '0' AND `SHARE` = '0'");
$spage = spage($column, PAGE_SETTINGS);
$page = page($spage);
$limit = PAGE_SETTINGS * $page - PAGE_SETTINGS;

if ($column == 0){ 
  
  html::empty('Пока пусто');
  
}

define('URL_BLOGS', '/m/blogs/?page='.tabs(get('page')));
$data = db::get_string_all("SELECT * FROM `BLOGS` WHERE `PRIVATE` = '0' AND `SHARE` = '0' LIMIT ".$limit.", ".PAGE_SETTINGS);
while ($list = $data->fetch()) {
  
  require (ROOT.'/modules/blogs/plugins/list.php');
  echo $blogs_list;
  
}

get_page('/m/blogs/?', $spage, $page, 'list');