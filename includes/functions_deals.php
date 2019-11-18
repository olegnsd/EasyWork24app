<?php
// Страница клиентов сотрудника
function fill_deals($user_id, $deal_id=0, $downl_call=0)
{
	global $site_db, $current_user_id, $_CURRENT_USER_WORKERS_ARR, $_CURRENT_USER_DEPUTY_WORKERS_ARR, $current_user_obj;
	
	if($downl_call){
        downl_call($deal_id, $user_id);
    }
	
	$main_tpl = file_get_contents('templates/deals/deals.tpl');
	
	$deals_top_menu_tpl = file_get_contents('templates/deals/deals_top_menu.tpl');
	
	$workers_deals_top_menu_tpl = file_get_contents('templates/deals/workers_deals_top_menu.tpl');
	
	$deals_top_menu_deals_search_tpl = file_get_contents('templates/deals/deals_top_menu_deals_search.tpl');

        $deals_top_menu_sms_tpl = file_get_contents('templates/deals/deals_top_menu_sms.tpl');
	
	$deal_add_form_tpl = file_get_contents('templates/deals/deal_add_form.tpl');
	
	$no_deals_tpl  = file_get_contents('templates/deals/no_deals.tpl');
	
	$more_deals_btn_tpl = file_get_contents('templates/deals/more_deals_btn.tpl');
	
	$deals_search_form_tpl = file_get_contents('templates/deals/deals_search_form.tpl');
	
	$deals_clear_date_tpl = file_get_contents('templates/deals/deals_clear_date.tpl');
	
	$deal_sales_funnel_block_tpl = file_get_contents('templates/deals/deal_sales_funnel_block.tpl');
	
	// Выводим кнопку "показать все", если был фильтр по дате
	if($_GET['date_from']!='' || $_GET['date_to']!='' || $_GET['group_id']!='' || $_GET['status']!='' || $_GET['call']!='')
	{
		$clear_date_block = $deals_clear_date_tpl;
		$_SESSION['deal_date_from'] = $_GET['date_from'];
		$_SESSION['deal_date_to'] = $_GET['date_to'];
		$_SESSION['deal_group_id'] = $_GET['group_id'];
		$_SESSION['deal_status'] = $_GET['status'];
		$_SESSION['deal_call'] = $_GET['call'];
	}
	else
	{
		unset($_SESSION['deal_date_from']);
		unset($_SESSION['deal_date_to']);
		unset($_SESSION['deal_group_id']);
		unset($_SESSION['deal_status']);
		unset($_SESSION['deal_call']);
	}
	
	// Если у пользователя нет сотрудников, не выводим раздел "Клиенты моих сотурдников"
	if($current_user_obj->get_user_is_dept_head())
	{
		$workers_deals_top_menu = $workers_deals_top_menu_tpl;
		$deals_search_top_menu = $deals_top_menu_deals_search_tpl;
	} 
        if($current_user_obj->get_is_admin())
        {
		$deals_sms_top_menu = $deals_top_menu_sms_tpl;
        }
	
	if($_GET['list']=='all' && !$current_user_obj->get_user_is_dept_head())
	{
		header('Location: /deals/'.$current_user_id);
	}
	
	### Верхнее меню
	// Подсвечивает табы
	if($current_user_id==$user_id)
	{
		$active_menu_1 = 'menu_active';
		$deal_list_type = 'my';
	}
	else if($_GET['list']=='av')
	{
		// Делаем отметку о просмотре переданных сделок
		set_noticed_new_avalible_deals_for_user();
		$active_menu_4 = 'menu_active';
		$deal_list_type = 'av';
	}
	else if($_GET['list']=='wks')
	{
		$active_menu_2 = 'menu_active';
		$deal_list_type = 'wks';
	}
	else if($_GET['list']== 'all')
	{
		$active_menu_3 = 'menu_active';
		$deal_list_type = 'all';
	}
else if($_GET['sms']== '1')
	{
		$active_menu_5 = 'menu_active';
                $deal_list_type='sms';
	}
	
	$top_menu = fetch_tpl($PARS_1, $clients_top_menu_tpl);
	 
 
	
	// Кол-во новых переданных сделок
	$new_avalible_deals_count = get_new_avalible_deals_count($user_id);
	$new_avalible_deals_count = $new_avalible_deals_count ? ' (+ '.$new_avalible_deals_count.')' : '';
	
	$PARS_1['{NEW_AVALIBLE_DEALS_COUNT}'] = $new_avalible_deals_count;
	
	$PARS_1['{WORKERS_DEALS_TOP_MENU}'] = $workers_deals_top_menu;
	
	$PARS_1['{WORKERS_DEALS_TOP_MENU}'] = $workers_deals_top_menu;
	
	$PARS_1['{DEALS_SEARCH_TOP_MENU}'] = $deals_search_top_menu;
	$PARS_1['{DEALS_SMS_TOP_MENU}'] = $deals_sms_top_menu;
	
	$PARS_1['{ACTIVE_1}'] = $active_menu_1;
	
	$PARS_1['{ACTIVE_2}'] = $active_menu_2; 
	
	$PARS_1['{ACTIVE_3}'] = $active_menu_3;
	
	$PARS_1['{ACTIVE_4}'] = $active_menu_4;

	$PARS_1['{ACTIVE_5}'] = $active_menu_5;

	
	$top_menu = fetch_tpl($PARS_1, $deals_top_menu_tpl);
	###
	
	if($_GET['wks'] && (!$_CURRENT_USER_WORKERS_ARR && !$_CURRENT_USER_DEPUTY_WORKERS_ARR))
	{
		header('Location: /deals');
		exit();
	}
	
	// Очистка массива удаленных сделок
	if($_SESSION['deal_deleted'])
	{
		$_SESSION['deal_deleted'] = '';
	}
	
	// Не выводим форму поиска при просмотре всех сделок сотрудников
	if($deal_list_type!='wks')
	{
		$deal_search_form = $deals_search_form_tpl;
	}
	
	// Сделки всех сотрудников пользователя
	if($deal_list_type=='wks')
	{  
		// Список сделок
		$deals_list = fill_all_user_workers_deals_list($current_user_id);
	}
	else
	{if($deal_list_type=='sms'){
if(!$current_user_obj->get_is_admin()){header('Location:deals');
die();}
if($_POST['deletesmsid']){
$sql = "DELETE FROM `".TASKS_SMS."` WHERE `id`=".(int)$_POST['deletesmsid'].";";

$site_db->query($sql);
$sql = "DELETE FROM `".TASKS_SMS_SENT."` WHERE `task`=".(int)$_POST['deletesmsid'].";";
$site_db->query($sql);
header('Location:deals?sms=1&page='.(int)$_GET['page']);
die();
}
if($_POST['export_lidov'] == 1){
            $row0_id = (int)$_POST['row0_id'];
            $group = (int)$_POST['group'];
            $deal_last_status = (int)$_POST['deal_last_status'];
            $date1 = mysql_escape_string($_POST['date1']);
            $date2 = mysql_escape_string($_POST['date2']);
            
            $sql = "SELECT sms FROM ".TASKS_SMS." WHERE id='$row0_id' LIMIT 1";
            $res = $site_db->query($sql);
            $name = $site_db->fetch_array($res);
            $name = '_'.$name['sms'];
            $name = substr($name, 0, 20);
            $name = iconv("CP1251", "UTF8//IGNORE", $name);
//            $name = '';

            $query = "";
            if($group != '')$query .= " AND group_id='".$group."'";
            if($deal_last_status != '')$query .= " AND deal_last_status='".$deal_last_status."'";
            if($date1 != '')$query .= " AND (deal_date_add >= '".$date1."'";
            if($date2 != '')$query .= " AND deal_date_add <= '".$date2." 23:59:59')";
            
            $sql = "SELECT deal_phone, deal_client_name FROM ".DEALS_TB." WHERE deal_id ".$query." AND deal_deleted = '0'";
            
            $myecho = json_encode($sql);
            $myecho2 = $row0_id;
            `echo $myecho2"_2: "  $myecho >>/var/www/tmp/qaz_hrm`;
            
            $foptmp = fopen("temp/csv/exp_lid/".$row0_id.$name.".vcf", "w");///temp/csv/exp_lid/156.csv "ab"

            $rows = $site_db->query($sql);
				
            while($clientTask = $site_db->fetch_array($rows)){//foreach ($clientTasks as $clientTask) {
                $phone = preg_replace("/^[8]/", '+7', $clientTask['deal_phone']);
                $phone = preg_replace("/[^0-9]/", '', $phone);
                if(strlen(strval($phone)) >= 10 && strlen(strval($phone)) <= 12){
                    $deal_client_name = iconv("CP1251", "UTF8//IGNORE", $clientTask['deal_client_name']);
                    $contact = 'BEGIN:VCARD
VERSION:3.0
FN:'.$deal_client_name.'
N:;;;;
TEL;TYPE=CELL:'.$phone.'
END:VCARD';
                               
                    fwrite($foptmp, $contact . PHP_EOL);
                }
            }

			fclose($foptmp);
			$file = "temp/csv/exp_lid/".$row0_id.$name.".vcf";
			if (file_exists($file)) {
			    header('Content-Description: File Transfer');
			    header('Content-Type: application/octet-stream');
			    header('Content-Disposition: attachment; filename="'.basename($file).'"');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . filesize($file));
			    readfile($file);
			    exit;
			}
        }

if($_POST['togglesmsid']){
$sql = "SELECT * FROM ".TASKS_SMS." WHERE `id` = ".(int)$_POST['togglesmsid']."";

$res0 = $site_db->fetch_array($site_db->query($sql));
if($res0['id']){
$sql = "UPDATE `".TASKS_SMS."` SET `status` = '0' WHERE `id`=".(int)$_POST['togglesmsid'].";";
if($res0['status']==0)$sql = "UPDATE `".TASKS_SMS."` SET `status` = '-1' WHERE `id`=".(int)$_POST['togglesmsid'].";";
$site_db->query($sql);
header('Location:deals?sms=1&page='.(int)$_GET['page']);
die();
}

}

if($_POST['sms']){
$sql = "INSERT INTO `".TASKS_SMS."` (`sms`, `data`, `user`) VALUES ('".stripslashes($_POST['sms'])."', '".serialize($_POST['segment'])."', '".(int)$current_user_obj->get_user_id()."');";

$site_db->query($sql);
header('Location:deals?sms=1');
die();
}
        $sms_page = file_get_contents('templates/deals/sms_page.tpl');

$_GET['page']=(int)$_GET['page'];
if($_GET['page']<1)$_GET['page']=1;
define('DEALS_PER_PAGE',2);
	$begin_pos = DEALS_PER_PAGE * ($_GET['page']-1);
	$limit = " LIMIT ".$begin_pos.",".DEALS_PER_PAGE;


		$sql = "SELECT * FROM ".TASKS_SMS."  ORDER BY `id` DESC $limit";

$res0 = $site_db->query($sql);

	while($row0=$site_db->fetch_array($res0))
	{$flag=1;
		// Заполнение элемента клиента
$status='';
if($row0['status']==1)$status='status_3';
if($row0['status']==-1)$status='status_1';
$data=unserialize($row0['data']);
$query='';$segment='';$pause='';
if($row0['status']==0)$pause='<form method=post><button type="submit" class="button" style="outline: none;border: 0;height: auto;line-height: 19px;float: none;"> <div class="right"></div><div class="left"></div><div class="btn_cont">Пауза</div></button><input type="hidden" name="togglesmsid" value="'.$row0['id'].'" class="btn_cont"></form>';
if($row0['status']==-1)$pause='<form method=post><button type="submit" class="button" style="outline: none;border: 0;height: auto;line-height: 19px;float: none;"> <div class="right"></div><div class="left"></div><div class="btn_cont">Старт</div></button><input type="hidden" name="togglesmsid" value="'.$row0['id'].'" class="btn_cont"></form>';
if($data['group']){$name=$site_db->fetch_array($site_db->query("SELECT `group_name` FROM ".DEALS_GROUPS_TB." WHERE `group_id`=".(int)$data['group']));$segment.=' Группа сделок: '.$name['group_name'].'.';$query.=' AND `group_id`="'.(int)$data['group'].'"';}
if($data['status']){$name=$site_db->fetch_array($site_db->query("SELECT `status_name` FROM ".DEALS_STATUSES_DATA_TB." WHERE `status_id`=".(int)$data['status']));$segment.=' Статус сделок: '.$name['status_name'].'.';$query.=' AND `deal_last_status`="'.(int)$data['status'].'"';}
if($data['date1'])$data['date1']=formate_to_norm_date(value_proc($data['date1']));
if($data['date2'])$data['date2']=formate_to_norm_date(value_proc($data['date2']));
if($data['date1'] && $data['date2'])
	{$segment.=" Дата: от ".$data['date1']." до ".$data['date2']."";
		$query.=" AND (deal_date_add >= '".$data['date1']."' AND deal_date_add <= '".$data['date2']." 23:59:59')";
	}
	
	else if($data['date1'] && !$data['date2'])
	{$segment.=" Дата: от ".$data['date1']."";
		$query.= " AND deal_date_add >= '".$data['date1']."'";
	}
	else if(!$data['date1'] && $data['date2'])
	{$segment.=" Дата: до ".$data['date2']."";
		$query.=" AND deal_date_add <= '".$data['date2']." 23:59:59'";
	} global $user_obj;
$user_obj->fill_user_data($row0['user']);

$col=$site_db->fetch_array($site_db->query("SELECT COUNT(*) FROM ".DEALS_TB." WHERE `deal_id` ".$query." AND `deal_deleted` = '0'"));

$col1=$site_db->fetch_array($site_db->query("SELECT COUNT(*) FROM ".DEALS_TB." WHERE `deal_id`<=".(int)$row0['next']." ".$query." AND `deal_deleted` = '0'"));

if(($row0['status']==1) & ($col1['COUNT(*)']<$col['COUNT(*)']))$pause='<form method=post><button type="submit" class="button" style="outline: none;border: 0;height: auto;line-height: 19px;float: none;"> <div class="right"></div><div class="left"></div><div class="btn_cont">Возобновить</div></button><input type="hidden" name="togglesmsid" value="'.$row0['id'].'" class="btn_cont"></form>';
		$sms_list .= '<tr class="task_it_row '.$status.'">
	<td class="task_name"> '.htmlspecialchars($row0['sms']).'</td>
<td><a href="/id'.(int)$row0['user'].'" class=\'user_link\'>'.$user_obj->get_user_name().' '.$user_obj->get_user_middlename().' '.$user_obj->get_user_surname().'</a></td>
    <td>'.$segment.'</td>
    <td style="white-space:nowrap">'.$col1['COUNT(*)'].'/'.$col['COUNT(*)'].'</td>
    <td>'.$pause.'</td>
<td><form method=post><button type="submit" class="button" style="outline: none;border: 0;height: auto;line-height: 19px;float: none;"> <div class="right"></div><div class="left"></div><div class="btn_cont">Удалить</div></button><input type="hidden" name="deletesmsid" value="'.$row0['id'].'" class="btn_cont"></form></td>
<td><form method=post><button type="submit" class="button" style="outline: none;border: 0;height: auto;line-height: 19px;float: none;"> <div class="right"></div><div class="left"></div><div class="btn_cont">Экспорт</div></button><input type="hidden" name="export_lidov" value="1" class="btn_cont"><input type="hidden" name="row0_id" value="'.$row0['id'].'" class="btn_cont"><input type="hidden" name="group" value="'.(int)$data['group'].'" class="btn_cont"><input type="hidden" name="deal_last_status" value="'.(int)$data['status'].'" class="btn_cont"><input type="hidden" name="date1" value="'.$data['date1'].'" class="btn_cont"><input type="hidden" name="date2" value="'.$data['date2'].'" class="btn_cont"><input type="hidden" name="query" value="" class="btn_cont"></form></td>
</tr>';
	}
if(!$flag)$sms_list="<tr><td colspan=10 align=center style=\"padding:15px;\">Ничего не найдено";
$pag='';
if($_GET['page']>1)$pag.="<a href=\"deals?sms=1&page=".($_GET['page']-1)."\">Назад</a>&nbsp;&nbsp;";
$next=$site_db->fetch_array($site_db->query("SELECT COUNT(*) FROM ".TASKS_SMS.""));
	$begin_pos1 = DEALS_PER_PAGE * ($_GET['page']);
if(($next["COUNT(*)"]-$begin_pos1)>0)$pag.="&nbsp;&nbsp;<a href=\"deals?sms=1&page=".($_GET['page']+1)."\">Вперёд</a>";
$sms_list.="<tr><td align=center colspan=10 style=\"padding:15px;\">".$pag;		
$sms_page=str_replace('{SMS_LIST}',$sms_list,$sms_page);
	
        }else{
		// Список сделок
		$deals_list = fill_deals_list($deal_list_type, 1,'', $user_id);
		
		// Воронка продаж
		$deal_sales_funnel = fill_sales_funnel($user_id);
		
		$PARS_1['{DEAL_SALES_FUNNEL}'] = $deal_sales_funnel;
		
		$deal_sales_funnel = fetch_tpl($PARS_1, $deal_sales_funnel_block_tpl);}
	}

	 
	// Выводим форму добавления клиента для своей страницы
	if($current_user_id==$user_id)
	{
		$deal_add_form = $deal_add_form_tpl;
	}
	
	// Не выполняем блок при просмотре всех клиентов сотрудника
	if($deal_list_type!='wks')
	{
		// Блок типов сделок
		$deals_types_block = fill_deals_types_block(0);
		
		// Список статусов сделки
		$deals_statuses_list = fill_deals_statuses_list(0, 'add');
	 
		// Кол-во сделок
		$deals_count = get_current_user_deals_count($deal_list_type);
		
		// Кол-во страниц
		$pages_count = ceil($deals_count/DEALS_PER_PAGE);
		 
		// Если страниц больше 1
		if($pages_count > 1)
		{
			$more_deals = $more_deals_btn_tpl;
		}
	}
	
	if(!$deals_list)
	{
		$deals_list = $no_deals_tpl;
	}
	 
	// Список групп сделок для селекта
	$deals_group_list = fill_deals_groups_list($_GET['group_id']);
	
	// Список статусов сделки
	$deals_status_list = fill_deals_statuses_list($_GET['status']);
	
	// 
	$deals_status_call = fill_deals_statuses_call($_GET['call']);
	
	// Блок 
	$deals_reminders_list = fill_deals_reminders_list();
	
	$secret_dwnl = $user_id;//generate_secret_dwnl();
		 
	$PARS['{DEAL_LIST_TYPE}'] = $deal_list_type;
	
	$PARS['{DATE_FROM}'] = $_GET['date_from'];
	
	$PARS['{DATE_TO}'] = $_GET['date_to'];
	
	$PARS['{CLEAR_DATE_BLOCK}'] = $clear_date_block;
	
	$PARS['{DEAL_SALES_FUNNEL_BLOCK}'] = $deal_sales_funnel;
	
	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{DEALS_LIST}'] = $deals_list;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{MORE_DEALS}'] = $more_deals;
	
	$PARS['{DEAL_ADD_FORM}'] = $deal_add_form;
	
	$PARS['{DEALS_TYPES_BLOCK}'] = $deals_types_block;
	
	$PARS['{DEALS_STATUSES_LIST}'] = $deals_statuses_list;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{DEALS_SEARCH_FORM}'] = $deal_search_form;
	
	$PARS['{DEALS_GROUPS_LIST}'] = $deals_group_list;
	
	$PARS['{DEALS_STATUS_LIST}'] = $deals_status_list;
	
	$PARS['{DEALS_STATUS_CALL}'] = $deals_status_call;
	
	$PARS['{DEALS_REMINDERS_LIST}'] = $deals_reminders_list;
	
	$PARS['{SECRET_DWNL}'] = $secret_dwnl;
	
        $PARS['{SMS_PAGE}'] = $sms_page;
	if($_GET['sms'])$main_tpl=fetch_tpl($PARS, $main_tpl);//форма дублируется
	
	return fetch_tpl($PARS, $main_tpl);
}

//генерация секрета для скачивания
function generate_secret_dwnl(){
    if(!is_dir(LOG_REC_PATH))
    {
        mkdir(LOG_REC_PATH, 0775, true);
    }
    $f_secret_dwnl = LOG_REC_PATH."/secret_dwnl.txt";
    if(file_exists($f_secret_dwnl)){
        $secret_dwnl = file_get_contents($f_secret_dwnl);
    }else{
        $secret_dwnl = strval(rand(1,9)).strval(rand(0,9)).strval(rand(0,9)).strval(rand(0,9)).strval(rand(0,9)).strval(rand(0,9));
        file_put_contents($f_secret_dwnl, $secret_dwnl);
    }
    return $secret_dwnl;
}

// Блок выжных сделок на сегодня
function fill_deals_reminders_list()
{
	global $site_db, $current_user_id;
	
	$deal_reminder_list_tpl = file_get_contents('templates/deals/deal_reminder_list.tpl');
	
	$deal_reminder_list_item_tpl = file_get_contents('templates/deals/deal_reminder_list_item.tpl');
	
	$now_mkdate = to_mktime(date('Y-m-d'));
		
	// Выбор важных сделок на сегодня
	$sql = "SELECT j.* FROM ".DEALS_REMINDERS_TB." i
			LEFT JOIN  ".DEALS_TB." j ON i.deal_id=j.deal_id
			WHERE i.user_id='$current_user_id' AND reminder_date = '$now_mkdate' AND j.deal_deleted=0";
			
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$PARS['{DEAL_ID}'] = $row['deal_id'];
		
		$PARS['{DEAL_NAME}'] = $row['deal_name'];
		
		$deals_list .= fetch_tpl($PARS, $deal_reminder_list_item_tpl);
	}
	
	if(!$deals_list)
	{
		return '';
	}
 
	
	$PARS['{DEALS_LIST}'] = $deals_list;
	
	return fetch_tpl($PARS, $deal_reminder_list_tpl);
}

// Список сделок 
function fill_deals_list($deal_list_type, $page=1, $search_word='', $user_id)
{
	global $site_db, $current_user_id;
			 
	// Страничность
	$begin_pos = DEALS_PER_PAGE * ($page-1);
	$limit = " LIMIT ".$begin_pos.",".DEALS_PER_PAGE;
	
	// Удаленные в этой сессии клиенты
	$deleted_deals_ids = implode(', ',$_SESSION['deal_deleted']);
	
	if($deleted_deals_ids)
	{
		$and_deleted_deals = " OR deal_id IN($deleted_deals_ids) ";
	}
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_user_deal_id'])
	{
		$and_deals_id = " AND deal_id <= '".$_SESSION['last_user_deal_id']."' ";
	}
	
	// При поиске по слову
	if($search_word)
	{
		// Часть запроса
		$search_word_s = get_part_query_search_words_for_deals($search_word);
	}
	
	// Часть запроса даты
	$date_part = get_deals_query_date_part();
	
	// Измененный статус  
	$deals_statuses_tb_left = get_deal_query_left_status_tb_part();
	
	if($deal_list_type=='av')
	{
		$sql = "SELECT DISTINCT(i.deal_id), i.* FROM ".DEALS_TB." i
				LEFT JOIN ".CLIENTS_TB." j ON i.deal_client_id=j.client_id
				LEFT JOIN ".DEALS_ACCESSES_TB." a ON i.deal_id = a.deal_id
				".$deals_statuses_tb_left."
				WHERE a.user_id='$current_user_id' AND (i.deal_deleted<>1 $and_deleted_deals) $date_part $search_word_s ORDER by i.deal_last_status_date DESC, i.deal_id DESC $limit";
				
	}
	else if($deal_list_type=='all')
	{
		$sql = "SELECT DISTINCT(i.deal_id), i.* FROM ".DEALS_TB." i
				LEFT JOIN ".CLIENTS_TB." j ON i.deal_client_id=j.client_id
				".$deals_statuses_tb_left."
				WHERE (i.deal_deleted<>1 $and_deleted_deals) $date_part $search_word_s ORDER by i.deal_last_status_date  DESC $limit";
				 
	}
	else
	{
		// Выбираем сделки пользователя
		$sql = "SELECT DISTINCT(i.deal_id), i.* FROM ".DEALS_TB." i
				LEFT JOIN ".CLIENTS_TB." j ON i.deal_client_id=j.client_id
				".$deals_statuses_tb_left."
				WHERE i.user_id='$current_user_id' AND (i.deal_deleted<>1 $and_deleted_deals) $date_part $and_deals_id $search_word_s ORDER by i.deal_last_status_date DESC, i.deal_id DESC $limit";
				 
				 
	}
	  
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		// Заполнение элемента клиента
		$deals_list .= fill_deals_list_item($row, 0, $search_word, '', $row['user_id']);
	}
	
	return 	$deals_list;
}

function get_deals_query_date_part()
{
	if($_SESSION['deal_date_from'])
	{
		$date_from = formate_to_norm_date(value_proc($_SESSION['deal_date_from']));
	}
	
	if($_SESSION['deal_date_to'])
	{
		$date_to = formate_to_norm_date(value_proc($_SESSION['deal_date_to']));
	}
	
	if($_SESSION['deal_group_id'])
	{
		$group_query_part = " AND group_id='".$_SESSION['deal_group_id']."' ";
	}
	
	if($_SESSION['deal_status'])
	{
		$deal_status_query_part = " AND deal_last_status='".$_SESSION['deal_status']."' ";
	}
	
	if($_SESSION['deal_call'])
	{
        if($_SESSION['deal_call']==1)$deal_status_b = -1;
        if($_SESSION['deal_call']==2)$deal_status_b = 0;
		$deal_call_query_part = " AND deal_status='$deal_status_b' ";
	}
	
	if($date_from && $date_to)
	{
		$date_query_part = " AND ((deal_date_add >= '$date_from' AND deal_date_add <= '$date_to 23:59:59') OR (s.status_date >= '$date_from' AND s.status_date <= '$date_to 23:59:59'))";
	}
	
	else if($date_from && !$date_to)
	{
		$date_query_part = " AND ((deal_date_add >= '$date_from') OR (s.status_date >= '$date_from'))";
	}
	else if(!$date_from && $date_to)
	{
		$date_query_part = " AND ((deal_date_add <= '$date_to 23:59:59') OR (s.status_date <= '$date_to 23:59:59'))";
	}
	
	return $date_query_part.$group_query_part.$deal_status_query_part.$deal_call_query_part;
	
}

function get_deal_query_left_status_tb_part()
{
	if($_SESSION['deal_date_from'] || $_SESSION['deal_date_to'])
	{
		$deals_statuses_tb_left = " LEFT JOIN ".DEALS_STATUSES_TB." s ON s.deal_id=i.deal_id ";
	}
	
	return $deals_statuses_tb_left;
}

// Возвращает кол-во сделок пользователя по его ID
function get_user_deals_count($user_id, $search_word='')
{
	global $site_db, $current_user_id;
	 
	// При поиске по слову
	if($search_word)
	{
		$search_word_s = get_part_query_search_words_for_deals($search_word);
	}
	// Часть запроса даты
	$date_part = get_deals_query_date_part();
	
	$deals_statuses_tb_left = get_deal_query_left_status_tb_part();
	 
	$sql = "SELECT COUNT(DISTINCT(i.deal_id)) as count 
				FROM ".DEALS_TB." i
				LEFT JOIN ".CLIENTS_TB." j ON i.deal_client_id=j.client_id
				".$deals_statuses_tb_left."
				WHERE i.user_id='$user_id' AND i.deal_deleted<>1 $search_word_s $date_part";
	
	$row = $site_db->query_firstrow($sql);
	 
	return $row['count'];
}

// Список сделок всех сотрудников 
function fill_all_user_workers_deals_list($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$workers_deals_block_tpl = file_get_contents('templates/deals/workers_deals_block.tpl');

	$workers_deals_more_btn_tpl = file_get_contents('templates/deals/workers_deals_more_btn.tpl');
	
	// Сливаем в единый массив временных и постоянных подчиненных
	$workers_users = get_current_user_users_arrs(array(0,1,0,0,1,1));
	
	$workers_users_ids = implode(',', $workers_users);
	
	foreach($workers_users as $user_id)
	{
		$deals_list = '';
		$more_deals = '';
		
		// Кол-во сделок пользователя
		$user_deals_count = get_user_deals_count($user_id);
		
		// Если у пользователя сделок нет - не выводим его в таблице
		if(!$user_deals_count)
		{
			continue;
		}
		
		// Кол-во страниц
		$pages_count = ceil($user_deals_count/WORKERS_DEALS_PER_PAGE);
		 
		// Если страниц больше 1
		if($pages_count > 1)
		{
			$PARS['{USER_ID}'] = $user_id;
			
			$more_deals = fetch_tpl($PARS, $workers_deals_more_btn_tpl);
		}
		
		 
		// Список сделок(массив) 
		$deals_list_arr = get_user_deals_list($user_id);
		// Список сделок
		$deals_list = $deals_list_arr['list'];
		// Дата последней измененной сделки пользователя
		$deal_last_status_date = $deals_list_arr['deal_last_status_date'];
		
		// График воронка продаж 
		$deal_sales_funnel = fill_sales_funnel($user_id);
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($user_id);
		 
		$PARS['{USER_ID}'] = $user_id;
			
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
		$PARS['{USER_USER_POSITION}'] = $user_obj->get_user_position();
	
		$PARS['{DEALS_LIST}'] = $deals_list;
		
		$PARS['{DEAL_SALES_FUNNEL}'] = $deal_sales_funnel;
		
		$PARS['{MORE_DEALS_BTN}'] = $more_deals;
		
		$workers_clients_list_arr[to_mktime($deal_last_status_date).'_'.$user_id] = fetch_tpl($PARS, $workers_deals_block_tpl);
	}
	
	// Сортируем в порядке новизны
	krsort($workers_clients_list_arr);
	
	$workers_clients_list = implode('', $workers_clients_list_arr);
		
	return 	$workers_clients_list;
}

// Список сделок пользователя
function get_user_deals_list($user_id, $page=1)
{
	global $site_db, $current_user_id, $user_obj;
	
	$page = $page ? $page : 1;
	
	// Часть запроса даты
	$date_part = get_deals_query_date_part();
	
	// Таблицы, которые присоединяем
	$deals_statuses_tb_left = get_deal_query_left_status_tb_part();
	
	// Страничность
	$begin_pos = WORKERS_DEALS_PER_PAGE * ($page-1);
	$limit = " LIMIT ".$begin_pos.",".WORKERS_DEALS_PER_PAGE;
	
	// Выбираем сделки пользователя
	$sql = "SELECT * FROM ".DEALS_TB." i
			".$deals_statuses_tb_left."
			WHERE i.user_id = '$user_id' AND i.deal_deleted<>1 $date_part ORDER by i.deal_last_status_date DESC, i.deal_id DESC $limit";
		
	$res = $site_db->query($sql);
			
	while($deal_data=$site_db->fetch_array($res, 1))
	{
		// Заполнение элемента сделки
		$deals_list .= fill_deals_list_item($deal_data, 0, $search_word, '', $user_id);
		$deal_last_status_date_arr[] = $deal_data['deal_last_status_date'];
	}
	
	$deal_last_status_date = max($deal_last_status_date_arr);
	 
	return 	array('list' => $deals_list, 'deal_last_status_date' => $deal_last_status_date);

}

// Заполняет элемент клиента
function fill_deals_list_item($deal_data, $edit_form=0, $search_word, $num, $user_id)
{
	global $site_db, $current_user_id, $user_obj;
	 
	$deal_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deals_list_item.tpl');
	
	$deal_edit_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deal_edit_form.tpl');
	
	$deal_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deals_edit_tools.tpl');
	
	$deals_downl_call_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deals_downl_call_btn.tpl');
    $deals_downl_call_btn_dis_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deals_downl_call_btn_dis.tpl');
	
	$deals_edit_tools_edit_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deals_edit_tools_edit_btn.tpl');
	
	$deals_edit_tools_delete_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deals_edit_tools_delete_btn.tpl');
	
	$deal_list_item_edit_private_options_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deals_list_item_edit_private_options.tpl');
	
	$reminder_add_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/reminder_add_block.tpl');
	
	$option_fcbk_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
	
	$access_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/access_btn.tpl');
	
	$save_deal_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/save_deal_btn.tpl');

	$deal_pay_sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deal_pay_sms.tpl');
	
	foreach($deal_data as $i => $j)
	{
		$deal_data[$i] = stripslashes($j);
	}
	
	// Проверка, является ли пользователь автором сделки
	if($current_user_id==$deal_data['user_id'])
	{
		// Автор сделки
		$is_deal_master = 1;
	}

	
	// Для создателя сделки, начальников или всем(если не отмечен чекбокс запретить редактирование всем, крмое вышестоящих сотрудников) выводим форму редактирования. Кнопка редактирования не выводится так же, если отмечен чекбокс НЕ ПОКАЗЫВАТЬ ДАННЫЕ
	if(is_deal_open_for_edit_for_user($current_user_id, $deal_data) || check_deal_for_available($current_user_id, $deal_data['deal_id'], $deal_data, 1))
	{
		$PARS_1['{DEAL_ID}'] =  $deal_data['deal_id'];
		
		$edit_tool_btns_arr[] = fetch_tpl($PARS_1, $deals_edit_tools_edit_btn_tpl);
	}	
	// Для автора сделки и его начальников выводим кнопку удалить
	if(is_deal_open_for_delete_for_user($current_user_id, $deal_data))
	{
		$PARS_1['{DEAL_ID}'] =  $deal_data['deal_id'];
		
		$edit_tool_btns_arr[] = fetch_tpl($PARS_1, $deals_edit_tools_delete_btn_tpl);
	}
	
	// Если есть кнопки редактирования
	if($edit_tool_btns_arr)
	{
		$edit_tool_btns = implode('', $edit_tool_btns_arr);
		
		$PARS_2['{TOOL_BTNS}'] =  $edit_tool_btns;
		
		$edit_tools = fetch_tpl($PARS_2, $deal_edit_tools_tpl);
	}
	
	//кнопка скачивания записи звонка
    //$deal_phone = $deal_data['deal_phone'];
    //$deal_phone = preg_replace("/[^0-9]/", '', $deal_phone);
    //if(strlen($deal_phone) == 11)$deal_phone = substr($deal_phone, 1);

    //$file = REC_CALL_PATH."/".$deal_phone.".m4a";//$_SERVER['DOCUMENT_ROOT']."/temp/rec_call/".$deal_phone.".m4a";
    //$rec_call_path = REC_CALL_PATH.'/'.$user_id;
    //$file = $rec_call_path."/".$deal_phone.".m4a";//$file = $_SERVER['DOCUMENT_ROOT']."/temp/rec_call/".$deal_phone.".m4a";

    $PARS_1['{USER_ID}'] = $user_id;

    if ($deal_data['deal_status']=='-1') {
        $deal_downl = fetch_tpl($PARS_1, $deals_downl_call_btn_tpl);
    }else{
        $deal_downl = fetch_tpl($PARS_1, $deals_downl_call_btn_dis_tpl);
    }
    
    //$PARS_1['{USER_ID}'] = $deal_data['user_id'] ? $deal_data['user_id'] : 1;
	
	// Подсвечиваем слова
	$deal_name = !$edit_form ? $deal_data['deal_name'] : $deal_data['deal_name'];
	
	$deal_other_info = $deal_data['deal_other_info']; 
	
	
	// Если клиент не постоянный
	if(!$deal_data['deal_client_id'])
	{
		$deal_client = $deal_data['deal_client_name'];
		// название клиента для поля фсбк
		$PARS_3['{CLASS}'] = 'selected';
		$PARS_3['{VALUE}'] = $deal_data['deal_client_name'];
		$PARS_3['{NAME}'] = $deal_data['deal_client_name'];
		$deal_client_option = fetch_tpl($PARS_3, $option_fcbk_tpl);
	}
	else
	{
		// название клиента
		$deal_client = get_client_name_by_id($deal_data['deal_client_id']);
		// название клиента для поля фсбк
		$PARS_3['{CLASS}'] = 'selected';
		$PARS_3['{VALUE}'] = $deal_data['deal_client_id'];
		$PARS_3['{NAME}'] = $deal_client;
		$deal_client_option = fetch_tpl($PARS_3, $option_fcbk_tpl);
	}
	
	$deal_client = !$edit_form ? $deal_client : $deal_client;
	
	// Выбираем последний статус сделки
	$deal_status_data_arr = get_last_deal_status_arr($deal_data['deal_id']);
	$deal_status_report = $deal_status_data_arr['status_report'];
	$deal_status_cut_report = strlen($deal_status_report)>130 ? substr($deal_status_report, 0, strpos($deal_status_report, ' ', 100)).'..' : $deal_status_report;
	// Если есть статус для сделки
	if($deal_status_data_arr['id'])
	{
		$deal_status_id = $deal_status_data_arr['status_id'];
	}
	
	// Класс для подсветки статуса сделки
	$deal_status_back_class = get_deal_status_back_class($deal_status_data_arr['status_id']);
	
	// для формы редактирования
	if($edit_form)
	{
		// Блок типов сделок
		$deals_types_block = fill_deals_types_block($deal_data['deal_type']);
		
		// Список статусов сделки
		$deals_statuses_list = fill_deals_statuses_list($deal_status_data_arr['status_id'], 'edit');
		
		// Блок истории изменения статусов
		$deal_history_block = fill_deal_history_block($deal_data['deal_id']);
		
	}
	else
	{
		// Статус сделки для вывода в списках
		$deal_status = $deal_status_data_arr['status_id'] ?  get_deal_status_by_status_id($deal_status_data_arr['status_id']) : '';
	
		// Тип сделки 
		$deal_type = get_deal_type_by_type_id($deal_data['deal_type']);
	}	
	// Сумма сделки
	$deal_price = $deal_data['deal_price'];
	
	$deal_contact_person = $deal_data['deal_contact_person'];
	
	$deal_email = $deal_data['deal_email'];
	$deal_address = $deal_data['deal_address'];
	$deal_phone = $deal_data['deal_phone'];
	
	// Не показывать данные сделки, всем, кроме вышестоящих сотрудников
	if(!$is_deal_master && $deal_data['deal_private_edit'] && !check_user_access_to_user_content($deal_data['user_id'], array(0,1,0,0,1,1)) && !$edit_form)
	{
		$deal_client = $deal_client == '' ? '' : '**********';
		$deal_type = $deal_type == '' ? '' : '**********';
		$deal_price = $deal_price == '' ? '' : '**********';
		$deal_contact_person = $deal_contact_person == '' ? '' : '**********';
		$deal_email = $deal_email == '' ? '' : '**********';
		$deal_address = $deal_address == '' ? '' : '**********';
		$deal_phone = $deal_phone == '' ? '' : '**********';
	}
	else
	{
		if($edit_form)
		{
			// Список файлов для отчета
			$files_list = get_attached_files_to_content($deal_data['deal_id'], 5, 2);
		}
	}
	
	$deal_private_edit_checked = $deal_data['deal_private_edit'] ? 'checked' : '';
	//$deal_private_show_checked = $deal_data['deal_private_show'] ? 'checked' : '';
	
	// не выводим пустые поля
	$deal_name_display = $deal_name == '' ? 'display:none' : '';
	$deal_client_display = $deal_client == '' ? 'display:none' : '';
	$deal_type_display = $deal_type == '' ? 'display:none' : '';
	$deal_status_display = $deal_status == '' ? 'display:none' : '';
	$deal_price_display = $deal_price == '' ? 'display:none' : '';
	$deal_contact_person_display = $deal_contact_person == '' ? 'display:none' : '';

	// Дата сделки
	$deal_date = formate_date($deal_data['deal_date_add'], 1);

	
	// Приватные опции при редактировании
	//if( $is_deal_master || check_user_access_to_user_content($deal_data['user_id'], array(0,1,0,0,1)))
	
	if(is_deal_open_for_edit_for_user($current_user_id, $deal_data))
	{
$sql = "SELECT * FROM tasks_integration WHERE `type`='kassa'";

    $keyRow = $site_db->query_firstrow($sql);

    if($keyRow['id']) {

        $kassa = unserialize($keyRow['data']);

    }
if($kassa['active']){


if($_POST['pay_sms']>0 & $_POST['pay_sms_text']!=''){$_POST['pay_sms']=(float)$_POST['pay_sms'];
//$sql = "SELECT * FROM ".TASKS_SMS." WHERE `id` = ".(int)$_POST['togglesmsid']."";

//$res0 = $site_db->fetch_array($site_db->query($sql));
if($_POST['pay_sms']>0){
$phone=$deal_data['deal_phone'];
$phone = preg_replace("/^[8]/", '+7', $phone);
$phone = preg_replace("/[^0-9]/", '', $phone);
$phone=substr($phone,0,11);//echo($phone.'<hr>');
$sms=$_POST['pay_sms_text'];
if(strlen($phone)==11){
$sql = "SELECT * FROM ".DEALS_STATUSES_TB." WHERE deal_id='".(int)$deal_data['deal_id']."' ORDER by id DESC LIMIT 1";
$deal_status_data = $site_db->fetch_array($site_db->query($sql));
$deal_status_data['status_id']=7;
global $freekassa;
//die($freekassa[1].$freekassa[0].$freekassa[2].((float)$_POST['pay_sms']+(int)$deal_data['deal_id']));
$sms=str_replace('{PAY}',(isset($_SERVER['HTTPS']) ? "https://" : "http://") .str_replace('www.', '', $_SERVER['HTTP_HOST']).'/pay.php?id='.(int)$deal_data['deal_id'].'&s='.(float)$_POST['pay_sms']/*.'&t='.substr(md5($freekassa[1].$freekassa[0].$freekassa[2].((float)$_POST['pay_sms']+(int)$deal_data['deal_id'])),0,5)*/,$sms);
$sms=str_replace('{SUM}',(float)$_POST['pay_sms'],$sms);

$sql = "INSERT INTO ".DEALS_STATUSES_TB." (`deal_id`, `user_id`, `status_id`, `status_report`, `status_date`) VALUES ('".(int)$deal_data['deal_id']."', '".(int)$current_user_id."', '".(int)$deal_status_data['status_id']."', '".stripslashes($deal_status_data['status_report'])."\r\n-------\r\nSMS со ссылкой на оплату ".(float)$_POST['pay_sms']." руб.:\r\n".stripslashes($sms)."', now());";

$site_db->query($sql);
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
send_sms_msg($phone,$sms);
}
//$sql = "UPDATE `".TASKS_SMS."` SET `status` = '0' WHERE `id`=".(int)$_POST['togglesmsid'].";";
//if($res0['status']==0)$sql = "UPDATE `".TASKS_SMS."` SET `status` = '-1' WHERE `id`=".(int)$_POST['togglesmsid'].";";
//$site_db->query($sql);
header('Location:/deals/edit/'.(int)$deal_data['deal_id']);
die();
}

}}else{$deal_pay_sms_tpl='';}


		//$PARS_1['{DEAL_PRIVATE_SHOW_CHECKED}'] = $deal_private_show_checked;
		// для автора сделки выводим опции приватности редактирования
		if($deal_data['user_id']==$current_user_id)
		{
			$PARS_1['{DEAL_PRIVATE_EDIT_CHECKED}'] = $deal_private_edit_checked;
			$edit_private_options = fetch_tpl($PARS_1, $deal_list_item_edit_private_options_tpl);
		}
		
		$PARS_1['{PRIVATE_OPTIONS}'] = $edit_private_options;
		$save_deal_btn = fetch_tpl($PARS_1, $save_deal_btn_tpl);
		$deal_pay_sms = fetch_tpl($PARS_1, $deal_pay_sms_tpl);
	}
	
	$last_date_edit = datetime($deal_data['deal_last_status_date'], '%d.%m.%y в %H:%i:%s');
	
	if($deal_data['group_id'])
	{
		$deal_group = get_selected_easycomplete($deal_data['group_id'], get_deal_group_name_by_group_id($deal_data['group_id']));
		 
	}
	
	// блок передачи доступа к клиенту
	//$deal_access_block = fill_deal_access_block($deal_data);
	
	// выводим кнопку передачи сделки, у кого есть доступ к ней 
	if($current_user_id == $deal_data['user_id'] || is_deal_open_for_edit_for_user($current_user_id, $deal_data) || check_deal_for_available($current_user_id, $deal_data['deal_id'], $deal_data))
	{
		$PARS['{DEAL_ID}'] = $deal_data['deal_id'];
		$access_btn = fetch_tpl($PARS, $access_btn_tpl);
	}
	
	$PARS['{ACCESS_BTN}'] = $access_btn;
	$PARS['{SAVE_DEAL_BTN}'] = $save_deal_btn;
	$PARS['{PAY_FORM}'] = $deal_pay_sms;
	
	$PARS['{DEAL_ID}'] = $deal_data['deal_id'];
	$PARS['{DEAL_NAME}'] = $deal_name;
	$PARS['{DEAL_CLIENT_OPTION}'] = $deal_client_option;
	$PARS['{DEAL_CLIENT}'] = $deal_client;
	$PARS['{DEAL_TYPE}'] = $deal_type;
	$PARS['{DEAL_PRICE}'] = $deal_price;
	$PARS['{DEAL_OTHER_INFO}'] = $deal_other_info;
	$PARS['{DEAL_STATUS}'] = $deal_status;
	$PARS['{DEAL_STATUS_REPORT}'] = $deal_status_report;
	$PARS['{DEAL_STATUS_CUT_REPORT}'] = $deal_status_cut_report;
	$PARS['{DEAL_DATE}'] = $deal_date;
	$PARS['{DEAL_CONTACT_PERSON}'] = $deal_contact_person;
	$PARS['{DEAL_STATUS_BACK_CLASS}'] = $deal_status_back_class;
	$PARS['{LAST_DATE_EDIT}'] = $last_date_edit;
	$PARS['{DEAL_STATUS_CLASS}'] = $deal_status_class;
	$PARS['{DEAL_GROUP_EASYSELECTED}'] = $deal_group;
	
	$PARS['{DEAL_GROUP_EASYSELECTED}'] = $deal_group;
	
	 
	
	### Блок трекингов 
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_post_tracking.php';
	// Блок трекинга для сделки
	$tracking_block = fill_post_tracking_block_in_linked_content(array('deal_id'=>$deal_data['deal_id']));
	$PARS['{TRACKING_BLOCK}'] = $tracking_block;	
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($deal_data['user_id']);
	
	// Выводим график статусов при редактировании сделки
	if($edit_form)
	{
		// График статусов
		$deal_schedule = fill_deal_schedule($deal_data['deal_id']);
	}
	### \  Блок трекингов 
	
	
	### Блок активных задач пользователя, привязанных к сделке
	//include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks.php';
	// Блок трекинга для сделки
	//$tracking_block = fill_tasks_in_linked_content(array('deal_id'=>$deal_data['deal_id']));
	//$PARS['{TASKS_BLOCK}'] = $tracking_block;
	### \
	$PARS['{TASKS_BLOCK}'] = '';
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_calendar_of_events.php'; 
	// Список часов
	$time_hour_list = get_event_hour_list();
	// Список минут
	$time_minutes_list = get_event_minutes_list();
	
	$PARS_2['{TIME_HOURS_LIST}'] = $time_hour_list;
	
	$PARS_2['{TIME_MINUTES_LIST}'] = $time_minutes_list;
	
	$PARS_2['{DEAL_ID}'] =  $deal_data['deal_id'];
	$reminder_add_block =  fetch_tpl($PARS_2, $reminder_add_block_tpl);
	
	// Блок о напоминании важности сделки
	$deal_reminder_block = fill_deal_reminder_block($deal_data['deal_id']);
	
	$PARS['{FILES_LIST}'] = $files_list;
	
	$PARS['{DEAL_REMINDER_BLOCK}'] = $deal_reminder_block;
	 
	$PARS['{DEAL_NAME_DISPLAY}'] = $deal_name_display;
	$PARS['{DEAL_CLIENT_DISPLAY}'] = $deal_client_display;
	$PARS['{DEAL_TYPE_DISPLAY}'] = $deal_type_display;
	$PARS['{DEAL_STATUS_DISPLAY}'] = $deal_status_display;
	$PARS['{DEAL_PRICE_DISPLAY}'] = $deal_price_display;
	$PARS['{DEAL_CLIENT_CONTACT_PERSON_DISPLAY}'] = $deal_contact_person_display;

	
	
	$PARS['{DEAL_HISTORY_BLOCK}'] = $deal_history_block;
	
	$PARS['{ACCESS_BLOCK}'] = $deal_access_block;
	
	$PARS['{REMINDER_ADD_BLOCK}'] = $reminder_add_block;
	
	$PARS['{USER_ID}'] = $deal_data['user_id'] ? $deal_data['user_id'] : 1;
	
	$PARS['{DEAL_DOWNL}'] = $deal_data['user_id'];
		
	$PARS['{USER_NAME}'] = $user_obj->get_user_name();
	
	$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
		
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
		
	$PARS['{USER_USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$PARS['{DEAL_DOWNL_BTN}'] = $deal_downl;
	
	$PARS['{DEALS_TYPES_BLOCK}'] = $deals_types_block;
	
	$PARS['{DEALS_STATUSES_LIST}'] = $deals_statuses_list;
	
	$PARS['{DEAL_SHELDULE}']  = $deal_schedule;
	
	$PARS['{DEAL_EMAIL}']  = $deal_email;
	$PARS['{DEAL_ADDRESS}']  = $deal_address;
	$PARS['{DEAL_PHONE}']  = $deal_phone;
	
	//$PARS['{SERIES}'] = $schedule_series;
	
	//$PARS['{SERIES_WITH_COMMENT}'] = $series_with_comment;
	
	// Форма для редактирования
	if($edit_form)
	{ 
		return fetch_tpl($PARS, $deal_edit_form_tpl);
	}
	else
	{
		return fetch_tpl($PARS, $deal_list_item_tpl);
	}
}

// Блок напоминания важности сделки
function fill_deal_reminder_block($deal_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$now_mkdate = to_mktime(date('Y-m-d'));
	
	$deal_reminder_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deal_reminder_block.tpl');
	
	// Выбор напоминания дял сделки
	$sql = "SELECT * FROM ".DEALS_REMINDERS_TB." WHERE deal_id='$deal_id' AND user_id='$current_user_id' AND reminder_date >= '$now_mkdate' ORDER by reminder_id DESC LIMIT 1";
	 
	$row = $site_db->query_firstrow($sql);
	
	if(!$row['reminder_id'])
	{
		return '';
	}
	
	$PARS['{REMINDER_ID}'] = $row['reminder_id'];
	
	$PARS['{DATE}'] = date('d.m.Y в H:i', $row['reminder_date']);
	
	return fetch_tpl($PARS, $deal_reminder_block_tpl);
}

// Доступ для клиентов
function fill_deal_access_block($deal_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$deal_id = $deal_data['deal_id'];
	
	$users_access_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/users_access_block.tpl');
	$users_access_user_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/users_access_user_item.tpl');
	
	$option_fcbk_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
	
	$user_access_select_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/user_access_select.tpl');
	
	if($current_user_id != $deal_data['user_id'] && !is_deal_open_for_edit_for_user($current_user_id, $deal_data) && !check_deal_for_available($current_user_id, $deal_id, $deal_data))
	{  
		return '';
	}
	
	// выбор всех пользоватлей, кому передали документ
	$sql = "SELECT * FROM tasks_deals_users_access WHERE deal_id='$deal_id' AND user_id!='$current_user_id' ORDER by id ";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$user_obj->fill_user_data($row['user_id']); 
	
		$user_name = $user_obj->get_user_name();
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_name = $user_obj->get_user_surname().' '.$user_name[0].'. '.$user_middlename[0].', '.$user_obj->get_user_position(); 
		
		$PARS['{ACCESS_ID}'] = $row['id'];
		$PARS['{DEAL_ID}'] = $row['deal_id'];
		$PARS['{CLASS}'] = 'selected';
		$PARS['{VALUE}'] = $row['user_id'];
		$PARS['{NAME}'] = $user_name;
		$users_access_list .= fetch_tpl($PARS, $user_access_select_tpl);
		
	}
	
	 
/*	// Документ можно передать и начальнику и подчиненному
	$users_for_access_arr = get_current_user_users_arrs(array(1,1,1,1,1), 1);
 
	foreach($users_for_access_arr as $user_data)
	{ 
	 	if($client_data['user_id']==$user_data['user_id'])
		{
			continue;
		}
		
		$access_active = '';
		
		// Проверяем доступность документа начальнику
		$sql = "SELECT id FROM ".DEALS_ACCESSES_TB." WHERE user_id='".$user_data['user_id']."' AND deal_id='$deal_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$access_active = 'access_active';
		}
		
		$user_obj->fill_user_data($user_data['user_id']);
		
		$user_name = $user_obj->get_user_name();
		
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_position = $user_obj->get_user_position();

		$PARS1['{DEAL_ID}'] = $deal_id;
		
		$PARS1['{ACCESS_ACTIVE}'] = $access_active;

		$PARS1['{USER_ID}'] = $user_data['user_id'];
		
		$PARS1['{SURNAME}'] = $user_surname;
		
		$PARS1['{NAME}'] = $user_name;
				
		$PARS1['{MIDDLENAME}'] = $user_middlename;
				
		$PARS1['{USER_POSITION}'] = $user_position;
		  
		$users_access_list .= fetch_tpl($PARS1, $users_access_user_item_tpl);
	}*/
	
	if(!$users_access_list)
	{
		//return '';
	}

	
	$PARS['{DEAL_ID}'] = $deal_id;
	
	$PARS['{USERS_LIST}'] = $users_access_list;
	
	return  fetch_tpl($PARS, $users_access_block_tpl);
}

// Проверяет, доступна ли сделка пользователю
function check_deal_for_available($user_id, $deal_id, $deal_data, $for_view)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	if($deal_data['user_id']==$current_user_id)
	{
		return true;
	}
	else
	{
		$sql = "SELECT id FROM ".DEALS_ACCESSES_TB." WHERE deal_id='$deal_id' AND user_id='$user_id'";
		 
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			return true;
		}
	}
	
	//  доступ для просмотра
	if($for_view && (($current_user_obj->get_user_is_dept_head() && !$deal_data['deal_private_edit']) || check_user_access_to_user_content($deal_data['user_id'], array(0,1,0,0,1,1))))
	{
		return true;
	}
	
	return false;
}

// Проверка, может ли пользователь, который просматривает - удалить клиента приватности для вывода кнопки удаления
function is_deal_open_for_delete_for_user($user_id, $deal_data)
{
	global $current_user_id;
	
	// Для автора сделки и его начальников есть возможность удалить сделку
	if($user_id == $deal_data['user_id'] || check_user_access_to_user_content($deal_data['user_id'], array(0,1,0,0,1,1)))
	{
		return true;
	}
	else
	{
		return false;
	}
}

// Проверка, может ли пользователь, который просматривает 
function is_deal_open_for_edit_for_user($user_id, $deal_data)
{
	global $site_db, $current_user_id;
	
	if($user_id == $deal_data['user_id'])
	{
		return true;
	}
	else if($deal_data['deal_private_edit'] && check_user_access_to_user_content($deal_data['user_id'], array(0,1,0,0,1,1)))
	{
		return true;
	}
	/*else if($deal_data['deal_private_show'] && check_user_access_to_user_content($deal_data['user_id'], array(0,1,0,0,1,1)))
	{
		return true;
	}*/
	else if(check_user_access_to_user_content($deal_data['user_id'], array(0,1,0,0,1,1)))
	{
		return true;
	}
	else if(check_deal_for_available($user_id, $deal_data['deal_id'], $deal_data) && !$deal_data['deal_private_edit'])
	{
		return true;
	}
	 
	
	// Для создателя сделки, начальников или всем(если не отмечен чекбокс запретить редактирование всем, крмое вышестоящих сотрудников) выводим форму редактирования. Кнопка редактирования не выводится так же, если отмечен чекбокс НЕ ПОКАЗЫВАТЬ ДАННЫЕ
	/*if($user_id == $deal_data['user_id'] || 
	(($deal_data['deal_private_edit'] && $deal_data['deal_private_show'] && check_user_access_to_user_content($deal_data['user_id'], array(0,1,0,0,1,1))) 
	|| check_user_access_to_user_content($deal_data['user_id'], array(0,1,0,0,1,1)))
	{
		return true;
	}*/
	
	// Проверка, была ли активная задача пользователя привязана к сделке
	/*$sql = "SELECT * FROM ".TASKS_DEALS_LINKS_TB." i
			LEFT JOIN ".TASKS_TB." j ON j.task_id=i.task_id
			WHERE i.deal_id='".$deal_data['deal_id']."' AND j.task_to_user='$user_id' AND j.task_deleted=0";
	 
	$row = $site_db->query_firstrow($sql);
	
	if($row['link_id'])
	{
		return true;
	}*/
	
	return false;
	
}

// Блок статусов сделки
function fill_deal_history_block($deal_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$deals_status_history_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deals_status_history.tpl');
	
	$deals_status_history_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deals_status_history_item.tpl');
	
	// Выбираем последний статус сделки
	$deal_status_data_arr = get_last_deal_status_arr($deal_id);
	
	if($deal_status_data_arr['id'])
	{
		//$and_not_status = " AND id!='".$deal_status_data_arr['id']."'";
	}
	
	// Выбор статусов
	$sql = "SELECT * FROM ".DEALS_STATUSES_TB." WHERE deal_id='$deal_id' $and_not_status ORDER by id DESC ";
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		$deal_status_date = datetime($row['status_date'], '%d.%m.%Y');
		
		$zebra_class = $num%2 ? 'zebra' : '';
		
		$status_name = $row['status_id']!=0 ? get_deal_status_by_status_id($row['status_id']) : 'Статуса нет';
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['user_id']);
		
		$PARS_1['{USER_ID}'] = $row['user_id'];
			
		$PARS_1['{USER_NAME}'] = $user_obj->get_user_name();
		
		$PARS_1['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
		$PARS_1['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
		$PARS_1['{USER_USER_POSITION}'] = $user_obj->get_user_position();
	
		$PARS_1['{STATUS_NAME}'] = $status_name;
		
		$PARS_1['{STATUS_REPORT}'] = nl2br($row['status_report']);
		
		$PARS_1['{DEAL_STATUS_DATE}'] = $deal_status_date;
		
		$PARS_1['{ZEBRA_BACK}'] = $zebra_class;
		
		$PARS_1['{CALL_ID}'] = $deal_id;
		
		$statuses_list .= fetch_tpl($PARS_1, $deals_status_history_item_tpl); 
		
		$num++;
	}
	
	if(!$statuses_list)
	{
		return '';
	}
	$PARS['{DEAL_STATUSES_LIST}'] = $statuses_list;
	
	return fetch_tpl($PARS, $deals_status_history_tpl); 
}

// Возвращает массив последнего статуса сделки
function get_last_deal_status_arr($deal_id)
{
	global $site_db, $current_user_id;
	
	// Статус сделки
	// Выбираем последний статус сделки
	$sql = "SELECT * FROM ".DEALS_STATUSES_TB." WHERE deal_id='$deal_id' ORDER by id DESC LIMIT 1";
	
	$deal_status_data = $site_db->query_firstrow($sql);
	
	return $deal_status_data;
}

// Страница клиентов сотрудника
function fill_deal_edit($deal_id)
{
	global $site_db, $current_user_id;
	
	$main_tpl = file_get_contents('templates/deals/deal_edit.tpl');
	
	// Выбораем данные сделки
	$sql = "SELECT * FROM ".DEALS_TB." WHERE deal_id='$deal_id'";
	
	$deal_data = $site_db->query_firstrow($sql);
	
	if(!$deal_data['deal_id'] || $deal_data['deal_deleted']==1)
	{ 
		header('Location: /deals');
	}
	
	if(!is_deal_open_for_edit_for_user($current_user_id, $deal_data) && !check_deal_for_available($current_user_id, $deal_data['deal_id'], $deal_data, 1))
	{  
		header('Location: /deals');
	}
	
	// Заполнение элемента клиента
	$deal_edit_form .= fill_deals_list_item($deal_data, 1, '', '');
	
	$PARS['{DEAL_EDIT_FORM}'] = $deal_edit_form;

	return fetch_tpl($PARS, $main_tpl);
}

// Возврашает название статуса сделки по status_id
function get_deal_status_by_status_id($status_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT status_name FROM ".DEALS_STATUSES_DATA_TB." WHERE status_id='$status_id'";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['status_name'];
}

// Возврашает название типа сделки по type_id
function get_deal_type_by_type_id($type_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT type_name FROM ".DEALS_TYPES_TB." WHERE type_id='$type_id'";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['type_name'];
}
// Возвращает кол-во сделок пользователя
function get_current_user_deals_count($deal_list_type, $search_word='')
{
	global $site_db, $current_user_id;
	 
	// При поиске по слову
	if($search_word)
	{
		$search_word_s = get_part_query_search_words_for_deals($search_word);
	}
	// Часть запроса даты
	$date_part = get_deals_query_date_part();
	
	$deals_statuses_tb_left = get_deal_query_left_status_tb_part();
	 
	// Переданные клиенты
	if($deal_list_type=='av')
	{
		$sql = "SELECT COUNT(*) as count FROM ".DEALS_TB." i
				LEFT JOIN ".CLIENTS_TB." j ON i.deal_client_id=j.client_id
				LEFT JOIN ".DEALS_ACCESSES_TB." a ON i.deal_id = a.deal_id
				".$deals_statuses_tb_left."
				WHERE a.user_id='$current_user_id' AND i.deal_deleted<>1 $search_word_s $date_part"; 
				
	}
	else if($deal_list_type=='all')
	{
		$sql = "SELECT COUNT(DISTINCT(i.deal_id)) as count 
				FROM ".DEALS_TB." i
				LEFT JOIN ".CLIENTS_TB." j ON i.deal_client_id=j.client_id
				".$deals_statuses_tb_left."
				WHERE i.deal_deleted<>1 $search_word_s $date_part";
	}
	else
	{
		$sql = "SELECT COUNT(DISTINCT(i.deal_id)) as count 
				FROM ".DEALS_TB." i
				LEFT JOIN ".CLIENTS_TB." j ON i.deal_client_id=j.client_id
				".$deals_statuses_tb_left."
				WHERE i.user_id='$current_user_id' AND i.deal_deleted<>1 $search_word_s $date_part";
	}
	$row = $site_db->query_firstrow($sql);
	 
	return $row['count'];
}

// Возвращает кол-во сделок контактов
function get_all_deals_count($search_word)
{
	global $site_db, $current_user_id;
	
	// При поиске по слову
	if($search_word)
	{
		$search_word_s = get_part_query_search_words_for_deals($search_word);
	}
	
	// Часть запроса даты
	$date_part = get_deals_query_date_part();
	
	$deals_statuses_tb_left = get_deal_query_left_status_tb_part();
	
	$sql = "SELECT COUNT(DISTINCT(i.deal_id)) as count 
			FROM ".DEALS_TB." i
			LEFT JOIN ".CLIENTS_TB." j ON i.deal_client_id=j.client_id
			".$deals_statuses_tb_left."
			WHERE i.deal_deleted<>1 $search_word_s $date_part";

	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}


// Формирование части запроса при поиске по словам
function get_part_query_search_words_for_deals($search_word)
{
	$search_word_s = " AND ( i.deal_name LIKE '%$search_word%' OR i.deal_phone LIKE '%$search_word%' OR ( i.deal_client_id=0 AND i.deal_client_name LIKE '%$search_word%' ) 
						OR (i.deal_client_id > 0 AND j.client_name LIKE '%$search_word%') ) ";
	
	return $search_word_s;
}

// Возвращает список статусов сделки
function fill_deals_statuses_list($status_id, $mode)
{
	global $site_db, $current_user_id;
	
	// Список статус при добавлении сделки
	if($mode=='add')
	{
		$sql = "SELECT *, if(status_id=11,1,0) as status_order FROM ".DEALS_STATUSES_DATA_TB." WHERE status_for_add=1 ORDER by status_order ASC,  status_name ASC";
	}
	// Список статус при редактировании сделки
	else if($mode=='edit')
	{
		$sql = "SELECT *, if(status_id=11,1,0) as status_order FROM ".DEALS_STATUSES_DATA_TB." WHERE status_for_edit=1 ORDER by status_order ASC,  status_name ASC";
	}
	else
	{
		$sql = "SELECT *, if(status_id=11,1,0) as status_order FROM ".DEALS_STATUSES_DATA_TB." ORDER by status_order ASC,  status_name ASC";
	}
	
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{ 
		if($mode=='edit' && $status_id && $row['status_id']==0)
		{
			continue;
		}
		
		$selected = '';
		
		if($status_id)
		{
			$selected = $row['status_id'] == $status_id ? 'selected' : '';
		}
		else
		{
			$selected = $row['status_id'] == 0 ? 'selected' : '';
		}
		
		$PARS2['{NAME}'] = $row['status_name'];
				
		$PARS2['{VALUE}'] = $row['status_id'];
				
		$PARS2['{SELECTED}'] = $selected;
				
		$statuses_list .= fetch_tpl($PARS2, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl'));
	}
	
	 
	return $statuses_list;
	
}

// Возвращает список статусов звонка
function fill_deals_statuses_call($call_id)
{
    $statuses_call  = array('1'=>'Есть звонок', '2'=>'Нет звонка');
    for($i=1; $i<=2; $i++)
	{ 
		$selected = '';
		
		if($call_id)
		{
			$selected = $i == $call_id ? 'selected' : '';
		}
		else
		{
			$selected = $i == 0 ? 'selected' : '';
		}
		
		$PARS2['{NAME}'] = $statuses_call[$i];
				
		$PARS2['{VALUE}'] = $i;
				
		$PARS2['{SELECTED}'] = $selected;
				
		$call_list .= fetch_tpl($PARS2, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl'));
	}
	
	 
	return $call_list;
    
}

// Возвращает блок с типами сделок
function fill_deals_types_block($type_id)
{  
	global $site_db, $current_user_id;
	
	$radio_tpl = file_get_contents('templates/tags/radio.tpl');
	
	$sql = "SELECT * FROM ".DEALS_TYPES_TB."";
	
	$res = $site_db->query($sql);
	$num=1;		
	while($row=$site_db->fetch_array($res))
	{
		$radio_name = 'deal_type';
		
		$checked = '';
		// Если не передано значение типа сделки
		if(!$type_id)
		{
			$checked = $row['type_checked'] ? 'checked' : '';
		}
		else
		{
			$checked = $row['type_id'] == $type_id? 'checked' : '';
		}
		
		$PARS['{RADIO_NAME}'] = $radio_name;
		
		$PARS['{VALUE}'] = $row['type_id'];
		
		$PARS['{NAME}'] = $row['type_name'];
		
		$PARS['{CHECKED}'] = $checked;
		
		$PARS['{RADIO_ID}'] = $radio_name.$num;
		
		// Заполнение элемента клиента
		$deals_types_list .= fetch_tpl($PARS, $radio_tpl);
		
		$num++;
	}
	
	return $deals_types_list;
}

// Возвращает класс css для выделения статуса
function get_deal_status_back_class($status_id)
{
	switch($status_id)
	{
		// оранжевая
		case '3':
			$deal_status_back_class = 'deal_status_back_6';
		break;
		// красная
		case '4':
		case '10':
			$deal_status_back_class = 'deal_status_back_1';
		break;
		// синяя
		case '9';
			$deal_status_back_class = 'deal_status_back_2';
		break;
		// зеленая
		case '6';
		case '7';
		case '8';
			$deal_status_back_class = 'deal_status_back_3';
		break;
		// салатовая
		case '13':
			$deal_status_back_class = 'deal_status_back_5';
		break;
		// желтая
		default:
			$deal_status_back_class = 'deal_status_back_4';
		break;
	}
	
	return $deal_status_back_class;
}

// График Воронка продаж
function fill_sales_funnel($user_id)
{
	/*Черных завершенных - 100,000 руб 000
		Зеленых - 200,000 руб 36d00d
		Желтых - 300,000 руб f0f21e
		Красных - 400,000 руб  e21414*/
	global $site_db;
	
	$deal_sales_funnel_tpl = file_get_contents('templates/deals/deal_sales_funnel.tpl');
	
	// Сделки пользователя
	if($user_id)
	{
		$and_user = " AND i.user_id='$user_id'";
	}
	
	// Часть запроса даты
	$date_part = get_deals_query_date_part();
	
	$left_status_tb_part = get_deal_query_left_status_tb_part();
	
	// Выбираем сделки пользователя 
	$sql = "SELECT i.deal_id, i.deal_price FROM ".DEALS_TB." i 
			".$left_status_tb_part."
			WHERE i.deal_deleted<>1 $and_user $date_part";
	 
	$res = $site_db->query($sql);
	
	$result_status['funnel_item_1'] = 0;
	$result_status['funnel_item_2'] = 0;
	$result_status['funnel_item_3'] = 0;
	$result_status['funnel_item_4'] = 0;
	
	$result_sum['funnel_item_1_sum'] = 0;
	$result_sum['funnel_item_2_sum'] = 0;
	$result_sum['funnel_item_3_sum'] = 0;
	$result_sum['funnel_item_4_sum'] = 0;
			
	while($row=$site_db->fetch_array($res))
	{
		// Актуальный статус
		$last_deal_status_arr = get_last_deal_status_arr($row['deal_id']);
		
		$deal_price_tmp = str_replace(' ', '', $row['deal_price']);
		
		$deal_price = 0;
		
		if(is_numeric($deal_price_tmp))
		{
			$deal_price = $deal_price_tmp;
		}
		
		if(1)
		{ 
			switch($last_deal_status_arr['status_id'])
			{
				// красная
				case '4':
				case '10':
					$result_status['funnel_item_1'] += 1;
					$result_sum['funnel_item_1_sum'] += $deal_price;
				break;
				// черная
				case '9';
					$result_status['funnel_item_4'] += 1;
					$result_sum['funnel_item_4_sum'] += $deal_price;
				break;
				// зеленая
				case '6';
				case '7';
				case '8';
				case '13';
					$result_status['funnel_item_3'] += 1;
					$result_sum['funnel_item_3_sum'] += $deal_price;
				break;
				// желтая
				default:
					$result_status['funnel_item_2'] += 1;
					$result_sum['funnel_item_2_sum'] += $deal_price;
				break;
			}
		}
	
	}
 
	$array_sum_status =  array_sum($result_status);
	
	$array_count = count($result_sum);
	
	$funnel_data_1 =  round($result_status['funnel_item_1'] / $array_sum_status * 100);
	$funnel_data_2 =  round($result_status['funnel_item_2'] / $array_sum_status * 100);
	$funnel_data_3 =  round($result_status['funnel_item_3'] / $array_sum_status * 100);
	$funnel_data_4 =  round($result_status['funnel_item_4'] / $array_sum_status * 100);
	
	
	//  echo "<pre>",  print_r($result_sum), "</pre>";
	
	// Статусы
	$funnel_width_1 = 60  + $funnel_data_1 * 2;
	$funnel_width_2 = 60  + $funnel_data_2 * 2;
	$funnel_width_3 = 60  + $funnel_data_3 * 2;
	$funnel_width_4 = 60  + $funnel_data_4 * 2;
	
/*	// Сумма по статусам
	$funnel_width_sum_1 = 80  + $funnel_data_1 * 2;
	$funnel_width_sum_2 = 80  + $funnel_data_2 * 2;
	$funnel_width_sum_3 = 80  + $funnel_data_3 * 2;
	$funnel_width_sum_4 = 80  + $funnel_data_4 * 2;*/
	
	$PARS['{FUNNEL_DATA_1}'] = $result_status['funnel_item_1'];
	$PARS['{FUNNEL_DATA_2}'] = $result_status['funnel_item_2'];
	$PARS['{FUNNEL_DATA_3}'] = $result_status['funnel_item_3'];
	$PARS['{FUNNEL_DATA_4}'] = $result_status['funnel_item_4'];
	
	$PARS['{FUNNEL_DATA_SUM_1}'] = sum_process($result_sum['funnel_item_1_sum']);
	$PARS['{FUNNEL_DATA_SUM_2}'] = sum_process($result_sum['funnel_item_2_sum']);
	$PARS['{FUNNEL_DATA_SUM_3}'] = sum_process($result_sum['funnel_item_3_sum']);
	$PARS['{FUNNEL_DATA_SUM_4}'] = sum_process($result_sum['funnel_item_4_sum']);
	
	$PARS['{FUNNEL_WIDTH_1}'] = $funnel_width_1;
	$PARS['{FUNNEL_WIDTH_2}'] = $funnel_width_2;
	$PARS['{FUNNEL_WIDTH_3}'] = $funnel_width_3;
	$PARS['{FUNNEL_WIDTH_4}'] = $funnel_width_4;
/*	
	$PARS['{FUNNEL_WIDTH_SUM_1}'] = $funnel_width_sum_1;
	$PARS['{FUNNEL_WIDTH_SUM_2}'] = $funnel_width_sum_2;
	$PARS['{FUNNEL_WIDTH_SUM_3}'] = $funnel_width_sum_3;
	$PARS['{FUNNEL_WIDTH_SUM_4}'] = $funnel_width_sum_4;*/
	 
	return fetch_tpl($PARS, $deal_sales_funnel_tpl);
}

// График статусов сделки
function fill_deal_schedule($deal_id)
{
	global $site_db, $user_obj;
	
	$deal_schedule_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deal_schedule.tpl');
	
	$deal_status_tooltip_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/deal_status_tooltip.tpl');
	
	// Выбор статусов
	$sql = "SELECT * FROM ".DEALS_STATUSES_TB." WHERE deal_id='$deal_id' ORDER by id ASC";
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		$date_tmp = substr($row['status_date'], 0, 10);
		
		$dates_arr[$date_tmp] = array('date' => $date_tmp, 'status_id' => $row['status_id'],'user_id'=> $row['user_id'], 'comment' => $row['status_report']);
	}
	
	ksort($dates_arr);
	
	// Проходим по датам и отмечаем точки эффективности
	foreach($dates_arr as $date => $status_data)
	{ 
		// Заполянем объект пользователя
		$user_obj->fill_user_data($status_data['user_id']);
		 
		// Отчет 
		$report = str_replace(array(chr(10)), '', nl2br($status_data['comment']));
		
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		$PARS['{STATUS_NAME}'] = get_deal_status_by_status_id($status_data['status_id']);
		$PARS['{REPORT}'] = $report;
		$PARS['{DATE}'] = formate_date($date, 1);
		
		// Текст точки для вывода в графике
		$tooltip_html = fetch_tpl($PARS, $deal_status_tooltip_tpl);
		
		$SERIES_DATA_ARR[$date] = series_data($date, switch_deal_status_weight_point($status_data['status_id']), $tooltip_html);
	
	}
	if($SERIES_DATA_ARR)
	{
		$series = '['.implode(',', $SERIES_DATA_ARR).']';
	}
	else
	{
		return '';
	}

	$PARS['{SERIES}'] = $series;
	
	
	return fetch_tpl($PARS, $deal_schedule_tpl);
}


// Возвращает вес точки статуса
function switch_deal_status_weight_point($status_id)
{
	switch($status_id)
	{
		// красная
		case '1':
			return 10;
		break;
		case '5':
			return 20;
		break;
		case '2':
			return 30;
		break;
		case '13':
			return 50;
		break;
		case '6':
			return 65;
		break;
		case '7':
			return 75;
		break;
		case '8':
			return 90;
		break;
		case '9':
			return 100;
		break;
		case '10':
		case '4':
			return 0;
		break;
		case '3':
			return 50;
		break;
		case '11':
			return 50;
		break;
		case '7':
			return 60;
		break;
	}
}

// Добавить группу
function check_deal_group($group_name, $add_if_not_exists)
{
	global $site_db, $current_user_id;
	
	if(!$group_name)
	{
		return '';
	}
	
	// Проверяем, есть ли такая группа сделок
	$sql = "SELECT group_id FROM ".DEALS_GROUPS_TB." WHERE group_name='$group_name'";
	  
	$row = $site_db->query_firstrow($sql);
	
	if($row['group_id'])
	{
		return $row['group_id'];
	}
	
	// Если группа не найдена и требуется создание такой
	if($add_if_not_exists)
	{
		$sql = "INSERT INTO ".DEALS_GROUPS_TB." SET group_name='$group_name', user_id='$current_user_id', dateadd = NOW()";
		
		$res = $site_db->query($sql);
		
		$group_id = $site_db->get_insert_id();
		
		return $group_id;
	}
}

// название группы по ее id
function get_deal_group_name_by_group_id($group_id)
{
	global $site_db, $user_obj;
	
	$sql = "SELECT group_name FROM ".DEALS_GROUPS_TB." WHERE group_id='$group_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['group_name'];
}


// Список для селекта всех групп сделок
function fill_deals_groups_list($group_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM ".DEALS_GROUPS_TB." ORDER by group_name DESC";
	
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{ 
		$selected = '';
		
		if($group_id)
		{
			$selected = $row['group_id'] == $group_id ? 'selected' : '';
		}
		
		$PARS2['{NAME}'] = $row['group_name'];
				
		$PARS2['{VALUE}'] = $row['group_id'];
				
		$PARS2['{SELECTED}'] = $selected;
				
		$deals_group_list .= fetch_tpl($PARS2, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl'));
	}
	
	return $deals_group_list;
}

function update_deals_status($deal_id, $deal_status)
{
	global $site_db, $current_user_id;
	
	// Обновляем последний статус сделки
	$sql = "UPDATE ".DEALS_TB." SET deal_last_status_date=NOW(), deal_last_status='$deal_status' WHERE deal_id='$deal_id'";
			
	$site_db->query($sql);
}
// Кол-во новых переданных сделок
function get_new_avalible_deals_count($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".DEALS_TB." i
			LEFT JOIN ".DEALS_ACCESSES_TB." a ON i.deal_id = a.deal_id
			WHERE a.user_id='$current_user_id' AND a.noticed = 0 AND i.deal_deleted<>1 ";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Устанавливаем флаг о просмотре переданных сделок
function set_noticed_new_avalible_deals_for_user($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "UPDATE ".DEALS_ACCESSES_TB." SET noticed = 1 WHERE user_id='$current_user_id'";
	
	$site_db->query($sql);
}
function get_deal_name_by_deal_id($deal_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM ".DEALS_TB." WHERE deal_id='$deal_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['deal_name'];
}

function downl_call($deal_id, $user_id){
    global $site_db;
    
    $deal_id;
    $sql = "SELECT deal_phone FROM ".DEALS_TB." WHERE deal_id='$deal_id' AND deal_deleted <> '1'";
    $row = $site_db->query($sql);
    $deal_phone = $site_db->fetch_array($row);
    $deal_phone = $deal_phone['deal_phone'];
    $deal_phone = preg_replace("/[^0-9]/", '', $deal_phone);
    if(strlen($deal_phone) == 11)$deal_phone = substr($deal_phone, 1);

    //$file = REC_CALL_PATH."/".$deal_phone.".m4a";//$_SERVER['DOCUMENT_ROOT']."/temp/rec_call/".$deal_phone.".m4a";
    $rec_call_path = REC_CALL_PATH.'/'.$user_id;
    $file = $rec_call_path."/".$deal_phone.".m4a";
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}
?>
