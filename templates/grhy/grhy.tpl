<script src="/js/jquery.ui.touch-punch.min.js"></script>
<div class="grhy_title"><a href="/grhy?scheme=1" class="grhy_title_link {SCHEME_ACTIVE_1}">Полное дерево иерархии</a> | <a href="/grhy?scheme=2" class="grhy_title_link {SCHEME_ACTIVE_2}">Графическое представление статусов сотрудников</a></div>

{VISIBLE_USER_CONT_MENU}

<div class="wrap_grhy">
<div class="left_menu_grhy">
{LEFT_MENU}
</div>

 
<a href="javascript:;" onclick="g_slide_left_menu()" class="menu_slide"></a>



<div class="grhy" style="width:{GRHY_CONT_WIDTH}px; left:-{CENTER_CONT}px;" id="grhy">

<div row="1" class="workers_row" style="width:312px">
<div row="1" boss_id="0" class="workers_group_boss ">
<div user_id="{USER_ID}" class="worker_user_item worker_item boss_item  worker_id_{USER_ID}" is_top="1">
<table cellspacing="0" cellpadding="0" class="boss_it_tb">
<tr>
<td style="padding-left:10px"><img src="{AVATAR_SRC}"></td>
<td><div style="width:180px; max-height:30px" class="user_name"><span class="userfullname">{SURNAME} {NAME} {MIDDLENAME}</span></div><div class="position">{USER_POSITION}</div></td>
</tr>
</table>
</div>
</div>
</div>

<div class="clear"></div>


{LIST}
</div>

</div>

    
<script>
scheme = '{SCHEME}';

grhy_lines_init();
grhy_get_user_info();
$().ready(function() {
	$('.grhy').draggable({
});	  
});

</script>
