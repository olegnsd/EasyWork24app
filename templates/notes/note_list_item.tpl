<div class="style_list_item note_item" id="note_{NOTE_ID}">
<div id="note_cont_{NOTE_ID}">
<table cellpadding="0" cellspacing="0">
<tr>
	<td class="note_version_open" id="note_version_{NOTE_ID}" note_id='{NOTE_ID}'>
    {NOTE_VERSION_ITEM}
    </td>
    <td class="note_versions">
    <div style="float:right">{HIDE_NOTE} <a href="javascript:;" onclick="get_note_access_block('{NOTE_ID}')" class="access_btn link">Передать</a></div>
    <div class="note_access_block" id="owner_block_{NOTE_ID}"></div>
    {NOTE_ACCESS_BLOCK}
   	<span class="note_vts_tl">Версии:</span>
    <div class="note_versions_wrap" id="note_versions_list_{NOTE_ID}">
    	 {NOTE_VERSIONS_LIST}
    </div>
    </td>
</tr>
</table>
</div>
<div id="note_cont_action_notice_{NOTE_ID}"></div>
</div>