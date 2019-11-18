Integration = {

    mielofonSave : function()
    {
        var active = $('#mielofon_active').is(':checked') ? 1 : 0;

        $.post('/ajax/ajaxIntegration.php',
            {
                mode : 'mielofon_save',
                active : active
            },
            function(data){

                if(active==1) {
                    $('.mielofon_key_wrap').show();
                    $('#mielofon_key').val(data.key);
                }
                else {
                    $('.mielofon_key_wrap').hide();
                    $('#mielofon_key').val('');
                }


            }, 'json');
    }
}