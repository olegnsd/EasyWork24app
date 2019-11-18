<script>
finance_id = '{FINANCE_ID}';
</script>

<table cellpadding="3" cellspacing="3">
	<tr>
    	<td class="td_title">Номер счета</td>
        <td class="td_value"><b>{FINANCE_ID}</b></td>
    </tr>
    <tr>
    	<td class="td_title">Название счета</td>
        <td class="td_value">{FINANCE_NAME}</td>
    </tr>
    <tr>
    	<td class="td_title">Остаток на счете</td>
        <td class="td_value"><span id="finance_summa">{FINANCE_SUMMA}</span> {FINANCE_CURRENCY}</td>
        <td class="error"></td>
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        
        <a href="javascript:;"  class="link" onclick="open_transfer_form()">Сделать перевод &rarr;</a>
        
        <div class="finance_transfer" id="finance_transfer_form_block">
        
        <div class="popup">
                <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="pp_top_left"></td>
                    <td class="pp_top_border"></td>
                    <td class="pp_top_right"></td>
                </tr>
                <tr>
                    <td class="pp_left_border"></td>
                    <td class="pp_cont">
                    
                    	<div class="access_proc" id="finance_search_acc_proc"></div>
						<a class="link popup_close" onclick="close_transfer_form()" href="javascript:;">Закрыть</a>
						<div class="clear"></div>

                        
                        <table cellpadding="0" cellspacing="0" style="margin-top:10px">
                            <tr>
                                <td class="td_title">Поиск по номеру счета</td>
                                <td class="td_value" ><input type="text" value="" class="input_text" style="width:70px" id="search_finance_id" onkeyup="get_finance_for_transfer()" /></td>
                            </tr>
                         </table>
                        
                        <div id="transfer_form"></div>
                        <div id="transfer_error" class="error"></div> 
                        
                    </td>
                    <td class="pp_right_border"></td>
                </tr>
                <tr>
                    <td class="pp_bottom_left"></td>
                    <td class="pp_bottom_border"></td>
                    <td class="pp_bottom_right"></td>
                </tr>
                </table>
                <div class="clear" style="clear:both"></div>
                </div>
        </div>
        </td>
        <td class="error"></td>
    </tr>
    
    <tr>
    	<td class="td_title"></td>
        <td class="td_value"><a href="javascript:;" id="show_add_finance_form_a" class="link" onclick="$('#add_finance_operation').fadeIn(200); $(this).hide()">Добавить операцию +</a></td>
       
    </tr>

</table>


<div style="display:none" id="add_finance_operation" class="add_form_margin">
<div class="title_add_form">Добавить операцию</div>
<div class="add_form">

<table cellpadding="0" cellspacing="0" class="add_client_tb">
	<tr>
    	<td class="td_title">Тип</td>
        <td class="td_value">
        <select id="operation_type" class="input_text" style="width:200px"><option value="0" selected="selected">- Выберите тип -</option>{FINANCE_OPERATIONS_TYPES_LIST}</select><div id="" class="td_error sub_input_error"></div></td>
        
    </tr>
    <tr>
    	<td class="td_title">Клиент</td>
        <td class="td_value"><div id="operation_client_block"><select id="operation_client"></select></div>
        <div id="operation_client_error" class="td_error sub_input_error"></div>
        <div id="client_notice" style="color: rgb(153, 153, 153); font-size: 11px; display:none">Добавить название клиента без добавления в постоянные клиенты</div>
        </td>
        
    </tr>
    <tr>
    	<td class="td_title">Сумма</td>
        <td class="td_value"><input type="text" id="operation_summa" value="0.00" class="finance_summa input_text" style="width:100px" />
        <div id="" class="td_error sub_input_error"></div></td>
        
    </tr>
    <tr>
    	<td class="td_title">Дата</td>
        <td class="td_value"><input type="text" id="operation_date" class="input_text" style="width:100px" disabled="disabled" value="{CURRENT_NORM_DATE}" /> <input id="operation_time" type="text" class="input_text" style="width:60px" />
        <div id="" class="td_error sub_input_error"></div></td>
        
    </tr>
    <tr>
    	<td class="td_title">Комментарий</td>
        <td class="td_value"><textarea class="input_text" id="operation_comment"></textarea></td>
        
    </tr>
    <tr>
    	<td></td>
        <td class="td_value" id="file_form_{FINANCE_ID}"></td>
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="add_finance_operation()" href="javascript:;" id="add_finance_operation_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить операцию</div></a>
    
        
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value"><div id="add_operation_proc"></div></td>
        
    </tr>
  
</table>

</div>
<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_finance_operation').hide(); $('#show_add_finance_form_a').show()">Скрыть</a>
</div>
</div>

<div class="add_new_list_item" >

</div>




<div class="tables_data_1_wrap_visible">
<div class="tables_data_1_wrap_back finance_tb_back_bl">
<table cellpadding="0" cellspacing="0" class="tables_data_1" style="width:1600px">
<thead>
<tr class="tr_th">
	<th class="">Номер</th>
    <th class="">Сумма входящей операции</th>
    <th class="">Сумма исходящей операции</th>
    <th class="">Клиент</th>
    <th class="">Остаток счета</th>
    <th class="">Сотрудник</th>
    <th class="">Дата</th>
    <th class="">Примечание</th>
    <th class="">Статус</th>
    <th class="">Действия</th>
</tr>
</thead>
<tbody id="finance_operations_list">
{FINANCE_OPERATIONS_LIST}
</tbody>
</table>

</div>
</div>
{FINANCES_CHARTS_30_DAYS}
{FINANCES_CHARTS_ONE_YEAR}


<script>

Disk.get_content_file_upload_form(finance_id, 2, 'file_form_'+finance_id);

$('.finance_summa').keydown(sum_mask_proc);
$('.finance_summa').blur(check_for_format);

$("#operation_date").datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true,
	  changeMonth: true,
      changeYear: true
    });

 set_operation_time_default();
 draw_background_list_item("operation_item");	
 
 $(document).ready(function(){ 
 	finance_clients_init();
 });
</script>

 