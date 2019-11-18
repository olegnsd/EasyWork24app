<div style="min-width:300px;">

<div class="corner_up" style="left:12px"></div>
<a class="add_ps_btn" href="/evcal?sf=1"><b>+</b> добавить</a>    
{NOTICE_BLOCK}

<div class="padn_wrap">
<div class="notice_title"><a href="/evcal?id={USER_ID}" class="link">Перейти в календарь событий &rarr;</a></div>
</div>
 
</div>

<script>
$('.hide_p_ev_btn').click(function(event){
	 
	event.stopPropagation();
})
tp_notice_bar_init('evcal');
</script>


