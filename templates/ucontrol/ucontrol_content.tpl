<div class="content_block">
<div class="add_form" id="select_user_wrap">
<table cellpadding="0" cellspacing="0" class=" ">
<tr>
    <td class="td_title">Пользователь</td>
    <td class="td_value"><select id="select_user"></select></td>
</tr>
<tr>
    <td class="td_title"></td>
    <td class="td_value"><a class="button" onclick="Ucontrol.select_user()" href="javascript:;" id="">
        <div class="right"></div><div class="left"></div><div class="btn_cont">выбрать</div></a>
     <div class="clear"></div>
     <div id="select_result"></div></td>
</tr>
</table>
</div>

{SELECTED_FORM}

</div>

{TABS}




<script>
$('#select_user').easycomplete(
{
	str_word_select : 'Выбрать пользователя',
	width:396,
	url:'/ajax/ajaxGetUsers.php?current_user=1&by=name&who=all'
});

$("#for_date").datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true,
	  changeMonth: true,
      changeYear: true
});
</script>