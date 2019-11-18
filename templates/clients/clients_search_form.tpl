<div class="search_back_bl">
<div class="search_list_block">
<input type="input_text" id="search_text" value="Начните вводить данные клиента..." class="input_default input_search"
onfocus="if($(this).val()=='Начните вводить данные клиента...'){ $(this).val(''); $(this).removeClass('input_default')}"  onblur="if($(this).val()==''){ $(this).val('Начните вводить данные клиента...'); $(this).addClass('input_default')}" onkeyup="clients_search()" />
</div>
<script>
default_search_text = 'Начните вводить данные клиента...';
</script>
</div>