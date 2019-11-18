<script src="/js/integration.js"></script>
<form method=post>
<table cellpadding="0" cellspacing="0" class="user_settings_tb table">
    <tr>
        <td class="td_title"></td>
        <td class="td_value"><div class="us_bl_title_st ">Free Kassa (отправка ссылок на оплату лидам)</div></td>
    </tr>

    <tr>
        <td class="td_title" style="width: 100px">Включить</td>
        <td class="td_value"><input type="checkbox" name="kassa[active]" value="1" {KASSA_ACTIVE}></td>
        <td class="td_error"></td>
    </tr>
    <tr>
        <td class="td_title">ID Вашего магазина</td>
        <td class="td_value"><input   type="text" name="kassa[id]" class="input_text" value="{KASSA_ID}"></td>
        <td class="td_error"></td>
    </tr>

    <tr>
        <td class="td_title">Ключ 1</td>
        <td class="td_value"><input   type="text" name="kassa[key1]" class="input_text" value="{KASSA_KEY1}"></td>
        <td class="td_error"></td>
    </tr>

    <tr>
        <td class="td_title">Ключ 2</td>
        <td class="td_value"><input   type="text" name="kassa[key2]" class="input_text" value="{KASSA_KEY2}"></td>
        <td class="td_error"></td>
    </tr>
<tr>
        <td class="td_title">Адрес оповещения</td>
        <td class="td_value">http://{HOST}/payalert.php</td>
        <td class="td_error"></td>
    </tr>
<tr>
        <td class="td_title">Адрес для переадресации после успешной оплаты</td>
        <td class="td_value">http://{HOST}/payinfo.php</td>
        <td class="td_error"></td>
    </tr>

    <tr>
        <td class="td_title"></td>
        <td class="td_value"><button type="submit" class="button" style="outline: none;border: 0;height: auto;line-height: 19px;float: none;"> <div class="right"></div><div class="left"></div><div class="btn_cont">Сохранить Free-Kassa</div></button></td>
        <td class="td_error"></td>
    </tr>
   


</table>
</form>
<br><br>

<table cellpadding="0" cellspacing="0" class="user_settings_tb table">
    <tr>
        <td class="td_title"></td>
        <td class="td_value"><div class="us_bl_title_st ">Миелофон</div></td>
    </tr>

    <tr>
        <td class="td_title" style="width: 100px">Включить</td>
        <td class="td_value"><input type="checkbox" id="mielofon_active" value="1" onchange="Integration.mielofonSave();"></td>
        <td class="td_error"></td>
    </tr>
    <tr class="mielofon_key_wrap">
        <td class="td_title">Ключ интеграции</td>
        <td class="td_value"><input   type="text" id="mielofon_key" class="input_text" value="{MIELOFON_KEY}"></td>
        <td class="td_error"></td>
    </tr>
    <tr class="mielofon_key_wrap">
        <td class="td_title">Точка входа</td>
        <td class="td_value"><input   type="text" id="mielofon_key" class="input_text" value="http://{HOST}/services/deals_import/"></td>
        <td class="td_error"></td>
    </tr>


</table>

<script>
    var mielofon_active = '{MIELOFON_ACTIVE}';

    if(mielofon_active==1) {
        $('#mielofon_active').attr('checked', 'checked');
    }
    else {
        $('.mielofon_key_wrap').hide()
    }
</script>

<script>
    init_phone_mask();
    init_select_date_block($('#bdate').val());
</script>
