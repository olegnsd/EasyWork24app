<div id="client_finances_block_{CLIENT_ID}" style="display:none; margin-top:15px">
<div class="cat_block">Внешние финансы:</div>
<table cellpadding="2" cellspacing="2" class="data_table client_data_table">
<thead>
<tr class="tr_th">
	<th class="nopdl">№</th>
    <th class="">Счет</th>
    <th class="">Сумма входящей операции</th>
    <th class="">Сумма исходящей операции</th>
    <th class="">Дата</th>
</tr>
</thead>
<tbody>
{FINANCES_LIST}
</tbody>
</table>
<a href="javascript:;" onclick="$('#client_finances_block_{CLIENT_ID}').hide(); $('#finances_btn_{CLIENT_ID}').show();" class="link">скрыть</a>
</div>
<div style="margin-top:15px" id="finances_btn_{CLIENT_ID}">
<a href="javascript:;"   onclick="$('#finances_btn_{CLIENT_ID}').hide(); $('#client_finances_block_{CLIENT_ID}').show()" class="link">Показать внешние финансы с клиентом</a>
</div>