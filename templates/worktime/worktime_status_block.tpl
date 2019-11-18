<div class="user_status_sep"></div>
<div class="user_status_carcas">
<a class="user_status_st_down" href="javascript:;" onclick="show_statsu_list();"></a>
<div class="user_status_st_list_selected" id="user_status_active">{ACTIVE_USER_STATUS}</div>
<div class="clear"></div>

<div class="wk_statuses_list" id="wk_statuses_list">
<a href="javascript:;" status_id="0">Статуса нет</a>
{STATUSES_LIST}
</div>

</div>

<script>
$('#wk_statuses_list a').bind('click', change_user_status)
</script>

 