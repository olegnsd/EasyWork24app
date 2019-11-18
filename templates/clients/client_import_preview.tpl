<div class="tables_data_1_wrap_visible">
<div class="tables_data_1_wrap_back" style="width:710px; overflow-x:scroll">
<table cellpadding="0" cellspacing="0" id="deals_list" class="deals_list_tb tables_data_1">
<thead>
<tr class="tr_th">
	<th class="">Тип</th>
	<th class="">Название</th>
	<th class="">ИНН</th>
    <th class="">Контактное лицо</th>
    <th class="">Фактический адрес</th>
    <th class="">Юридический адрес</th>
    <th class="">Телефон</th>
    <th class="">Факс</th>
    <th class="">E-mail</th>
    <th class="">Банк</th>
    <th class="">БИК</th>
    <th class="">№ Счета</th>
    <th class="">Описание</th>
</tr>
</thead>
<tbody id="clients_list">
{CLIENTS_LIST}
</tbody>
</table>
</div>
</div>

<div style="margin:10px 0px 10px 0px">
Выберите опции, которые будут применимы для всех добавленных контрагентов:
</div>
<table cellpadding="0" cellspacing="0" class="add_client_tb">
   <tr>
    	<td class=""><input type="checkbox" id="client_private_edit" /> <label for="client_private_edit">Запретить редактировать и просматривать информацию по клиенту всем, кроме вышестоящих сотрудников.</label></td>
        
    </tr>
  
    <tr>
    	<td>
        <br /><a class="button" onclick="client_import_save('{IMPORT_FILE}')" href="javascript:;" id="client_import_save_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить список конрагентов</div></a>
    	
        <div style="float:left; padding:4px 0px 0px 10px">или <a href="javascript:;" onclick="$('#import_preview').html('');$('#import_add_form').show();" class="link">отменить и загрузить новый файл</a></div>
    	</td>
         
    </tr>
</table>

