<LINK rel=stylesheet type=text/css href="/css/pagination.css" />

<div class="content_block_padn">


{ADD_FORM}


<div class="task_report_list_wrap" id="task_reports_list"></div>

<div id="pagination" class="pagination"></div>

</div>

<script>
current_page = 0;
reports_per_page = '{REPORTS_PER_PAGE}';
Tasks.init_task_report_list('{TASK_ID}');
</script>

