<?php  
html::title('Настройки');
livecms_header();
access('users');

?><div class='list-body'><?
direct::components(ROOT.'/users/account/settings/components/', 0);
?></div><?

back('/account/cabinet/');
acms_footer();