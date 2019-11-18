<div class="search_back_bl" style="border-radius:5px 5px 0px 0px">
<div class="search_list_block">
<input type="input_text" id="search_text"  value="Начните вводить данные сделки..." class="input_default input_search"
onfocus="if($(this).val()==default_search_text){ $(this).val(''); $(this).removeClass('input_default')}"  onblur="if($(this).val()==''){ $(this).val(default_search_text); $(this).addClass('input_default')}" onkeyup="deals_search()" />
</div>
</div>
<script>
default_search_text = 'Начните вводить данные сделки...';
</script>