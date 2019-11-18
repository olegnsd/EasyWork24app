<div class="client_item" id="client_{CLIENT_ID}">
 
<div id="client_content_{CLIENT_ID}">

<div class="client_list_action_block">
{EDIT_TOOLS}
{CLIENT_ACCESS_BTN}
 
</div>
<div id="access_block_{CLIENT_ID}" style="position:relative;"></div>
 

<div id="take_client_access_block_{CLIENT_ID}" class="take_client_access_block">
<div id="access_to_client_cont_{CLIENT_ID}">
<div class="float_left">
Телефон <input type="text" class="input_text phone_to_client_access" id="phone_to_client_access_{CLIENT_ID}" style="width:120px" />
</div><div class="button_sep"></div>
<a class="button" onclick="take_access_to_client('{CLIENT_ID}')" href="javascript:;" id="take_access_to_client_btn_{CLIENT_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">передать</div></a>
    <div class="button_sep"></div>
<a class="button"onclick="hide_add_client_access('{CLIENT_ID}')" href="javascript:;" id="add_client_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">отменить</div></a>

<div class="clear"></div>    
 
<div style="" id="access_to_client_proc_{CLIENT_ID}"></div>
<div class="error access_to_client_error" id="phone_to_client_access_error_{CLIENT_ID}"></div>

</div>
<div id="access_to_client_result_{CLIENT_ID}"></div>
</div>

<table cellpadding="0" cellspacing="0" class="style_list_item_tb">
	<tr style="{CLIENT_NAME_DISPLAY}">
    	<td class="td_title">Название</td>
        <td class="td_value"><b>{CLIENT_ORGANIZATION_TYPE} {CLIENT_NAME}</b></td>
        <td class="error"></td>
    </tr>
    <tr style="{CLIENT_INN_DISPLAY}">
    	<td class="td_title">ИНН</td>
        <td class="td_value">{CLIENT_INN}</td>
        <td class="error"></td>
    </tr>
    <tr style="{CLIENT_CONTACT_PERSON_DISPLAY}">
    	<td class="td_title">Контактное лицо</td>
        <td class="td_value">{CLIENT_CONTACT_PERSON}</td>
        <td class="error"></td>
    </tr>  
    <tr style="{CLIENT_ADDRESS_ACTUAL_DISPLAY}">
    	<td class="td_title">Фактический адрес</td>
        <td class="td_value">{CLIENT_ADDRESS_ACTUAL}</td>
        <td class="error"></td>
    </tr>
    <tr style="{CLIENT_ADDRESS_LEGAL_DISPLAY}">
    	<td class="td_title">Юридический адрес</td>
        <td class="td_value">{CLIENT_ADDRESS_LEGAL}</td>
        <td class="error"></td>
    </tr>
    <tr style="{CLIENT_PHONE_DISPLAY}">
    	<td class="td_title">Телефон</td>
        <td class="td_value">{CLIENT_PHONE}</td>
        <td class="error"></td>
    </tr>
    <tr style="{CLIENT_FAX_DISPLAY}">
    	<td class="td_title">Факс</td>
        <td class="td_value">{CLIENT_FAX}</td>
        <td class="error"></td>
    </tr>
    <tr style="{CLIENT_EMAIL_DISPLAY}">
    	<td class="td_title">E-mail</td>
        <td class="td_value">{CLIENT_EMAIL}</td>
        <td class="error"></td>
    </tr>
    <tr style="{CLIENT_BANK_DISPLAY}">
    	<td class="td_title">Банк</td>
        <td class="td_value">{CLIENT_BANK}</td>
        <td class="error"></td>
    </tr>  
    <tr style="{CLIENT_BIK_DISPLAY}">
    	<td class="td_title">БИК</td>
        <td class="td_value">{CLIENT_BIK}</td>
        <td class="error"></td>
    </tr>  
    <tr style="{CLIENT_BANK_ACCOUNT_DISPLAY}">
    	<td class="td_title">№ Счета</td>
        <td class="td_value">{CLIENT_BANK_ACCOUNT}</td>
        <td class="error"></td>
    </tr>
    <tr style="{CLIENT_DESC_DISPLAY}">
    	<td class="td_title td_vert_top">Описание</td>
        <td class="td_value">{CLIENT_DESC}</td>
        <td class="error"></td>
    </tr>
</table><div class="clear"></div>
{ADDED_BY}

{TRACKING_BLOCK}
{DEALS_BLOCK}
{FINANCES_BLOCK}
 
</div>
<div id="client_result_{CLIENT_ID}"></div>
</div>
<script>
client_show = 1;
</script>