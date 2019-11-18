<select id="ofdoc_user_access_{ACCESS_ID}_{DEAL_ID}" class="access_user_item"><option value="{VALUE}" class="{CLASS}" >{NAME}</option></select>
<br />
<script>
$('#ofdoc_user_access_{ACCESS_ID}_{DEAL_ID}').easycomplete(
	{
		str_word_select : 'Выбрать пользователя',
		width:396,
		url:'/ajax/ajaxGetUsers.php?by=name&who=all_tree&result_name=1'
	});	
</script>