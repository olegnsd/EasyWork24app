<style>
body
{
	background-image:none ;
	background-color:#f2f2f2;
	background-image:url('/img/auth_back_easy.jpg');
	background-repeat:repeat
}
</style>
<table cellpadding="0" cellspacing="0" style="width:100%;height:100%">
<tr>
<td style="">
<div class="auth_left">&nbsp; </div>
</td>

<td style="width:900px; text-align:center; vertical-align:middle; height:100%;">
<div class="auth_panel">
<div class="auth_logo"></div>
<div class="auth_block">
 	<table cellpadding="0" cellspacing="0" class="auth_tb">
    <tr>
    <td class="auth_enter_td_title" >Логин:</td>
    <td class="auth_enter_td_input"><input style="width:280px;" type="text" value="" id="login" /></td>
    </tr>
    <tr>
    <td class="auth_enter_td_title" >Пароль:</td>
    <td class="auth_enter_td_input"><input style="width:280px;" type="password" value="" id="password" onkeyup="hide_auth_by_sms()" /></td>
    </tr>
    <tr style="display:none" id="auth_by_sms">
        <td class="auth_enter_td_title" ></td>
        <td class="auth_enter_td_input">
            <div class="auth_by_sms_code_text">
            <input type="hidden" id="is_sms_code" value="0" />
            <input style="width:36px;" id="sms_code" type="text" value="" />
            </div>
            <div class="auth_by_sms_title" id="auth_by_sms_notice"></div>
        </td>
    </tr>
    <tr>
    <td></td>
    <td class="auth_sub_block"> <a href="javascript:;" onclick="auth()"class="auth_enter_btn"></a></td>
    </tr>
    </table>
<div class="auth_error" id="auth_error"></div>
</div>
 </div>
</td>

<td>
<div class="auth_right">&nbsp;</div>
</td>

</tr>
</table>

<script>

$('#login').focus();

$('#password').keydown(function(e)
{
	if(e.which==13)
	{
		auth()
	}
})

$('#sms_code').keydown(function(e)
{
	if(e.which==13)
	{
		auth()
	}
})
</script>