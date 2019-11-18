<script>
user_id = "{USER_ID}";		
pages_count = '{PAGES_COUNT}'; 
accruals = 0;
payments = 1;
</script>

{SEARCH_PANEL}

{MONEY_ADD_FORM}

<div id="money_accruals_result"  class="money_accruals_result">
{USER_ACCRUALS_RESULT}
</div>



<div class="style_lis1t">
<table cellpadding="0" cellspacing="0" class="tables_data_1" style="width:100%">
<thead>
<tr class="tr_th">
	<th class="">Тип</th>
	<th class="">Сотрудник</th>
    <th class="">Дата</th>
    <th class="">Сумма</th>
    <th class="">Примечание</th>
    <th class="">Отчет</th>
    <th class="">Действия</th>
</tr>
</thead>
<tbody id="money_list">
{MONEY_LIST}
</tbody>
</table>
</div>

{MORE_MONEY}


 


 