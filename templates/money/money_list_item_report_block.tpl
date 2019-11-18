<div id="money_report_block_{MONEY_ID}" style="display:none">
<div class="cat_block cat_block_margin">Отчет:</div>
<div id="money_report_list_{MONEY_ID}">
{REPORTS_LIST}
</div>
{MONEY_REPORT_ADD_FORM}
<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#money_report_block_{MONEY_ID}').hide(); $('#show_money_report_{MONEY_ID}').show()">скрыть</a>
</div>
</div>
<div class="stand_margin">
<a href="javascript:;" id="show_money_report_{MONEY_ID}" class="link" onclick="$('#money_report_block_{MONEY_ID}').fadeIn(200); $(this).hide()">показать</a>
</div>