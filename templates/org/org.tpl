<table cellpadding="0" cellspacing="0" style="width:100%" class="org_top_tb">
<tr>
<td style="width:100%" class="td">
<div class="search_back_bl">
<div class="search_list_block">
Подразделение <select style="width:300px" class="input_text" onchange="Org.search_users(1)" id="depts_list"><option value="0">- Все сотрудники -</option>{DEPTS_LIST}</select>
<br /><br />
<input type="input_text" id="search_word"  class="input_search" onkeyup="Org.search_users()" style="width:250px" placeholder="Начните вводить имя сотрудника.." /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="user_is_fired" onchange="Org.search_users()"> <label for="user_is_fired">Показать уволеных</label>
</div>
</div>
</div>
</td>
<td class="td">
{EDIT_TOOLS}
</td>
</tr>

</table>
 

<div class="clear"></div>

<div id="users_list_cont">
{USERS_LIST_CONT}
</div>
<div class="pop" id="pop" style=" "></div>


<script>
$('#search_word').focus();


$(window).scroll(function(){
	Load.list_scroll('users_list .user_item:last', Org.get_more)
});
 
</script>