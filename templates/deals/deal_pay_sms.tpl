<div class="title_add_form">Выставить счёт через FREE-KASSA</div>
<form method=post>
<table cellpadding="0" cellspacing="0" class="add_client_tb">
	 
	<tr>
    	<td class="td_title">Сумма</td>
        <td class="td_value"><input type="text" name="pay_sms" value="{DEAL_PRICE}" class="input_text" /></td>
        
    </tr>

	<tr>
    	<td class="td_title">Текст</td>
        <td class="td_value"><textarea class="input_text" name="pay_sms_text">Ссылка на оплату вашего заказа на сумму {SUM} руб.: {PAY}</textarea></td>
        
    </tr>
   

	<tr>
    	<td class="td_title"></td>
        <td class="td_value"><button class="button" type=submit style="outline: none;border: 0;height: auto;line-height: 19px;float: none;">
    <div class="right"></div><div class="left"></div><div class="btn_cont">Отправить СМС со ссылкой на оплату на номер {DEAL_PHONE}</div></button> </td>
        
    </tr> 
     
</table>

</form>
