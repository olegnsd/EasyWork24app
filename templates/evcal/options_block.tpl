<div style="float:right" id="settings_bl_btn"><a href="javascript:;" class="link" onclick="$('#settings_bl').fadeIn(200); $('#settings_bl_btn').hide()">Настройки</a></div>

<div style="display:none" id="settings_bl" class="add_form_margin">
<div class="title_add_form">Настройки</div>
<div class="add_form add_form_margin">

<input type="checkbox" id="public_evcal" onchange="Evcal.public_evcal()" {PUBLIC_CHECKED}/> <label for="public_evcal">Разрешить просматривать мой календарь руководителю</label>

</div>

<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#settings_bl').hide(); $('#settings_bl_btn').show()">Скрыть</a>
</div>

</div>