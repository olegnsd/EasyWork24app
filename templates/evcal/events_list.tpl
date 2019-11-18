<div class="cal_events_wrap" id="events_list_{DATE}">

<div style="{ADD_FORM_DISPLAY}" id="add_form_block_{DATE}">
{ADD_FORM}
</div>

<div class="list" style="{EVENT_LIST_DISPLAY}">
<div class="selected_date"><a href="javascript:;" onclick="Evcal.hide_events_list('{DATE}')" class="close_20"></a>События - {DATE_STR}  &nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="Evcal.show_event_add_form('{DATE}')" class="add_event_btn"><b>+</b> Добавить событие</a></div>
<div class="time_wrap">
<table cellpadding="0" cellspacing="0" class="event_list_tb">
<thead>
<tr>
	<th>Время</th>
    <th>Название</th>
    <th>Описание</th>
    <th>Автор</th>
    <th></th>
</tr>
</thead>
{EVENTS_LIST}
</table> 

</div>
</div>

<script>
show_list_date = "{DATE}";
</script>
</div>