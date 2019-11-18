<div class="search_back_bl">
<div class="search_list_block">
<input type="input_text" id="search_text"  value="Начните вводить данные задачи..." class="input_default input_search"
onfocus="if($(this).val()==default_search_text){ $(this).val(''); $(this).removeClass('input_default')}"  onblur="if($(this).val()==''){ $(this).val(default_search_text); $(this).addClass('input_default')}" onkeyup="get_tasks_list('{USER_ID}')" />
</div>
</div>
<script>
is_tasks_to_users = '{TASKS_TO_USERS}';

default_search_text = 'Начните вводить данные задачи...';
</script>