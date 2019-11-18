<script>
user_id = "{USER_ID}";
pages_count = '{PAGES_COUNT}';
client_show = 0;
client_list_type = '{CLIENT_LIST_TYPE}';	 
</script>

{TOP_MENU}

{CLIENT_ADD_FORM}

{CLIENT_SEARCH_FORM}

<div class="tables_data_1_wrap_visible">
<div class="tables_data_1_wrap_back" style="width:1800px">
<table cellpadding="0" cellspacing="0" id="deals_list" class="deals_list_tb tables_data_1">
<thead>
<tr class="tr_th">
	<th class="">Действия</th>
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
</tr>
</thead>
<tbody id="clients_list">
 {CLIENTS_LIST}
</tbody>
</table>
</div>
</div>
{MORE_CLIENTS}
<script>
$(".phone_to_client_access").mask("+7 (999) 999-99-99");
</script>