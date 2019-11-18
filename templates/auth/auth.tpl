<style>
body
{
	background-image:url('/img/top_panel_back.jpg') ;
	background-repeat:repeat; 
}
</style>

 
<div class="auth_bl">
<div class="au_logo"></div>

<div class="au_form">

	<div id="auth_form" style="display:none1">
	<table cellpadding="0" cellspacing="0" class="auth_tb">
    
    <tr>
    <td class="auth_enter_td_title">Логин:</td>
    <td class="auth_enter_td_input"><div class="auth_restore_title" id="auth_restore_title">Восстановление доступа</div><input style="width:280px;" type="text" value="" id="login" onkeyup="hide_auth_by_sms()"/></td>
    </tr>
    <tr style="display:none" id="pass_method">
    <td class="auth_enter_td_title" >Пароль:</td>
    <td class="auth_enter_td_input"><input style="width:280px;" type="password" value="" id="password"  /></td>
    </tr>
    <tr style="display:none" id="auth_by_sms">
        <td class="auth_enter_td_title" ></td>
        <td class="auth_sub_block">
            <div class="auth_by_sms_code_text">
            <input type="hidden" id="is_sms_code" value="0" />
            <input type="hidden" id="restore_by_sms_code" value="0" />
            <input style="width:36px;" id="sms_code" type="text" value="" />
            
            </div>
            <div class="auth_by_sms_title" id="auth_by_sms_notice"></div>
        </td>
    </tr>
    <tr id="auth_df_btn">
    <td class=""></td>
    <td class="auth_sub_block"><a href="javascript:;" class="au_restore_btn" onclick="show_restore_auth_form();">Забыли пароль?</a> <a href="javascript:;" onclick="auth(0)"class="auth_btn auth_btn_fl" >Войти</a></td>
    </tr>
    
    <tr id="auth_restore_proc_btn" style="display:none">
    <td class=""></td>
    <td class="auth_sub_block"><a href="javascript:;" onclick="auth()" class="auth_btn auth_btn_fl">Войти</a> <a href="javascript:;" class="au_restore_btn" onclick="cancel_restore_auth_form();">&larr; Вернуться ко входу</a> </td>
    </tr>
    
    <tr id="auth_restore_btn" style="display:none">
    <td class=""></td>
    <td class="auth_sub_block"><div class="au_restore_sms_btn"><a href="javascript:;" onclick="auth(1);" class="auth_btn">Получить пароль по смс</a> <a href="javascript:;" style="color:#FFF" onclick="cancel_restore_auth_form()">Отмена</a></div></td>
    </tr>
    
    </table>
    </div>
    <a href="//www.free-kassa.ru/"><img src="//www.free-kassa.ru/img/fk_btn/16.png"></a>
    <!---->
    
    
    
    
    <div class="auth_error" id="auth_error"></div>
</div>

</div>
 


<script>
auth_ev_init();
</script>
