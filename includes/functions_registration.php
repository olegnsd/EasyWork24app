<?php
// Страница - Регистрация сотрудника
function fill_registration()
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	// Список подразделений
	global $depts_list;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';
	
	$registration_tpl = file_get_contents('templates/registration/registration.tpl');
	
	$no_privilege_registration_tpl = file_get_contents('templates/registration/no_privilege_registration.tpl');

	$user_obj->fill_user_data($current_user_id);
	
	 
	
	// Блок редактирования для админа
	if(!$current_user_obj->get_is_admin())
	{
		return $no_privilege_registration_tpl;
	}
	
	

	fill_depts_list(0,0,$user_depts);
	
	$add_dept_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/add_dept_form.tpl');
	
	$PARS['{DEPST_LIST}'] = $depts_list;
	
	return fetch_tpl($PARS, $registration_tpl);
}
?>