<table cellpadding="0" cellspacing="0" class="user_settings_tb table">

<tr>
	<td class="td_title" style="width:115px"></td>
    <td class="td_value"><div class="us_bl_title_st ">Электронная почта</div></td>
</tr>
<tr>
	<td class="td_title">Задачи</td>
    <td class="td_value"><input type="checkbox" id="tasks_notice" {TASKS_CHECKED}/></td>
    <td class="td_error"></td>
</tr>
<tr>
	<td class="td_title">Проекты</td>
    <td class="td_value"><input type="checkbox" id="project_notice" {PROJECTS_CHECKED}/></td>
    <td class="td_error"></td>
</tr>

<tr>
	<td></td>
    <td class="td_value">
    <div class="us_bl_title"></div>
    <a class="button" onclick="save_settings_notices('{USER_ID}')" href="javascript:;" id="save_notice_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
    <br class="clear" />
    <div id="settings_result"></div>
    </td>
    <td  style="text-align:left"></td>
</tr>
</table>
