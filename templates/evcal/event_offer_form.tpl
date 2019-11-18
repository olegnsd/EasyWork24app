<div class="">
<div class="tlt">Предложить событие "<b>{EVENT_NAME}</b>".</div>

<div class=""></div>

<div id="event_offer_form">
<table cellpadding="0" cellspacing="0"  class="f_access_wrap_file_tb"  style="margin-top:10px">
<tbody id="offer_rows_{EVENT_ID}">
</tbody>
<tbody>
<tr>
<td><a href="javascript:;" onclick="Evcal.add_offer_user_row('{EVENT_ID}')" class="link">+ Добавить сотрудника</a></td>
</tr>
</tbody>
</table>
<br />
<a class="button"   href="javascript:;" id="add_offer_btn" onclick="Evcal.add_event_offer('{EVENT_ID}')">
<div class="right"></div><div class="left"></div><div class="btn_cont">предложить событие</div></a>

<a href="javascript:;" onclick="close_popup('offer_form')" class="cancel_add_btn">Отмена</a>

</div>
<div class="clear"></div>

<div id="event_offer_result"></div>

</div>