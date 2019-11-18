<script>
user_id = '{USER_ID}';
checked_date = '{CHECKED_DATE}';
</script>

{ACTIVITY_BTNS}
{STATUSES}

<div class="clear"></div>
<div class="wktime_date_block">
Дата <input type="text" id="activity_date" class="select_date_inp input_text" value="{CHECKED_DATE}"/> <a href="javascript:;" onclick="show_user_activity_by_date()" class="link">показать</a>
</div>


{USER_ACTIVITY}



{WORKTIME_LIST}

{USER_PLANNING_CALENDAR}

<script>
$(function() {
	$.datepicker.setDefaults(
	        $.extend($.datepicker.regional["ru"])
	  );
	$("#activity_date").datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true
    });
});
</script>

{SCHEDULE_WORKTIME}

{SCHEDULE_CHANGE_DEAL_STATUSES_ACTUAL_DAY}
{SCHEDULE_CHANGE_DEAL_STATUSES_ACTUAL_30DAYS}


 