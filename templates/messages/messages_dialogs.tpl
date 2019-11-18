<div class="search_back_bl dialog_search_form">
<div class="search_list_block">


<div class="search_dialogs_input_wrap">
<input type="input_text" id="search_text" placeholder="Поиск пользователя или сообщений..."  class="input_search" style="width:705px" />
</div>

<div id="search_in_msgs_tool" class="search_in_msgs_tool" >

	<a class="search_msg_close" onclick="close_search_msgs();" href="javascript:;"></a>
    
    <div class="search_dialogs_date">
    	 
    	
        <a href="javascript:;" class="msg_search_date_bl" onclick="msg_date_block_op('open')">Дата <span id="date_selected"></span></a> 
        
        <div class="search_date_block" id="search_date_block">
        
    	<input type="text" id="from_date" class=" input_text" style="width:80px" placeholder="С.."  onchange="if(!$('#to_date').val()) {$('#to_date').val($('#from_date').val())}"/>
        <span id="to_date_wrap">&nbsp;&nbsp;<input type="text" id="to_date"  class="input_text" style="width:80px;" placeholder="По.."/></span>
        
        <div  style="margin-top:20px">
        	<a href="javascript:;" class="link" onclick="msg_date_block_op('select')">Выбрать</a><span id="date_delete_btn_wrap" style="display:none">&nbsp;&nbsp <a href="javascript:;" class="link" onclick="msg_date_block_op('delete')">Сбросить дату</a> </span><span id="date_cancel_btn_wrap">&nbsp;&nbsp;<a href="javascript:;" class="link" onclick="msg_date_block_op('cancel')">Отмена</a></span>
        </div>
        
        </div>
    </div>
    
    <div class="search_dialogs_btn_wrap">
    <a class="button" onclick="search_in_messages(1)" href="javascript:;" id="search_mesg_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">Поиск..</div></a>
    </div>
    
</div>


<div class="clear"></div>
</div>
</div>
<div id=""><div id="search_in_messages_btn" class="msg_main_list_item search_in_messages_btn" href="javascript:;" onclick="search_in_messages(1);">Найти <b><span id="msg_search_text"></span></b> в сообщениях..</div></div>

<div id="dialog_search_res"></div>

 
 
<div id="messages_search_wrap"></div>
 



<div id="dialogs_wrap">
{DIALOG_BLOCK}
</div>

<script>
$("#from_date").datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true,
	  changeMonth: true,
      changeYear: true
    });

$("#to_date").datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true,
	  changeMonth: true,
      changeYear: true
    });	
	
dialog_list_refresh();

$('#search_text').bind('keyup', dialogs_search);
$('#search_text').bind('keydown', function(e) {		 
	if(e.which==13)
	{
		 search_in_messages(1);
	}
});
</script>


