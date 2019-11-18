<div class="task_quality_block">
Оценка качества работы: <select id="task_quality_select_{TASK_ID}" onchange="edit_task_quality('{TASK_ID}', $(this).val())" class="input_text">
<option id="0">Не выбрано</option>
{QUALITY_LIST}
</select><span id="task_quality_{TASK_ID}" class="task_quality_proc"></span>
</div>