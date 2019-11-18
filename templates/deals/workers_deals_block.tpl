<tr class="">
<td colspan="12" class="deals_worker_head">
<div><a href="/id{USER_ID}" class='link'><b>{USER_SURNAME} {USER_NAME} {USER_MIDDLENAME}</b></a> <span>{USER_USER_POSITION}</span> | <a href="javascript:;" id="show_sales_funnel_{USER_ID}" onclick="show_sales_funnel('{USER_ID}', 1); $(this).hide(); $('#hide_sales_funnel_{USER_ID}').show();" class="link">показать график "Воронка продаж"</a> <a href="javascript:;" id="hide_sales_funnel_{USER_ID}" onclick="show_sales_funnel('{USER_ID}', 0);  $(this).hide(); $('#show_sales_funnel_{USER_ID}').show();" class="link" style="display:none">скрыть график "Воронка продаж"</a>
</div></td>
</tr>
<tr class="" style="display:none" id="sales_funnel_{USER_ID}">
	<td colspan="11">{DEAL_SALES_FUNNEL}</td>
</tr>
<tbody id="user_deals_list_{USER_ID}">
{DEALS_LIST}
</tbody>
{MORE_DEALS_BTN}