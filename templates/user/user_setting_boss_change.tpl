<table cellpadding="0" cellspacing="0" class="user_settings_tb table">
<tr>
	<td class="td_title"></td>
    <td class="td_value"><div class="us_bl_title_st ">Информация</div></td>
</tr>
<tr>
	<td class="td_title">Фамилия</td>
    <td class="td_value"><input {USER_NAME_DISABLED} type="text" id="surname" class="input_text" value="{SURNAME}"></td>
    <td class="td_error"></td>
</tr>
<tr>
	<td class="td_title">Имя</td>
    <td class="td_value"><input {USER_NAME_DISABLED} type="text" id="name" class="input_text" value="{NAME}"></td>
    <td class="td_error"></td>
</tr>
<tr>
	<td class="td_title">Отчество</td>
    <td class="td_value"><input {USER_NAME_DISABLED} type="text" id="middlename" class="input_text" value="{MIDDLENAME}"></td>
    <td class="td_error"></td>
</tr>
<tr>
	<td class="td_title">Дата рождения</td>
    <td class="td_value"><select id="bdate_day" class="input_select"  onchange="chenge_to_day=this.value"></select>
    <select id="bdate_month" class="input_select" onchange="init_select_date_block_init_days($('#bdate').val())"></select>
    <select id="bdate_year" class="input_select" onchange="init_select_date_block_init_days($('#bdate').val())"></select>
    <input type="hidden"  value="{BDATE}" id="bdate"/></td>
    <td class="td_error"></td>
</tr>
<tr>
	<td class="td_title">Должность</td>
    <td class="td_value"><input {POSITION_DISABLED} type="text" id="position" class="input_text" value="{POSITION}"></td>
    <td class="td_error"></td>
</tr>
<tr>
	<td class="td_title">Телефон</td>
    <td class="td_value"><select class="input_text" id="phone_country_code" onchange="change_phone_mask()" style="width:100px"><option value="RU">Россия</option><option value="BY">Белоруссия</option><option value="UK">Украина</option></select>&nbsp;&nbsp;&nbsp;<input type="text" id="phone" class="input_text" value="{PHONE}" style="width:150px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="send_reg_data_to_user('{USER_ID}')" id="send_reg_data_to_user_btn" class="link">Выслать логин по смс</a></td>
    <td class="td_error"></td>
</tr>
<tr>
	<td class="td_title">E-mail</td>
    <td class="td_value"><input type="text" id="email" class="input_text" value="{EMAIL}"></td>
    <td class="td_error"></td>
</tr>
<tr>
	<td class="td_title">Логин</td>
    <td class="td_value"><input type="text" id="login" class="input_text" value="{LOGIN}"></td>
    <td class="td_error"></td>
</tr>
<tr>
	<td class="td_title">Новый пароль</td>
    <td class="td_value"><input type="password" id="pass" class="input_text"></td>
    <td class="td_error"></td>
</tr>
<tr>
	<td class="td_title">Новый пароль еще раз</td>
    <td class="td_value"><input type="password" id="pass1" class="input_text"></td>
    <td class="td_error"></td>
</tr>



<tr>
	<td class="td_title"></td>
    <td class="td_value"><div class="us_bl_title">Структура</div></td>
</tr>

<tr>
	<td class="td_title">Подразделение</td>
    <td class="td_value"><select style="width:400px; height:150px; overflow-y:scroll" id="user_dept" class="input_text" multiple="multiple" >{DEPST_LIST}</select></td>
    <td class="td_error"></td>
</tr>

<tr>
	<td class="td_title"></td>
    <td class="td_value"><div class="us_bl_title">Безопасность</div></td>
</tr>

<tr>
	<td class="td_title"></td>
    <td class="td_value">
    <div class="auth_method_bl"><input type="radio" name="auth_method" id="auth_method_1" {AUTH_METHOD_0} value="0" /> <label for="auth_method_1">Авторизация через <b>пароль</b>.</label></div>
    <div class="auth_method_bl"><input type="radio" name="auth_method" id="auth_method_2" {AUTH_METHOD_1} value="1"/> <label for="auth_method_2">Авторизация через <b>смс-код</b>.</label></div>
    <div class="auth_method_bl"><input type="radio" name="auth_method" id="auth_method_3" {AUTH_METHOD_2} value="2"/> <label for="auth_method_3">Авторизация через <b>пароль</b> и <b>смс-код</b>.</label></div>
    </td>
    <td class="td_error"></td>
</tr>

<tr>
	<td class="td_title"></td>
    <td class="td_value"><div class="us_bl_title">Привилегии</div></td>
</tr>
<tr>
	<td class="td_title"></td>
    <td class="td_value">
     <div class="auth_method_bl"><input type="checkbox" id="user_is_admin" {USER_IS_ADMIN_CHECKED} {USER_IS_ADMIN_DISABLED}> <label for="user_is_admin">Администратор системы</label></div>
     </td>
    
</tr>
<tr>
	<td class="td_title"></td>
    <td class="td_value">
     <div class="auth_method_bl"><input type="checkbox" id="user_is_full_access" {USER_IS_FULL_ACCESS_CHECKED}> <label for="user_is_full_access">Полный доступ к профилям сотрудников</label></div>
     </td>
    
</tr>

<tr>
	<td class="td_title"></td>
    <td class="td_value"><div class="us_bl_title">Ограничение общения</div></td>
</tr>
<tr>
	<td class="td_title"></td>
    <td class="td_value">
     <input type="radio" style="margin-left:0px" name="user_limitation" value="1" {LIMITATION_CHECKED_1} /> Писать может только руководителю <br><input type="radio" style="margin-left:0px" name="user_limitation" value="2" {LIMITATION_CHECKED_2}/> Писать может только пользователям внутри отдела <br><input type="radio" style="margin-left:0px" name="user_limitation" value="0" {LIMITATION_CHECKED_0}/> Писать может всем
     </td>
    
</tr>

<tr>
	<td class="td_title"></td>
    <td class="td_value"><div class="us_bl_title" style="color:#C00">Отстранение от работы</div></td>
</tr>
<tr>
	<td class="td_title"></td>
    <td class="td_value">
     <div class="auth_method_bl"><input type="checkbox" id="user_is_fired" {USER_IS_FIRED_CHECKED} {USER_IS_ADMIN_DISABLED}> <label for="user_is_fired">Уволить</label></div>
     </td>
    
</tr>


<tr>
	<td class="td_title"></td>
    <td class="td_value"><div class="us_bl_title"></div></td>
</tr>

<tr>
	<td></td>
    <td class="td_value">
    <a class="button" onclick="save_profile_settings('{USER_ID}')" href="javascript:;" id="save_profile_settings_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
    <br class="clear" />
    <div id="settings_result"></div>
    </td>
    <td  style="text-align:left"></td>
</tr>
</table>

 
<script>
init_phone_mask();
init_select_date_block($('#bdate').val());
</script>