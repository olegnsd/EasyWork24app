<div class="cat_block_item_title">Начисления сотрудника за 30 дней:</div>
<table cellpadding="0" cellspacing="0" class="tables_data_1" style="width:100%; margin-top:5px">
<thead>
<tr class="tr_th">
    
    <th class="">Сотрудник</th>
    <th class="">Дата</th>
    <th class="">Тип</th>
    <th class="">Сумма</th>
</tr>
</thead>
<tbody id="accruals_list">
{LIST_VISIBLE}
</tbody>
{MORE_BTN}
<tbody class="accruals_list_hidden d_none">
{LIST_HIDDEN}
</tbody>

<tbody>
<tr class="tb_data_1_row">
<td colspan="2" style="border:none "></td>
<td><b>Итого:</b></td>
<td><b>{ACCRUALS_SUM_30_DAYS} руб.</b></td>
</tr>
</tbody>

</table>


 
<div  id="money_list_hidden" class="d_none accruals_list_hidden">
<div style="margin-top:10px;">
    <a href="javascript:;" onclick="$('#show_accrual_btn').show(); $('.accruals_list_hidden').hide()" class="link">скрыть</a>
</div>
</div>
 
 
 