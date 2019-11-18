<div class="add_form {LIST_ITEM_CLASS}" id="client_{CLIENT_ID}" >
<div id="client_content_{CLIENT_ID}">

<table cellpadding="0" cellspacing="0" class="add_client_tb">
	<tr>
    	<td class="td_title">Название</td>
        <td class="td_value"><select class="input_text" id="client_organization_type_{CLIENT_ID}" style="width:120px">{CLIENT_ORGANIZATIONS_TYPE_LIST}</select> <input type="text" id="client_name_{CLIENT_ID}" value="{CLIENT_NAME}" class="input_text" style="width:407px" /><div id="client_name_error_{CLIENT_ID}" class="td_error sub_input_error"></div></td>
        
    </tr>
    <tr>
    	<td class="td_title">ИНН</td>
        <td class="td_value"><input type="text" id="client_inn_{CLIENT_ID}" value="{CLIENT_INN}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Контактное лицо</td>
        <td class="td_value"><input type="text" id="client_contact_person_{CLIENT_ID}" value="{CLIENT_CONTACT_PERSON}" class="input_text" /></td>
        
    </tr>  
    <tr>
    	<td class="td_title">Фактический адрес</td>
        <td class="td_value"><input type="text" id="client_address_actual_{CLIENT_ID}" value="{CLIENT_ADDRESS_ACTUAL}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Юридический адрес</td>
        <td class="td_value"><input type="text" id="client_address_legal_{CLIENT_ID}" value="{CLIENT_ADDRESS_LEGAL}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Телефон</td>
        <td class="td_value"><input type="text" id="client_phone_{CLIENT_ID}" value="{CLIENT_PHONE}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Факс</td>
        <td class="td_value"><input type="text" id="client_fax_{CLIENT_ID}" value="{CLIENT_FAX}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">E-mail</td>
        <td class="td_value"><input type="text" id="client_email_{CLIENT_ID}" value="{CLIENT_EMAIL}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Банк</td>
        <td class="td_value"><input type="text" id="client_bank_name_{CLIENT_ID}" value="{CLIENT_BANK}" class="input_text" /></td>
        
    </tr>  
    <tr>
    	<td class="td_title">БИК</td>
        <td class="td_value"><input type="text" id="client_bik_{CLIENT_ID}" value="{CLIENT_BIK}" class="input_text" /></td>
        
    </tr>  
    <tr>
    	<td class="td_title">№ Счета</td>
        <td class="td_value"><input type="text" id="client_bank_account_{CLIENT_ID}" value="{CLIENT_BANK_ACCOUNT}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Описание</td>
        <td class="td_value"><textarea type="text" id="client_desc_{CLIENT_ID}" class="input_text" >{CLIENT_DESC}</textarea></td>
        
    </tr> 
    {PRIVATE_OPTIONS}
     <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="save_client('{CLIENT_ID}')" href="javascript:;" id="save_client_btn_{CLIENT_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
    <div class="button_sep"></div>
    <a class="button" onclick="cancel_save_client('{CLIENT_ID}')" href="javascript:;" id="cancel_client_btn_{CLIENT_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">отменить</div></a>
    </td>
        <td class="td_error"></td>
    </tr> 
</table>
</div>
<div id="client_result_{CLIENT_ID}"></div>

<script>
$("#client_inn_{CLIENT_ID}").mask("9999999999");
$("#client_bik_{CLIENT_ID}").mask("999999999");
$("#client_bank_account_{CLIENT_ID}").mask("99999999999999999999");
</script>
</div>