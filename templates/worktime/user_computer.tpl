<script>
user_id = '{USER_ID}';
</script>

<table cellpadding="0" cellspacing="0" class="table">
<tr>
	<td class="td_title">Название компьютера</td>
    <td class="td_value"><input type="text" class="input_text" style="width:200px" value="{COMPUTER_NAME}" id="computer_name" /></td>
</tr>
<tr>
	<td class="td_title">Операционная система</td>
    <td class="td_value">{OS}</td>
</tr>
<tr>
	<td class="td_title">Разрешение экрана</td>
    <td class="td_value">{RESOLUTION}</td>
</tr>
<tr>
	<td class="td_title">Погрешность во времени с сервером</td>
    <td class="td_value">{TIME_FAULT}</td>
</tr>
<tr>
	<td class="td_title"></td>
    <td class="td_value"><input type="checkbox" id="computer_authed" {COMPUTER_AUTHED_CHECK} /> <label for="computer_authed">Авторизовать компьютер</label></td>
</tr>
<tr>
	<td class="td_title"></td>
    <td class="td_value">
    <a class="button " onclick="save_computer_name({COMPUTER_ID})" href="javascript:;" id="save_computer_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
    
    <div class="clear"></div>
    <div id="computer_result"></div>
</tr>
</table>





 