<div class="user_cont_block project_report_item report_item_{PROJECT_ID} {REPORT_NOT_CONFIRM_CLASS}" id="report_{REPORT_ID}">
<div class="cont_hide_{REPORT_ID}_{PROJECT_ID}">{EDIT_TOOLS}</div>
<table cellpadding="0" cellspacing="0" >
	<tr>
    	<td class="user_left"><a href="/id{USER_ID}" class="user_link"><img src="{AVATAR_SRC}"/></a></td>
        <td class="user_right"><b><a href="/id{USER_ID}" class="user_link">{SURNAME} {NAME} {MIDDLENAME}</a></b> <span class="user_position">{USER_POSITION}</span>        
        <div class="user_cont">
        	<div class="cont_hide_{REPORT_ID}_{PROJECT_ID}">
         	<div class="user_cont_text">{TEXT}</div>
            <div class="user_cont_sub">{DATE}</div>
            {CONFIRM_BTN}
            </div>
            <div id="cont_report_result_{REPORT_ID}_{PROJECT_ID}"></div>
        </div>        
        </td>
    </tr>
</table>
</div>