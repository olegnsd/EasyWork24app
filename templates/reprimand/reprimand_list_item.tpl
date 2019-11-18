<div class="user_cont_block reprimand_item {NOT_CONFIRM}" id="reprimand_{REPRIMAND_ID}">
<div class="cont_hide_after_act_{REPRIMAND_ID}">
{EDIT_TOOLS}
</div>

<table cellpadding="0" cellspacing="0">
	<tr>
    	<td class="user_left"><a href="/id{USER_ID}" class="user_link"><img src="{AVATAR_SRC}"/></a></td>
        <td class="user_right"><b><a href="/id{USER_ID}" class="user_link">{USER_SURNAME} {USER_NAME} {USER_MIDDLENAME}</a></b> <span class="user_position">{USER_POSITION}</span>
        
        <div class="user_cont">
        <div class="cont_hide_after_act_{REPRIMAND_ID}">
            <div class="user_cont_text">
            <div class="user_cont_block_type_title">{TYPE} <span style="font-weight:normal"></span></div>
            <div class=""> ому: {TO_SURNAME} {TO_NAME} {TO_MIDDLENAME} {NOT_CONFIRM_STATUS}</div>
            <div id="reprimand_text_{REPRIMAND_ID}" class="stand_margin" style="display:none">           
            {REPRIMAND_TEXT}
            
            <div class="user_cont_btn_block">
                {REPRIMAND_CONFIRM_BTN}
            <div class="clear"></div>
            <div class="stand_margin">
            <a href="javascript:;" class="link" onclick="$('#reprimand_text_{REPRIMAND_ID}').hide(); $('#reprimand_show_text_{REPRIMAND_ID}').show()">скрыть</a>
            </div>
        </div>
            </div>
            <div class="stand_margin" id="reprimand_show_text_{REPRIMAND_ID}">
            <a href="javascript:;" class="link" onclick="$('#reprimand_text_{REPRIMAND_ID}').fadeIn(200); $('#reprimand_show_text_{REPRIMAND_ID}').hide()">показать текст документа</a>
            </div>
            
			</div>
            <div class="user_cont_sub">{DATE_ADD}</div>
            
        </div>
        <div class="action_notice" id="action_notice_{REPRIMAND_ID}"></div>
        </div>
        </td>
    </tr>
</table>
<div class="clear"></div>
</div>