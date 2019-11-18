<div class="evcal_top">
{EVCAL_WORKERS_SELECT}
<div class="evcal_nav_lbl"><a href="javascript:;" class="evcal_nav" onclick="Evcal.evcal_get_month('prev')"><</a></div>
<div class="evcal_select_month_title" id="evcal_select_month_title"><span></span></div>
<div class="evcal_nav_rbl"><a href="javascript:;" class="evcal_nav" onclick="Evcal.evcal_get_month('next')">></a></div>
<div class="clear"></div>
<input type="hidden" value="" id="evcal_showed_date" />
</div>
<div class="evcal_tb_wrap" id="evt">
<table cellpadding="0" cellspacing="0" class="evcal_tb">
<thead class="evcal_week_days">
	<tr>
    	<th>Ïí</th>
        <th>Âò</th>
        <th>Ñð</th>
        <th>×ò</th>
        <th>Ïò</th>
        <th class="weekend">Ñá</th>
        <th class="weekend">Âñ</th>
    </tr>
</thead>
<tbody id="evcal_dates" class="evcal_dates">
</tbody>
</table>

<div id="evcal_events_list"></div>

</div>

<script>
evcal_user_id = '{EVCAL_USER_ID}';

show_event_id = '{SHOW_EVENT_ID}';
show_event_date_start = '{SHOW_EVENT_DATE_START}';

Evcal.evcal_get_month();

if(show_event_id)
{
	Evcal.get_events_list('', show_event_id, show_event_date_start);
}


</script>
