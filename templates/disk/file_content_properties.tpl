<table cellpadding="0" cellspacing="0" class="finfo_tb">
<tr>
	<td class="td_title">Название</td>
    <td>{FILE_NAME}</td>
</tr>
<tr>
	<td class="td_title">Размер</td>
    <td>{FILE_SIZE}</td>
</tr>
<tr>
	<td class="td_title">Создал</td>
    <td>
        <table cellpadding="0" cellspacing="0">
        <tr> 
        <td class="user_left" style="padding-right:10px"><a class="user_link" href="/id{USER_ID}"><img src="{AVATAR_SRC}"></a>
        
        </td>
        <td>
        <div class="user_name"><nobr><a href="/id{USER_ID}" target="_blank" class="userfullname link" onclick="event.stopPropagation()">{USER_SURNAME} {USER_NAME} {USER_MIDDLENAME}</a></nobr></div><div class="position"><span class="user_position">{USER_POSITION}</span></nobr></div>
        </td>
        </tr>
    	</table>
    </td>
</tr>
<tr>
	<td class="td_title">Дата создания</td>
    <td>{DATE_ADD}</td>
</tr>
<tr>
	<td class="td_title">Дата изменения</td>
    <td>{DATE_EDIT}</td>
</tr>
<tr>
	<td class="td_title fdesc">Описание <div style="position:relative">{EDIT_DESC_TOOL}</div></td>
    <td>
    <div class="file_desc_wrap" >
    	<div style=" width:550px; overflow:hidden" id="file_desc_text">{FILE_DESC}</div>
    </div>
    <div class="" id="file_desc_text_proc"></div>
    
    
    </td>
</tr>
</table>