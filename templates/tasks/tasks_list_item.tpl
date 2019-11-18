<div class="task_item {TASK_BACK_CLASS}" id="task_{TASK_ID}" show=0>

<table cellpadding="0" cellspacing="0" class="task_carc_tb" >
<tr><td id="show_task_cont_bl_{TASK_ID}" class="task_item_show_block task_item_show_icon" style="width:26px" ></td>

<td style="padding-left:5px;">
<div class="task_item_cont">
<div class="task_cont_to_hide_{TASK_ID}">
{TASK_EDIT_TOOLS}

</div>
<table cellpadding="0" cellspacing="0" class="add_task_tb">
	<tr>
    	<td class="user_left"><a href="/id{USER_ID}" class="user_link"><img src="{AVATAR_SRC}"/></a></td>
        <td class="user_right" style="width:100%"><b><a href="/id{USER_ID}" class="user_link">{SURNAME} {NAME} {MIDDLENAME}</a></b> <span class="user_position">{USER_POSITION}</span>
        {DEPUTY_BOSS_BLOCK}
        <div class="user_cont">
        <div class="user_cont_text">
        	<div class="task_cont_to_hide_{TASK_ID}">
            	<div class="preview_task" id="preview_task_{TASK_ID}">{TASK_PREVIEW_TEXT}</div>
                
                <div class="d_none" id="task_hidden_content_{TASK_ID}">
                {TASK_THEME}
                {TASK_LINK_CONTENT}
                {TASK_TEXT}
                {FILES_LIST}
                <div class="user_cont_sub">{TASK_DATE_ADD} {EDIT_DATE}</div>
                <div class="task_sub_block">	
                    <div>Срочность: <b>{TASK_PRIORITY}</b> | Сложность: <b>{TASK_DIFFICULTY}</b> {NEW_REPORTS_SMS_NOTICE}</div>
                    {TASK_QUALITY}
                </div>
                
                <div class="task_btn_block">
                {TASK_ADMIN_STATUSES_BTN_BLOCK}
                <div class="clear"></div>
                </div>
                
                {TASK_REPORT_BLOCK}
                
                </div>
            </div>
            <div id="task_notice_{TASK_ID}"></div>
        </div>
             
        </div>
        
         
        </td>
    </tr>
</table>
</div>

<div class="task_sub_status">
<div class="sub_task_date_opt_bl">
{TASK_DESIRED_DATE}
{TASK_MAX_DATE}
</div>
 
<div style="float:left;">{NEW_REPORTS_COUNT_BLOCK}</div> 
<div style="float:left;">{TASK_STATUS}</div>

<div class="clear"></div>

<div class="task_status_ext_bl d_none" id="task_status_ext_{TASK_ID}">
{TASK_EXTEND_STATUS_WARNING}
{TASK_EXTEND_STATUS_FAIL}
{TASK_EXTEND_STATUS_INFO}
</div>
</div>
<div class="clear"></div>
</td>
</tr>
</table>

</div>

<script>
$('#show_task_cont_bl_{TASK_ID}').bind('click', function(){ show_task_content('{TASK_ID}')});
</script>