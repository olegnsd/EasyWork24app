<div class="cat_block_item_title">Выплаты за последние 30 дней:</div>
<table cellpadding="0" cellspacing="0" class="tables_data_1" style="width:100%; margin-top:5px">
<thead>
<tr class="tr_th">
	<th class="">Сотрудник</th>
    <th class="">Дата</th>
    <th class="">Сумма</th>
     
</tr>
</thead>
<tbody>
{MONEY_LIST_VISIBLE}
</tbody>
<tbody class="money_list_hidden d_none">
{MONEY_LIST_HIDDEN}
</tbody>

{MORE_BTN}

<tbody>
<tr class="tb_data_1_row">
<td style="border:none "></td>
<td><b>Итого:</b></td>
<td><b>{RESULT_SUM} руб.</b></td>
</tr>
</tbody>
</table>

<div id="money_list_hidden" class="d_none money_list_hidden">
<div style="margin-top:10px;">
    <a href="javascript:;" onclick="$('#show_money_btn').show(); $('.money_list_hidden').hide()" class="link">скрыть</a>
</div>
</div>
 
 
 