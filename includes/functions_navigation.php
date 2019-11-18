<?php
// Строка навигации
function fill_nav($o)
{
	global $site_db, $user_obj,$current_user_id;
	
	$nav_main_tpl = file_get_contents('templates/navigation/nav_main.tpl');
	
	$nav_current_tpl = file_get_contents('templates/navigation/nav_current.tpl');
	
	$nav_a_tpl = file_get_contents('templates/navigation/nav_a.tpl');
	
	$nav_sep_tpl = file_get_contents('templates/navigation/nav_sep.tpl');
	
	$nav_block_tpl = file_get_contents('templates/navigation/nav_block.tpl');
	
	$not_with_main  = 1;
	 
	if(!$o)
	{
		return '';
	}
	switch($o)
	{
		case 'ucontrol':
			
			$PARS['{TITLE}'] = 'Статистика';
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
				
		break;
		case 'tasks':
		
			if($_GET['tid'])
			{
				$rf = urldecode($_GET['rf']);
				
				$rf = $rf ? '?'.$rf : '';
				
				$PARS['{TITLE}'] = 'Вернуться к списку задач';
				$PARS['{HREF}'] = '/tasks'.$rf;
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl); 
				
				$PARS['{TITLE}'] = 'Просмотр задачи';
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl); 
			}
			else
			{
				$PARS['{TITLE}'] = 'Задачи';
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
			}
			
			 
			
		break;
		case 'c_structure':
			
			$PARS['{TITLE}'] = 'Структура компании';
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
			
		break;
		case 'org':
			
			$PARS['{TITLE}'] = 'Сотрудники';
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
		break;
		case 'disk':
			
			if($_GET['act']=='av')
			{
				$PARS['{TITLE}'] = 'Доступные файлы';
			
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
			
			}
			else if($_GET['act']=='co')
			{
				$PARS['{TITLE}'] = 'Файлы компании';
			
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
			
			}
			else
			{
				$PARS['{TITLE}'] = 'Мои файлы';
			
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
			}
			
		break;
		case 'posttr':
			
			$PARS['{TITLE}'] = 'Трекинг. Почта России.';
			
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
			
		break;
		case 'evcal':
			
			$PARS['{TITLE}'] = 'Календарь событий';
			
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
			
		break;
		case 'cnews':
			
			$PARS['{TITLE}'] = 'Новости компании';
			
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
			
		break;
		case 'tasks_projects':
			if($_GET['id'])
			{
				if($_GET['referer']=='part')
				{ 
					$PARS['{HREF}'] = '/projects?part=1';
					$PARS['{TITLE}'] = 'Проекты, в которых участвую';
				}
				else
				{
					$PARS['{HREF}'] = '/projects'.$part;
					$PARS['{TITLE}'] = 'Мои проекты';
				}
				
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				
				$PARS['{TITLE}'] = 'Просмотр проекта';
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			}
			else if($_GET['part']==1)
			{
				$PARS['{TITLE}'] = 'Проекты, в которых участвую';
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				$PARS['{TITLE}'] = 'Мои проекты';
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
		break;
		case 'error404':
			$PARS['{TITLE}'] = 'Страница не найдена';
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
		break;
		case 'rfw':
		
			$PARS['{TITLE}'] = 'Отстранение от работы';
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
			
		break;
		case 'notes':
			
			if($_GET['av'])
			{
				// Имя
				$PARS['{TITLE}'] = 'Чужие заметки';
			}
			else
			{
				// Имя
				$PARS['{TITLE}'] = 'Мои заметки';
			}
			
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
		
		break;
		
		case 'external':
			
				$service_id = $_GET['s_id'] > 0 ? $_GET['s_id'] : 1;
				
			 	$service_name = get_external_service_name_by_service_id($service_id);
				// Имя
				$PARS['{TITLE}'] = $service_name;
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
		
		break;
		case 'deputy':
			
			if($_GET['my'])
			{
				// Имя
				$PARS['{TITLE}'] = 'Я замещаю';
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				// Имя
				$PARS['{TITLE}'] = 'Мои заместители';
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			
		break;
		case 'reprimand':
			
			if($_GET['wks'])
			{
				// Имя
				$PARS['{TITLE}'] = 'Выговоры моим сотрудникам';
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				// Имя
				$PARS['{TITLE}'] = 'Мои выговоры';
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			
		break;
		case 'ofdocs':
			
			if($_GET['wks'])
			{
				// Имя
				$PARS['{TITLE}'] = 'Официальные документы моих сотрудников';
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				// Имя
				$PARS['{TITLE}'] = 'Официальные документы';
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			
		break;
		case 'planning':
			
			// Имя
			$PARS['{TITLE}'] = 'Планирование отсутствий';
				
				 
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			
		break;
		case 'registration':
			
			// Имя
			$PARS['{TITLE}'] = 'Регистрация';
				
				 
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			
		break;
		case 'colleagues':
			
			// Имя
			$PARS['{TITLE}'] = 'Мои коллеги';
				
				 
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			
		break;
		case 'msgs':
			
			$PARS['{HREF}'] = '/msgs';
					
			$PARS['{TITLE}'] = 'Мои диалоги';
				
			$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
			
			// Заполянем объект пользователя
			$user_obj->fill_user_data($_GET['id']);
			
			// Имя
			$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
			
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
		break;
		case 'msgs_group':
		
			$PARS['{TITLE}'] = 'Планерка';
				
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			
		break;
		case 'msgs_group_add':
			
			$PARS['{TITLE}'] = 'Организация планерки';
				
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
				
		break;
		case 'dialogs':
			
			// Заполянем объект пользователя
			$user_obj->fill_user_data($_GET['user_id']);
			
			// Имя
			$PARS['{TITLE}'] = 'Мои диалоги';
				
				 
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			
		break;
		case 'finance':
			
			if($_GET['finance_id'])
			{
				$PARS['{HREF}'] = '/finances';
					
				$PARS['{TITLE}'] = 'Внешние финансы';
				
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				
				// Данные счета
				$sql = "SELECT * FROM ".FINANCES_TB." WHERE finance_id='".$_GET['finance_id']."'";
				
				$finance_data = $site_db->query_firstrow($sql);
				
				// Имя
				$PARS['{TITLE}'] = 'Редактирование счета "'.$finance_data['finance_name']."\"";
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				if($_GET['av'])
				{
					// Имя
					$PARS['{TITLE}'] = 'Чужие внешние финансы';
					
					$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
				}
				else
				{
					// Имя
					$PARS['{TITLE}'] = 'Внешние финансы';
					
					$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
				}
			}
		break;
		case 'auto':
		
			$PARS['{TITLE}'] = 'Мой автотранспорт';
				
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			
			
		break;
		case 'camera':
		
			$PARS['{TITLE}'] = 'Мои видеонаблюдения';
				
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			
			
		break;
		
		case 'wktime':
		
			if($_GET['cmp'])
			{
				// Имя
				$PARS['{TITLE}'] = 'Компьютер';
					
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				if($_GET['user_id']!=$current_user_id && $_GET['user_id'])
				{
					$PARS['{HREF}'] = '/workers';
				
					$PARS['{TITLE}'] = 'Мои сотрудники';
				
					$nav_string1 = fetch_tpl($PARS, $nav_a_tpl);
				
					// Заполянем объект пользователя
					$user_obj->fill_user_data($_GET['user_id']);
					
					$PARS['{HREF}'] = '/id'.$_GET['user_id'];
					
					// Имя
					$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
					
					$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
					
					// Имя
					$PARS['{TITLE}'] = 'Присутствие на работе';
					
					$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				}
				else
				{
					// Имя
					$PARS['{TITLE}'] = 'Присутствие на работе';
						
					$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
				}

			}
		break;
		
		case 'computer':
			
			$user_id = get_user_id_by_computer_id($_GET['id']);
			
			// Заполянем объект пользователя
			$user_obj->fill_user_data($user_id);
			
			$PARS['{HREF}'] = '/id'.$user_id;
			
			// Имя
			$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
			
			$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
			
			
			// Имя
			$PARS['{TITLE}'] = 'Присутствие на работе';
			
			$PARS['{HREF}'] = '/wktime/'.$user_id;
			
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_a_tpl);
			
			// Имя
			$PARS['{TITLE}'] = 'Редактирование компьютера';
			
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			
		break;
		case 'money':
		
			if($_GET['id'])
			{
				$PARS['{HREF}'] = '/money';
					
				$PARS['{TITLE}'] = 'Финансы';
				
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				
				if(!check_user_access_to_user_content($_GET['id'], array(0,1,0,0,1)) && $_GET['accruals'])
				{
					$PARS['{TITLE}'] = 'Мои начисления';
					
					$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				}
				else
				{
					if($_GET['accruals'] && check_user_access_to_user_content($_GET['id'], array(0,1,0,0,1)))
					{
						$PARS['{TITLE}'] = 'Начисления';
					}
					else
					{	
						$PARS['{TITLE}'] = 'Выплаты';
					}
					
					$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
					
					// Заполянем объект пользователя
					$user_obj->fill_user_data($_GET['id']);
				
					// Имя
					$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
				
					$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				}
				
				 
				 
				 
				 
			}
			else
			{
				$PARS['{TITLE}'] = 'Мои финансы';
					
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			
		break;
		case 'goods':
			
			if($_GET['good_id'])
			{ 
				$PARS['{TITLE}'] = 'Мое имущество';
				
				$PARS['{HREF}'] = '/goods/'.$current_user_id;
				
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				
				$PARS['{TITLE}'] = 'Редактирование';
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			}
			else if($_GET['wks'])
			{
				$PARS['{TITLE}'] = 'Имущество моих сотрудников';
				
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else if($current_user_id==$_GET['user_id'])
			{
				$PARS['{TITLE}'] = 'Мое имущество';
				
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				/*// Заполянем объект пользователя
				$user_obj->fill_user_data($_GET['user_id']);
				
				// Имя
				$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
				$PARS['{TITLE}'] = 'Имущество';
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);*/
			}

		break;
		case 'efficiency':
			
			// Заполянем объект пользователя
			$user_obj->fill_user_data($_GET['id']);
				
			// Имя
			$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();;
			
			// Имя
			$PARS['{HREF}'] = '/tasks?id='.$_GET['id'];
					
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_a_tpl);
			
			$PARS['{TITLE}'] = 'График эффективности';
				
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
		break;
		case 'deals':
			
			if($_GET['wks'])
			{
				$PARS['{TITLE}'] = 'Сделки моих сотрудников';
				
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			elseif($_GET['user_id'])
			{
				$PARS['{TITLE}'] = 'Мои сделки';
				
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				$PARS['{TITLE}'] = 'Поиск сделок по всей организации';
				
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			
		break;
		
		case 'deal_edit':
			
			// Имя
			$PARS['{TITLE}'] = 'Мои сделки';
			
			// Имя
			$PARS['{HREF}'] = '/deals/'.$current_user_id;
					
			$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
			
			$PARS['{TITLE}'] = 'Редактирование сделки';
				
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			
		break;
		case 'clients':
		
			if($_GET['import'])
			{
				$PARS['{TITLE}'] = 'Импорт базы контрагентов';
				
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else if($_GET['show']==1 && $_GET['id'])
			{
				$PARS['{HREF}'] = '/clients/'.$current_user_id;
				$PARS['{TITLE}'] = 'Клиенты';
				
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				
				$PARS['{TITLE}'] = 'Просмотр';
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			}
			else if($_GET['wks'])
			{
				$PARS['{TITLE}'] = 'Клиенты моих сотрудников';
				
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else if($_GET['user_id'])
			{
				$PARS['{TITLE}'] = 'Мои клиенты';
				
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else if($_GET['msg']==1)
			{
				
				$PARS['{HREF}'] = '/clients';
				$PARS['{TITLE}'] = 'Мои клиенты';
				
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				
				$client_data = get_client_data($_GET['id']);
				
				
				$PARS['{TITLE}'] = 'Диалог с клиентом';
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
				
				// Имя
				$PARS['{TITLE}'] = $client_data['client_name'];
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			}
			else if($_GET['files']==1)
			{
				$PARS['{HREF}'] = '/clients';
				$PARS['{TITLE}'] = 'Мои клиенты';
				
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				
				$client_data = get_client_data($_GET['id']);
				
				
				$PARS['{TITLE}'] = 'Файлы клиента';
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
				// Имя
				$PARS['{TITLE}'] = $client_data['client_name'];
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				$PARS['{TITLE}'] = 'Поиск клиентов по всей организации';
				
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			
		break;
		
		case 'task_to_users':
		
			$PARS['{TITLE}'] = 'Мои поставленные задачи';
			
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			
		break;
		case 'tree':
		
			$PARS['{TITLE}'] = 'Проверка подлинности';
						
			$nav_string = fetch_tpl($PARS, $nav_current_tpl);
				
			// Заполянем объект пользователя
			$user_obj->fill_user_data($_GET['user_id']);
				
			// Имя
			$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();;
				
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
		break;
		case 'contacts':
			
			if(!$_GET['user_id'])
			{
				$PARS['{TITLE}'] = 'Поиск контактов';
						
				$nav_string = fetch_tpl($PARS, $nav_current_tpl);
			}
			else if($_GET['user_id']==$current_user_id || !$_GET['user_id'])
			{  
				$PARS['{TITLE}'] = 'Мои контакты';
						
				$nav_string = fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				// Заполянем объект пользователя
				$user_obj->fill_user_data($_GET['user_id']);
				
				// Имя
				$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();;
				
				 
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
				$PARS['{TITLE}'] = 'Контакты';
						
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			}
			
		break;
		case 'active_log':
			
			// Имя
				$PARS['{TITLE}'] = 'Лог активности';
						
				$nav_string = $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
				// Заполянем объект пользователя
				$user_obj->fill_user_data($_GET['user_id']);
				
				// Имя
				$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();;
				
				 
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
		break;
		case 'personal':
			
			// Заполянем объект пользователя
			$user_obj->fill_user_data($_GET['user_id']);
			
			// Имя
			$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();;
				
				 
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			
			
			
					
		break;
		
		case 'files':
			
			
			// Если просмотр папки с файлами
			if($_GET['folder_id'])
			{
				if($_GET['av'])
				{
					// Имя
					$PARS['{TITLE}'] = 'Чужие файлы';
					
					$PARS['{HREF}'] = '/files?av=1';
					
					$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				}
				else if($_GET['s'])
				{
					// Имя
					$PARS['{TITLE}'] = 'Общие файлы';
					
					$PARS['{HREF}'] = '/files?s=1';
					
					$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				}
				else
				{
					// Имя
					$PARS['{TITLE}'] = 'Мои файлы';
					
					$PARS['{HREF}'] = '/files';
					
					$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				
				}
				// Название папки
				$sql = "SELECT folder_name FROM ".FOLDERS_TB." WHERE folder_id='".$_GET['folder_id']."'";
				
				$row = $site_db->query_firstrow($sql);
				
				// Имя
				$PARS['{TITLE}'] = $row['folder_name'];
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				if($_GET['av'])
				{
					// Имя
					$PARS['{TITLE}'] = 'Чужие файлы';
					
					$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
				}
				else if($_GET['s'])
				{
					// Имя
					$PARS['{TITLE}'] = 'Общие файлы';
					
					$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
				}
				else
				{
					// Имя
					$PARS['{TITLE}'] = 'Мои файлы';
					
					$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
				}
			}
			
		break;
		case 'user_comments':
			
			// Заполянем объект пользователя
			$user_obj->fill_user_data($_GET['id']);
			
			// Имя
			$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();;
			
			 
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			
			// Имя
			$PARS['{TITLE}'] = 'Отзывы';
			
			 
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			
		break;
		case 'work':
		
			if($_GET['id']==$current_user_id || !$_GET['id'])
			{
					// Имя
				$PARS['{TITLE}'] = 'Мой круг обязанностей';
			
			 
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			}
			else
			{
				
				$PARS['{HREF}'] = '/workers';
				
				$PARS['{TITLE}'] = 'Мои сотрудники';
				
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				 
				// Имя
				$PARS['{TITLE}'] = 'Круг обязанностей';
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				 
				// Заполянем объект пользователя
				$user_obj->fill_user_data($_GET['id']);
			 
				// Имя
				$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
			 
			}
			 
		break;
		case 'user_work':
			// Заполянем объект пользователя
			$user_obj->fill_user_data($_GET['id']);
			
			// Имя
			$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();;
			
			 
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			
			 
			
			// Имя
			$PARS['{TITLE}'] = 'Круг обязанностей';
			
			 
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
		break;
		case 'boss':
			// Имя
			$PARS['{TITLE}'] = 'Мое руководство';
				
			$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
		break;
		case 'worker':
			
			// Заполянем объект пользователя
			$user_obj->fill_user_data($_GET['id']);
			
			// Имя
			$PARS['{TITLE}'] = 'Мои сотрудники';
			
			$PARS['{HREF}'] = '/workers';
			
			$nav_string = $nav_sep_tpl.fetch_tpl($PARS, $nav_a_tpl);
				
			if($_GET['date'])
			{
				// Имя
				$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
			
				$PARS['{HREF}'] = '/workers?id='.$_GET['id'];
			
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_a_tpl);
			
				// Дата
				$PARS['{TITLE}'] = formate_date_rus($_GET['date'], 1);
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
			}
			else
			{
				// Имя
				$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			
			}
			 
			
		break;
		
		case 'settings':
			
			if($_GET['id'])
			{
				$PARS['{TITLE}'] =  'Настройки пользователя';
			}
			else
			{
				// Имя
				$PARS['{TITLE}'] =  'Мои настройки';
			}
				
			$nav_string = fetch_tpl($PARS, $nav_current_tpl);
				
		break;
		
		/*case 'my_tasks':
			
			if($_GET['date'])
			{
				$PARS['{TITLE}'] = 'Мои задачи';
			
				$PARS['{HREF}'] = '/tasks';
			
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
			
				// Дата
				$PARS['{TITLE}'] = 'Задачи на '.formate_date_rus($_GET['date'], 1);
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
			}
			else
			{
				$PARS['{TITLE}'] = 'Мои задачи';
				
				$nav_string .= fetch_tpl($PARS, $nav_current_tpl);
			
			}
				
		break;
		
		case 'tasks':
			
			if($_GET['date'])
			{
				$PARS['{HREF}'] = '/workers';
				
				$PARS['{TITLE}'] = 'Мои сотрудники';
				
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				
				 
				// Имя
				$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
				
				$PARS['{HREF}'] = '/tasks?id='.$_GET['id'];
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_a_tpl);
				
				// Дата
				$PARS['{TITLE}'] = 'Задачи на '.formate_date_rus($_GET['date'], 1);
				
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
				
			}
			else
			{
				$PARS['{HREF}'] = '/workers';
				
				$PARS['{TITLE}'] = 'Мои сотрудники';
				
				$nav_string .= fetch_tpl($PARS, $nav_a_tpl);
				
				 
				
				// Заполянем объект пользователя
				$user_obj->fill_user_data($_GET['id']);
			
				// Имя
				$PARS['{TITLE}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
			
				$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
			
			}
				
		break;*/
		
		case 'workers':
			
			// Имя
			$PARS['{TITLE}'] =  'Мои сотрудники';
				
			$nav_string = fetch_tpl($PARS, $nav_current_tpl);
				
		break;
	}
	
	if($not_with_main)
	{
		$nav_str =  $nav_string;
	}
	else
	{
		$nav_str =  $nav_main_tpl.' '.$nav_string;
	}
	
	$PARS_1['{NAV}'] = $nav_str;
	
	return fetch_tpl($PARS_1, $nav_block_tpl);
	 
}
?>