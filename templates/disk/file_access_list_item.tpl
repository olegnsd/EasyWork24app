<tr class="access_row" id="user_access_{ELEM}_{NUM}_row" num="{NUM}">
<td>
    {USER_NAME} <span class="user_position">{USER_POSITION}</span>
    <input type="hidden" value="{USER_ID}" id="user_access_{ELEM}_{NUM}">
</td>
<td>
   
    <select class="input_text" id="access_mode_{ELEM}_{NUM}" style="width:130px">
    <option value="1" {ACCESS_MODE_SELECTED_1}>Чтение</option>
    <option value="2" {ACCESS_MODE_SELECTED_2}>Изменение</option>
    </select>
    
</td>
<td>
<div class="edit_tools"><a href="javascript:;" class="delete" onclick="Disk.delete_access_user('{ELEM}', '{NUM}'); "></a></div>
</td>
</tr>