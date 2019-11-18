<div class="user_cont_block money_item {PLANNING_NOT_CONFIRM}" id="planning_{PLANNING_ID}">
<div class="cont_hide_after_act_{PLANNING_ID}">
{EDIT_TOOLS}
</div>

<table cellpadding="0" cellspacing="0">
	<tr>
    	<td class="user_left"><a href="/id{USER_ID}" class="user_link"><img src="{AVATAR_SRC}"/></a><div class="loading" id="loading_{COMMENT_ID}"></div></td>
        <td class="user_right"><b><a href="/id{USER_ID}" class="user_link">{USER_SURNAME} {USER_NAME} {USER_MIDDLENAME}</a></b> <span class="user_position">{USER_POSITION}</span>
        
        <div class="user_cont">
        <div class="cont_hide_after_act_{PLANNING_ID}" id="planning_id_{PLANNING_ID}">
            <div class="user_cont_text">
            <div class="user_cont_block_type_title">{PLANNING_TYPE}</div>
            {PLANNING_FOR_LIST}
            {PLANNING_DATES}
            <div id="planning_result_{PLANNING_ID}">{PLANNING_RESULT}</div>
			</div>
            <div class="user_cont_sub">{DATE_ADD}</div>
            
            <div class="user_cont_btn_block">
                {PLANNING_CONFIRM_BTN}
            <div class="clear"></div>
            
        </div>
        </div>
        <div class="action_notice" id="planning_notice_{PLANNING_ID}"></div>
        </div>
        </td>
    </tr>
</table>
<div class="clear"></div>
</div>