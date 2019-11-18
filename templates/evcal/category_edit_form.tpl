<div class="" id="evcal_types_add_wrap">
<div class="tlt">Редактирование</div>
<table cellpadding="0" cellspacing="0">
<tr>
	<td>Название:</td>
    <td><input type="text" id="ev_cat_name_{CATEGORY_ID}" style="color:#FFF; background-color:#{CATEGORY_COLOR};" placeholder="Название.." value="{CATEGORY_NAME}" />
    <input type="hidden" value="{CATEGORY_COLOR}" id="ev_cat_color_{CATEGORY_ID}" /></td>
</tr>
<tr>
	<td>Цвет:</td>
    <td>
    <div class="colors_wrap" id="colors_wrap">
    {COLORS}
    </div>
    </td>
</tr>
<tr>
	<td></td>
    <td><a href="javascript:;" class="add_type_btn" onclick="Evcal.save_evcal_cat('{CATEGORY_ID}');">Сохранить</a> <a href="javascript:;" class="add_type_btn" onclick="Evcal.delete_evcal_category('{CATEGORY_ID}');" style="color:#F03; border-color:#F03">Удалить</a> <a href="javascript:;" class="add_type_btn" onclick="$('#evcal_types_add_wrap').hide();" style="color:#900; border-color:#900">Отменить</a></td>
</tr>
</table>
</div>

<script>
$('#colors_wrap a').bind('click', function(){
	 
	var color = $(this).attr('color');
	$('#ev_cat_name_{CATEGORY_ID}').css('background-color', '#'+color);
	$('#ev_cat_color_{CATEGORY_ID}').val(color)
})
</script>