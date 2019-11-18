<script>
user_id = '{USER_ID}';
max_image_resolution = '{MAX_IMAGE_RESOLUTION}';
min_image_resolution = '{MIN_IMAGE_RESOLUTION}';
</script>

<script>
$(document).ready(function(){
  
	$("#user_image_form").fancybox(
	{
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
	});
			 
})
</script>
<script src="/js/disk.js"></script>

    

<div>
<table cellpadding="0" cellspacing="0">
	<tr>
    	<td class="personal_left">
      	
        <div class="personal_title_position">{USER_POSITION}</div>
        <div style="margin-top:10px">{USER_STATUS}</div>
        
         <table cellpadding="0" cellspacing="0" class="personal_rows_tb">
            	
                
                {USER_BDATE}
            		
            	
                
                {USER_EXT}
            	
                <tr>
                	<td class="personal_row">Руководители:</td><td class="personal_row_value">
                    <div style="margin-top:-8px">{BOSS_LIST}</div>
                    <div class="d_none">{BOSS_LIST_HIDDEN}
                    <div style="margin-top:10px;">
                    	<a href="javascript:;" onclick="close_users_list_in_personal_block(this)" class="link">скрыть</a>
                    </div>
                    </div>
                    {BOSS_LIST_MORE}
                    </td>
                </tr>
                <tr>
                	<td class="personal_row">Сотрудники:</td><td class="personal_row_value">
                    <div style="margin-top:-10px">{WORKERS_LIST}</div>
                    <div class="d_none">{WORKERS_LIST_HIDDEN}
                    <div style="margin-top:8px;">
                    	<a href="javascript:;" onclick="close_users_list_in_personal_block(this)" class="link">скрыть</a>
                    </div>
                    </div>
                    {WORKERS_LIST_MORE}
                    </td>
                </tr>
            </table>
        
        </td>
        <td class="personal_right">
        	 
            {USER_AVATAR_BLOCK}
            {SEND_MSG_BTN}
        	{REG_DATA_SMS}
            {PERSONAL_EDIT_TOOLS} 
              
        </td>
    </tr>
</table>

{EXT_INFO_BLOCK} 
 

</div>

<div class="upload">
</div>