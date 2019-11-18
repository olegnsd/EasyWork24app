<div class="add_form">
<div class="access_bl" style="float:right">
{REMINDER_ADD_BLOCK}
{ACCESS_BTN}
</div>
<div id="access_block_{DEAL_ID}" style=" position:relative;"></div>
<div style="margin-bottom:15px; font-size:15px">Сделка <span class="{DEAL_STATUS_BACK_CLASS}" id="deal_id_block_{DEAL_ID}">№ {DEAL_ID}</span> от {DEAL_DATE}</div>
<div id="deal_reminder_wrap">{DEAL_REMINDER_BLOCK}</div>

<table cellpadding="0" cellspacing="0" class="add_client_tb">
    <tr>
    	<td class="td_title">Название сделки</td>
        <td class="td_value"><input type="text" id="deal_name" value="{DEAL_NAME}" class="input_text" /></td>
    </tr>
    <tr>
    	<td class="td_title">Группа</td>
        <td class="td_value"><select id="deal_group">{DEAL_GROUP_EASYSELECTED}</select></td>
        
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">{DEALS_TYPES_BLOCK}</td>
    </tr>
    <tr>
    	<td class="td_title">Клиент<sup>*</sup></td>
        <td class="td_value"><select id="deal_client">{DEAL_CLIENT_OPTION}</select>
        <div id="client_name_error" class="td_error sub_input_error"></div>
        <div class="add_deal_sub_client" id="client_notice">Добавить название клиента без добавления в постоянные клиенты</div></td>
        
    </tr>
    <tr>
    	<td class="td_title">Контактное лицо</td>
        <td class="td_value"><input type="text" id="deal_contact_person" value="{DEAL_CONTACT_PERSON}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">E-mail</td>
        <td class="td_value"><input type="text" id="deal_email" value="{DEAL_EMAIL}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Адрес</td>
        <td class="td_value"><input type="text" id="deal_address" value="{DEAL_ADDRESS}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Телефон</td>
        <td class="td_value"><input type="text" id="deal_phone" value="{DEAL_PHONE}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Дополнительная информация</td>
        <td class="td_value"><textarea type="text" id="deal_other_info" class="input_text" />{DEAL_OTHER_INFO}</textarea></td>
        
    </tr>
    <tr>
    	<td class="td_title">Сумма сделки</td>
        <td class="td_value"><input type="text" id="deal_price" value="{DEAL_PRICE}" class="input_text" /></td>
        
    </tr>
    
    <tr>
    	<td class="td_title">Статус сделки</td>
        <td class="td_value"><select class="input_text"  style="width:200px" id="deal_status" ><option value="0">- Выберите статус сделки -</option>{DEALS_STATUSES_LIST}</select></td>
        
    </tr>
	<tr>
    	<td class="td_title">Отчет</td>
        <td class="td_value"><textarea type="text" id="deal_report" class="input_text" />{DEAL_STATUS_REPORT}</textarea></td>
        
    </tr> 
    
    <tr>
    	<td class="td_title" style="padding-bottom:0px"></td>
        <td class="td_value" style="padding-bottom:0px">{FILES_LIST}</td>
        
    </tr>
    
   
    
 

	{SAVE_DEAL_BTN} 
     
</table>

	{PAY_FORM} 

</div>

{TASKS_BLOCK}


<div id="deal_status_history_block">
{DEAL_HISTORY_BLOCK}
</div>

{TRACKING_BLOCK}

<div style="margin-bottom:50px"></div>

{DEAL_SHELDULE}


<script>
		
 $(document).ready(function(){                
            $('#deal_client').easycomplete(
			{
				str_word_select : 'Выбрать клиента',
				width:520,
				url:'/ajax/ajaxGetClients.php'
			});
			
			$('#deal_group').easycomplete(
			{
				str_word_select : 'Выбрать группу сделок',
				width:520,
				show_tag : 1,
				trigger : 1,
				url:'/ajax/ajaxDeals.php?mode=get_deals_groups'
			});
			
});

Disk.get_content_file_upload_form('{DEAL_ID}', 5, 'file_form_{DEAL_ID}');
</script>
