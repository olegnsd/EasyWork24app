<div id="version_{VERSION_ID}">
<input type="text" id="note_theme_{VERSION_ID}" value="{NOTE_THEME}" class="input_text"  style="width:490px;"/>
<br /><br />
<textarea id="note_text_{VERSION_ID}" class="input_text" style="width:490px; height:60px;">{NOTE_TEXT}</textarea>
<br class="clear" />	
<a class="button btm" onclick="save_note_version('{VERSION_ID}', '{NOTE_ID}')" href="javascript:;" id="save_note_version_btn_{VERSION_ID}"><div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
<div class="button_sep"></div>
<a class="button btm" onclick="get_note_version_item('{VERSION_ID}', 0, 0, 1)" href="javascript:;"><div class="right"></div><div class="left"></div>отменить</a>
<br class="clear" />	

</div>
     
    