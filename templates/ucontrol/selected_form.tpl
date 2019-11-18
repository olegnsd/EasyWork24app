<div>
<a class="options_btn" href="javascript:;" onclick="Ucontrol.show_user_options();" title="Настройка пользователя"></a>
<div class="title" style="width:670px;">{USER_SURNAME} {USER_NAME} {USER_SURNAME}, {USER_POSITION} <a href="javascript:;" class="link_proc" onclick="$('#select_user_wrap').toggle();">другой пользователь</a></div>

<div class="ucontrol_options_block" id="ucontrol_options_block">
 

<table cellpadding="0" cellspacing="0" class="ucontrol_options_tb">
<tr>
	<td class="td_t"><div class="tlt">Sipuni</div></td>
    <td class="td_v"></td>
</tr>
<tr>
	<td class="td_t">Номер телефона</td>
    <td class="td_v"><input type="text" class="input_text"  id="sipuni_phone" placeholder="Номер телефона sipuni.." value="{SIPUNI_PHONE}"/></td>
</tr>

<tr>
	<td class="td_t"></td>
    <td class="td_v"><br /></td>
</tr>

</table>

<div>
<a class="button"  href="javascript:;" id="save_user_options_btn" onclick="Ucontrol.save_user_options();">
        <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a><a class="cancel_add_btn" href="javascript:;" onclick="Ucontrol.show_user_options();">отменить</a>
</div>

</div>

</div>
<script>
selected_user_id = '{USER_ID}'
$('#select_user_wrap').hide();
</script>