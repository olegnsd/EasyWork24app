<div class="evcal_types_add_wrap" id="evcal_types_add_wrap">
<div class="tlt">Добавить категорию</div>
<table cellpadding="0" cellspacing="0">
<tr>
	<td>Название:</td>
    <td><input type="text" id="ev_cat_name" style="color:#FFF; background-color:#309a25;  " placeholder="Название.." />
    <input type="hidden" value="309a25" id="ev_cat_color" /></td>
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
    <td><a href="javascript:;" class="add_type_btn" onclick="Evcal.add_evcal_cat();">Добавить</a> <a href="javascript:;" class="add_type_btn" onclick="$('#evcal_types_add_wrap').hide();" style="color:#900; border-color:#900">Отменить</a></td>
</tr>
</table>
</div>

<script>
$('#colors_wrap a').bind('click', function(){
	 
	var color = $(this).attr('color');
	$('#ev_cat_name').css('background-color', '#'+color);
	$('#ev_cat_color').val(color)
})
</script>