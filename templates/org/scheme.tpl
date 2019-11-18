<script src="/js/jquery.ui.touch-punch.min.js"></script>


<div class="wrap_structure_scheme">


<div class="structure_scheme" style="width:{GRHY_CONT_WIDTH}px; " id="grhy">

<div row="1" class="depts_row" style="width:178px">
<div row="1" dept_pid="0">
<div dept_id="{DEPT_ID}" class="dept_item     dept_id_{DEPT_ID}" is_top="1" loaded="0">

 
</div>
</div>
</div>

<div class="clear"></div>


{LIST}
</div>

</div>

    
<script>
scheme = '{SCHEME}';

Org.scheme_lines_init();
Org.scheme_get_dept_info();
 

</script>
