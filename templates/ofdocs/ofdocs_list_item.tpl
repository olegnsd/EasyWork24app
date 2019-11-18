<div class="user_cont_block money_item " id="ofdoc_{OFDOC_ID}"  >
<div class="cont_hide_after_act_{OFDOC_ID}">
{EDIT_TOOLS}<div class="access_bl">
<a href="javascript:;" onclick="get_ofdoc_access_block('{OFDOC_ID}')" class="access_btn link">Передать</a></div>{STATUS_BLOCK}

<div class="item_access_block" id="access_block_{OFDOC_ID}"></div>
</div>

<table cellpadding="0" cellspacing="0" style="height: 100%">
	<tr>
    	<td class="user_left" style="background-color:{COLOR}; height: 100%">
            <table cellspacing="0" cellpadding="0" style="height: 100%">
                <tr>
                    <td style="height: 100%; vertical-align: top">
                        <a href="/id{USER_ID}" class="user_link"><img src="{AVATAR_SRC}"/></a>


                    </td>
                </tr>
                <tr>
                    <td>{OFDOC_TO_USER}</td>

                </tr>
            </table>
         </td>
        <td class="user_right"><b><a href="/id{USER_ID}" class="user_link">{USER_SURNAME} {USER_NAME} {USER_MIDDLENAME}</a></b> <span class="user_position">{USER_POSITION}</span>

        <div class="user_cont">
        <div class="cont_hide_after_act_{OFDOC_ID}">
            <div class="user_cont_text">
            <div class="user_cont_block_type_title">№ {OFDOC_ID} {OFDOC_TYPE}</div>
            <div class="">Кому: {TO_SURNAME} {TO_NAME} {TO_MIDDLENAME}</div>
            <div id="ofdoc_text_{OFDOC_ID}" class="stand_margin" style="display:none">
            {OFDOC_TEXT}
            <div class="stand_margin">
            <a href="javascript:;" class="link" onclick="$('#ofdoc_text_{OFDOC_ID}').hide(); $('#ofdoc_show_text_{OFDOC_ID}').show()">скрыть</a>
            </div>
                <div style="margin-top: 10px">
                    {USERS_LIST}
                </div>
            </div>
            <div class="stand_margin" id="ofdoc_show_text_{OFDOC_ID}">
            <a href="javascript:;" class="link" onclick="$('#ofdoc_text_{OFDOC_ID}').fadeIn(200); $('#ofdoc_show_text_{OFDOC_ID}').hide()">показать текст документа</a>
            </div>
			</div>
            <div class="user_cont_sub">{DATE_ADD}</div>
            <div class="user_cont_btn_block">
            <div class="clear"></div>

        </div>
        </div>
        <div class="action_notice" id="action_notice_{OFDOC_ID}"></div>
        </div>
        </td>
    </tr>
</table>
<div class="clear"></div>
</div>