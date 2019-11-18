<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>{TITLE}</title>
<script src="/js/jquery-1.8.3.min.js"></script>

<LINK rel=stylesheet type=text/css href="/css/style_new.css?v=4" />
<LINK rel=stylesheet type=text/css href="/css/style_popup.css" />

<LINK rel=stylesheet type=text/css href="/css/fck_style.css" />
<LINK rel=stylesheet type=text/css href="/css/calendar.css" />
<LINK rel=stylesheet type=text/css href="/css/pagination.css" />
<LINK rel=stylesheet type=text/css href="/css/jquery-ui-1.10.3.custom.min.css" />
<link rel="SHORTCUT ICON" href="/img/icon/favicon.ico?2"type="image/x-icon">
{CSS}
<script src="/js/messages.js?v=4"></script>
<script src="/js/functions.js?v=2"></script>
<script src="/js/worktime.js"></script>
<script src="/js/top_panel.js?v=2"></script>
<script src="/js/workers.js"></script>
<script src="/js/left_menu.js"></script>

<script src="/js/auth.js"></script>
<script src="/js/user.js"></script>
<script src="/js/video_instructions.js"></script>
<script src="/js/evcal.js?v=2"></script>
 
<script src="/js/jquery.fcbkcomplete.min.js"></script>
<script src="/js/jquery.easycomplete.js"></script>

<script src="/js/jquery.json-1.3.min.js"></script>
<script src="/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="/js/jquery.ui.datepicker-ru.js"></script>
<script src="/js/calendar.js"></script>
<script src="/js/jquery.pagination.js"></script> 
<script src="/js/autoresize.jquery.js"></script> 
<script src="/js/soundmanager2-jsmin.js"></script>

{SCRIPTS}


<script>
animate_title_stop = 0;
new_msgs_count = parseInt('{NEW_MSGS_COUNT}');
not_check_new_msgs = '{NOT_CHECK_NEW_MSGS}';
current_user_id = '{CURRENT_USER_ID}';
title = '{TITLE}';
o = '{O}';
$(document).ready(function()
{
	if(o!='auth')
	{
	 	set_u_act();
		Left_menu.check_new_count('new_tasks_count');
		check_new_msgs(current_user_id);
		$('textarea').autoResize();
	}
})
 
msg_audio_init();

</script>


</head>