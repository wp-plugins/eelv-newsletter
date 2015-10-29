jQuery(document).ready(function () {
    jQuery('.newsform input[type=text]').each(function () {
        ph = jQuery(this).attr('placeholder');
        val = jQuery(this).val();
        if (ph != '') {
            if (val == '') {
                jQuery(this).val(ph);
            }
        }
        jQuery(this).focus(function () {
            ph = jQuery(this).attr('placeholder');
            val = jQuery(this).val();
            if (val == ph) {
                jQuery(this).val('');
            }
        });
        jQuery(this).blur(function () {
            ph = jQuery(this).attr('placeholder');
            val = jQuery(this).val();
            if (val == '') {
                jQuery(this).val(ph);
            }
        });
    });
});
