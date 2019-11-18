<div class="title_add_form add_form_margin_1">Изменить круг обязанностей:</div> 
<div class="add_form">
<table cellpadding="0" cellspacing="0" class="add_task_tb">
<tr>
	<td class="td_title">Обязанность:</td>
    <td class="td_value"><textarea style="width:500px; height:100px;" class="input_text" id="work_text"></textarea></td>
</tr>
<tr>
    <td class="td_title td_vert_top">Периодичность отчета:</td>
    <td class="td_value"><select id="periodicity" class="input_text">{PERIODICITY_REPORT_OPTIONS}</select>
    <div class="sub_text">
    <input type="checkbox" id="work_sms_notice_to_boss" /> <label for="work_sms_notice_to_boss">Уведомлять по <b>SMS</b> о новых отчетах и этапах выполнения задания</label></div>
    </td>
</tr>
<tr>
	<td class="td_title"></td>
	<td class="td_value">
    <a class="button" onclick="add_user_work('{USER_ID}')" href="javascript:;" id="add_user_work_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить круг обязанностей</div></a>
	<div class="clear"></div>
    
    <div id="add_work_success"></div>
    </td>
</tr>
</table>
</div> 