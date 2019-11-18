<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	case 'edit_dept':
		
		$dept_id = value_proc($_POST['dept_id']);
		
		// Блок редактирования для админа
		if(!$current_user_obj->get_is_admin())
		{
			exit();
		}
		
		echo fill_dept_edit_form($dept_id);
		
	break;
	case 'delete_dept':
		
		$dept_id = value_proc($_POST['dept_id']);
		
		// Блок редактирования для админа
		if(!$current_user_obj->get_is_admin())
		{
			exit();
		}
		
		echo delete_company_dept($dept_id);
		
		
	break;
	case 'show_workers_list_in_dept':
		
		$dept_id = value_proc($_POST['dept_id']);
		
		$workers_list = fill_dept_workers_list_in_structure($dept_id);
		
		echo $workers_list;
		
	break;
	// Добавить контакт сотрудника
	case 'get_dept_info':
	
		$depts_arr = json_decode(str_replace('\\', '', $_POST['depts_arr']));
		
		$depts_info = structure_get_dept_data($depts_arr, $cont_type);

		echo json_encode(array('depts_info' => $depts_info));
		
	break;
	
	case 'save_dept':
		
		$parent_dept = value_proc($_POST['parent_dept']);
		
		$dept_name = value_proc($_POST['dept_name']);
		
		$dept_head = value_proc($_POST['dept_head']);
		
		$dept_id = value_proc($_POST['dept_id']);
		
		if(!$dept_name)
		{
			$error = 1;
		}
		
		// список дочерних отделов
		$depts_arr = get_company_dept_childs($dept_id);
		
		if($dept_id==$parent_dept)
		{
			$error = 2;
		}
		else if(in_array($parent_dept, $depts_arr))
		{
			$error = 3;
		}
	
		// Для админа
		if(!$current_user_obj->get_is_admin())
		{
			exit();
		}
		
		if(!$error)
		{
			
			if($dept_id==1)
			{
				$sql = "UPDATE tasks_company_depts SET dept_name='$dept_name' WHERE dept_id='$dept_id'";
			}
			else
			{
				$sql = "UPDATE tasks_company_depts SET dept_name='$dept_name', dept_parent_id='$parent_dept' WHERE dept_id='$dept_id'";
			}
			 
			$site_db->query($sql);
			
			// Руководитель отдела
			$head_dept_user = get_head_dept_user_id($dept_id);
			
			// Удаляем руководителя
			if(!$dept_head && $head_dept_user)
			{ 
				$sql = "DELETE FROM tasks_company_depts_users WHERE dept_id='$dept_id' AND is_head=1"; 
				
				$site_db->query($sql);
			}
			else if($dept_head > 0 && $head_dept_user && $head_dept_user!=$dept_head)
			{ 
				$sql = "UPDATE tasks_company_depts_users SET user_id='$dept_head' WHERE dept_id='$dept_id' AND is_head=1"; 
				
				$site_db->query($sql);
			}
			else if($dept_head > 0 && !$head_dept_user)
			{  
				add_user_to_dept($dept_id, $dept_head, 1);
			}
			
			$success = 1;
		}
		
		echo json_encode(array('success'=> $success, 'error' =>$error));
		
	break;
	case 'add_dept':
		
		$parent_dept = value_proc($_POST['parent_dept']);
		
		$dept_name = value_proc($_POST['dept_name']);
		
		$dept_head = value_proc($_POST['dept_head']);
		
		if(!$parent_dept)
		{
			exit();
		}
		
		// Для админа
		if(!$current_user_obj->get_is_admin())
		{
			exit();
		}
		
		if(!$dept_name)
		{
			$error = 1;
		}
		
		if(!$error)
		{
			$sql = "INSERT INTO tasks_company_depts SET dept_name='$dept_name', dept_parent_id='$parent_dept', date_add=NOW()";
			
			$site_db->query($sql);
			
			$dept_id = $site_db->get_insert_id();
			
			if($dept_head > 0)
			{
				// Добавить руководителя
				add_user_to_dept($dept_id, $dept_head, 1);
			}
			
			$success = 1;
		}
		
		echo json_encode(array('success'=> $success, 'error' =>$error));
		
	break;
	// Добавить контакт сотрудника
	case 'get_more':
		 
		$search_word = value_proc($_POST['search_word']);
		
		$dept_id = value_proc($_POST['dept_id']);
			
		$page = value_proc($_POST['page']);
		
		$user_is_fired = value_proc($_POST['user_is_fired']);
		
		$users_list = fill_org_list($page, $search_word, $dept_id, $user_is_fired);
		
		echo $users_list;
			
	break;
	
	case 'search_users':
		
		$search_word = value_proc($_POST['search_word']);
		
		$dept_id = value_proc($_POST['dept_id']);
		
		$user_is_fired = value_proc($_POST['user_is_fired']);
		
		$users_list = fill_org_list_cont($search_word, $dept_id, $user_is_fired);
		
		echo $users_list;
		
	break;
	
}

?>