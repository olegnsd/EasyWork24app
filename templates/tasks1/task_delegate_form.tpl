<div class="title_add_form">Делегировать задачу</div>
<br />
<select id="task_delegate_performer"></select>
<br /><br /> 
<a class="button" onclick="Tasks.delegate_task('{TASK_ID}')" href="javascript:;" id="delegate_btn">
<div class="right"></div><div class="left"></div><div class="btn_cont">делегировать</div></a> <a onclick="close_popup('', 1)" href="javascript:;" class="cancel_add_btn">отмена</a>
<div  class="clear"></div>
    
<script>
$('#task_delegate_performer').easycomplete(
{
	str_word_select : 'Выбрать нового исполнителя',
	width:396,
	url:'/ajax/ajaxGetUsers.php?current_user=1&by=name&who=all'
});
</script>