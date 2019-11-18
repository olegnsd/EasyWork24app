<div id="reg_result"></div>

<script>

global_current_user_id = "{CURRENT_USER_ID}"

			 
</script>

{ADD_PLANNING_SESSION_FORM}

<div class="workers_stat_bl">
<div>Всего специалистов на рабочем месте: <b>{ALL_WORKERS_IS_WORKING_COUNT}</b></div>
<div>Онлайн: <b>{ALL_WORKERS_IS_ONLINE_COUNT}</b></div>
<div>Не вышло на работу: <b>{ALL_WORKERS_IS_NOT_WORKING_COUNT}</b></div>
</div>

<div id="add_my_workers_list"></div>

<div class="user_list_container" id="user_list_container">
{USERS_LIST}
</div>

<script>
tr_sf = '{TRIGGER_SHOW_ADD_FORM}';
if(tr_sf)
{
	$('#add_session_planning_btn a').trigger('click')
}
</script>