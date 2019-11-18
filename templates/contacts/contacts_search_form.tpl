<div class="search_back_bl">
<div class="search_list_block">
<input type="input_text" id="contact_search_text"  value="Начните вводить данные контакта..." class="input_default input_search"
onfocus="if($(this).val()=='Начните вводить данные контакта...'){ $(this).val(''); $(this).removeClass('input_default')}"  onblur="if($(this).val()==''){ $(this).val('Начните вводить данные контакта...'); $(this).addClass('input_default')}" onkeyup="contact_search()" />

</div>
</div>
<script>
default_search_text = 'Начните вводить данные контакта...';
</script>