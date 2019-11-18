<div id="add_form_block" class="add_form_margin">
<div class="title_add_form">Изменение данных проекта</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="tables_data_1">
<tbody>
	<tr>
    	<td class="td_title">Название</td>
        <td class="td_value"><input type="text" class="input_text" id="project_name" style="width:528px" value="{PROJECT_NAME}"  />
        <div class="td_error sub_input_error"></div>
        </td>
        
    </tr>
    <tr>
    	<td class="td_title td_vert_top">Описание</td>
        <td class="td_value"><textarea id="project_desc" class="input_text" style="width:528px; height:100px">{PROJECT_DESC}</textarea>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top">Ответственное лицо</td>
        <td class="td_value"><select id="project_head">{PROJECT_HEAD}</select>
        </td>
    </tr>
    </tbody>
</table>

<div style="margin-top:20px">
<a class="button" onclick="save_project_heads('{PROJECT_ID}')" href="javascript:;" id="save_project_btn">
<div class="right"></div><div class="left"></div><div class="btn_cont">сохранить проект</div></a> <a class="cancel_add_btn" onclick="close_popup('', 1);tinyMCE.editors['project_desc'].destroy()" href="javascript:;">Отмена</a>
<div class="clear"></div>
</div> 


<div class="error_box" id="error_box"></div>
<div id="success" class="success_marg"></div>

</div>
</div>


<script type="text/javascript">
tinymce.init({
    selector: "#project_desc",
	language : 'ru',
	plugins: ['table'
         
   ],menubar:false,
    toolbar1: "table"
    
 });
</script>
<script>
$('#project_head').easycomplete(
{
	str_word_select : 'Выбрать сотрудника',
	url:'/ajax/ajaxGetUsers.php?who=all&by=name&current_user=1&result_name=2',
	width:350,
	trigger : 1
});
</script>