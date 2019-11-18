<a href="javascript:;" onclick="show_deal_reminder_date();" class="access_btn link" style="color:#F60"><b>!</b> Напомнить о важности</a>  
<div id="deal_reminder_block" class="popup_win">
<a class="link access_bl_close" style="float:right" onclick="show_deal_reminder_date();" href="javascript:;">Закрыть</a>
<div class="clear"></div>
<div class="deal_notice_bl_dt">Дата <input type="text" id="deal_reminder_date" class="input_text" style="width:80px"/> &nbsp;&nbsp;&nbsp;<select class="input_text" style="padding:2px !important" id="deal_reminder_date_hour">{TIME_HOURS_LIST}</select> : <select class="input_text" style="padding:2px !important" id="deal_reminder_date_minute">{TIME_MINUTES_LIST}</select></div>
		<a class="button" onclick="set_deal_reminder_date('{DEAL_ID}')" href="javascript:;" id="deal_reminder_date_btn" style="margin-top:6px;">
    	<div class="right"></div><div class="left"></div><div class="btn_cont">напомнить</div></a>
<div>
<div class="clear"></div>
<div id="deal_reminder_error_box" class="error_box"></div>
<div id="deal_reminder_error_box" class="success_marg"></div>
</div>
</div>
<script>
$("#deal_reminder_date").datepicker({
			  showOn: "button",
			  buttonImage: "/img/calendar.gif",
			  buttonImageOnly: true,
			  changeMonth: true,
			  changeYear: true
			});	
</script>
