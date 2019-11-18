<div class="evcal_types_wrap">


<div class="evcal_types_lines_wrap">
{ADD_BTN}<div class="">Категории:</div>
<div class="types_list_wrap" id="types_list_wrap">
    <a class="tit ev_item_1"><input type="checkbox" checked="checked" value="1" option="0"/> По умолчанию</a>
    <a class="tit ev_item_2"><input type="checkbox" checked="checked" value="2" option="0"/> Сделки</a>
    {EVCAL_CATEGORIES_LIST}
</div>
</div>

<div class="evcal_types_add_wrap" id="evcal_types_form_wrap"></div>


<div class="clear"></div>
</div>
<div class="clear"></div>

<script>

$('#types_list_wrap  input:checkbox').bind('change', function(){
	Evcal.get_month_events();
});
// Получаем дни, в которых есть события
		//Evcal.get_month_events();
</script>
