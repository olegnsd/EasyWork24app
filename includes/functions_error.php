<?php
// Ошибка 404
function fill_404($o)
{
	global $site_db, $current_user_id;
	
	$error_404_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/error/error_404.tpl');
	
	return $error_404_tpl; 
}
?>