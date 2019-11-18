
    <div style=" display:none" id="finance_operation_status_block_{OPERATION_ID}" class="add_form">
                <table cellpadding="2" cellspacing="2" class="tb_no_brds">
                <tr>
                    <td class="td_title">Статус</td>
                    <td class="td_value"><select id="operation_status_{OPERATION_ID}" class="input_text">{OPERATIONS_STATUSES_TYPES_LIST}</select></td>
                    
                </tr>
                <tr>
                    <td class="td_title">Комментарий</td>
                    <td class="td_value"><textarea id="operation_comment_{OPERATION_ID}" class="input_text" style="width:318px"></textarea></td>
                    
                </tr>
                <tr>
                    <td class="td_title"></td>
                    <td class="td_value"><a class="button" onclick="add_finance_operation_status('{OPERATION_ID}')" href="javascript:;" id="add_finance_operation_status_{OPERATION_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить статус</div></a>
  
                    <div id="operation_status_proc_{OPERATION_ID}"></div></td>
                    
                </tr>
                
                <tr>
                    <td class="td_title"></td>
                    <td class="td_value">
                    <div id="operation_statuses_list_{OPERATION_ID}">
                    {OPERATIONS_STATUSES_LIST}
                    </div>
                    
                    </td>
                    
                </tr>
            </table>
            
            <div class="stand_margin"> 
            <a href="javascript:;" class="link" onclick="$('#finance_operation_status_block_{OPERATION_ID}').hide(); $('#show_finance_status_a_{OPERATION_ID}').show()">Скрыть</a>
            </div>
            
            </div>
            <a href="javascript:;" id="show_finance_status_a_{OPERATION_ID}" class="link" onclick="$('#finance_operation_status_block_{OPERATION_ID}').fadeIn(200); $(this).hide()">&darr;  статус операции </a>
            