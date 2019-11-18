<div id="report_block_{GOOD_ID}" style="display:none">
<div class="cat_block" style="margin-top:5px">Отчет:</div>
<div class="" id="reports_list_{GOOD_ID}">{REPORT_LIST}</div>

<div class="add_form add_form_margin" id="add_report_form_{GOOD_ID}" style="display:{ADD_REPORT_FORM_DISPLAY}">


<textarea id="good_report_{GOOD_ID}" class="input_text" style="width:694px; height:60px"></textarea>
<br /> 

<div class="add_form_btn_margin">
<a class="button" onclick="add_good_report('{GOOD_ID}')" href="javascript:;" id="add_report_btn_{GOOD_ID}"><div class="right"></div><div class="left"></div><div class="btn_cont">добавить отчет</div></a>	
<div  class="clear"></div>
</div>

</div>

<div class="stand_margin"><a href="javascript:;" class="link" onclick="$('#report_block_{GOOD_ID}').hide(); $('#show_good_report_a_{GOOD_ID}').show()">скрыть</a></div>

</div>

<div style="margin-top:15px" id="show_good_report_a_{GOOD_ID}">
<a href="javascript:;" class="link" onclick="$('#report_block_{GOOD_ID}').show(); $('#show_good_report_a_{GOOD_ID}').hide()">Показать отчет</a>
</div>


