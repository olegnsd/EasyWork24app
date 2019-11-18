<div style="display:none" id="add_client_form_block" class="add_form_margin">
<div class="title_add_form">Добавить нового клиента</div>
<div class="add_form add_form_margin">
<table cellpadding="0" cellspacing="0" class="add_client_tb">
<tr>
    	<td class="td_title">Название<sup>*</sup></td>
        <td class="td_value"><select id="client_organization_type" class="input_text" style="width:120px">{CLIENT_ORGANIZATIONS_TYPE_LIST}</select> <input style="width:407px" type="text" id="client_name" value="" class="input_text"   /><div id="client_name_error" class="td_error sub_input_error"></div></td>
          
    </tr>
    <tr>
    	<td class="td_title">ИНН</td>
        <td class="td_value"><input type="text" id="client_inn" value="" class="input_text" /></td>
         
    </tr>
</table>


<div class="add_form_cat_title">Контакты</div>
<table cellpadding="0" cellspacing="0" class="add_client_tb">
	<tr>
    	<td class="td_title">Контактное лицо</td>
        <td class="td_value"><input type="text" id="client_contact_person" value="" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Фактический адрес</td>
        <td class="td_value"><input type="text" id="client_address_actual" value="" class="input_text" /></td>
        
    </tr> 
    <tr>
    	<td class="td_title">Юридический адрес</td>
        <td class="td_value"><input type="text" id="client_address_legal" value="" class="input_text" /></td>
        
    </tr> 
    <tr>
    	<td class="td_title">Телефон</td>
        <td class="td_value"><input type="text" id="client_phone" value="" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Факс</td>
        <td class="td_value"><input type="text" id="client_fax" value="" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">E-mail</td>
        <td class="td_value"><input type="text" id="client_email" value="" class="input_text" /></td>
        
    </tr> 
</table>

<div class="add_form_cat_title">Реквизиты</div>
<table cellpadding="0" cellspacing="0" class="add_client_tb">
    <tr>
    	<td class="td_title">Банк</td>
        <td class="td_value"><input type="text" id="client_bank_name" value="" class="input_text" /></td>
        
    </tr> 
    <tr>
    	<td class="td_title">БИК</td>
        <td class="td_value"><input type="text" id="client_bik" value="" class="input_text" /></td>
        
    </tr> 
    <tr>
    	<td class="td_title">№ Счета</td>
        <td class="td_value"><input type="text" id="client_bank_account" value="" class="input_text" /></td>
        
    </tr>
   
</table>

<div class="add_form_cat_title"></div>
<table cellpadding="0" cellspacing="0" class="add_client_tb">
    <tr>
    	<td class="td_title">Описание</td>
        <td class="td_value"><textarea type="text" id="client_desc" class="input_text" /></textarea></td>
        
    </tr> 
    <tr>
    	<td class="td_title"></td>
        <td class="td_value"><input type="checkbox" id="client_private_edit" /> <label for="client_private_edit">Запретить редактирование информацию по клиенту всем, кроме вышестоящих сотрудников.</label></td>
        
    </tr>
     
</table>

<table cellpadding="0" cellspacing="0" class="add_client_tb">
    
    <tr>
    	<td class="td_title"></td>
        <td class="td_value"> <a class="button" onclick="add_new_client()" href="javascript:;" id="add_client_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить контрагента</div></a>
    </td>
    </tr> 
     
    <tr>
    <td class="td_title"></td>
    <td class="td_value"></td>
</tr> 
</table>
</div>

<div class="stand_margin">
<a href="javascript:;" id="" onclick="$('#add_client_form_block').hide(); $('#show_client_add_form_a').show()" class="link">скрыть форму добавления</a>
</div>
</div> 


<div class="add_new_list_item" >
<a  style="float:right" href="/clients?import=1" class="link">&darr; Импорт контрагентов</a>
<a href="javascript:;" id="show_client_add_form_a" class="link" onclick="$('#add_client_form_block').fadeIn(200); $(this).hide()">+ Добавить контрагента</a>  

<div class="clear"></div>
</div>
 
 
<script>
$("#client_inn").mask("9999999999");
$("#client_bik").mask("999999999");
$("#client_bank_account").mask("99999999999999999999");
</script>
