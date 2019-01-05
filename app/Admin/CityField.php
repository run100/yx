<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/11/15
 * Time: 上午9:57
 */

namespace App\Admin;


use Encore\Admin\Form\Field;

class CityField extends AbstractFormField
{

    protected static function script()
    {
        return <<<eot
<script>
$(function() {
    $('.wj-city-group').each(function() {
        var group = this;

        //Load country options
        $('.wj-city-part[data-level=1]', group).append(
            $('.wj-city-part[data-level=-1] option[data-parent=00]', group)
        );

        $('.wj-city-part[data-level!=-1]', group).change(function() {
            var lv = $(this).data('level');
            var nextLv = parseInt(lv) + 1;

            //Clear next level options
            $('.wj-city-part[data-level=-1]', group).append(
                $('.wj-city-part[data-level='+nextLv+'] option[data-parent]', group)
            ).find('option').removeAttr('selected');

            //Reload next level options
            if ($(this).val()) {
                $('.wj-city-part[data-level='+nextLv+']', group).append(
                    $('.wj-city-part[data-level=-1] option[data-parent='+$(this).val()+']', group)
                );
                $('input[type=hidden]', group).val($(this).val());
            }
            $('.wj-city-part[data-level='+nextLv+']', group).val('').trigger('change');
            

        });

        //Init default selection
        $('.wj-city-part', group).each(function() {
            var initVal = $(this).data('value');
            if (initVal) {
                $(this).val(initVal).trigger('change');
            }
        });
    });
});
</script>
eot;
    }


    public function getView()
    {
        return 'admin::form.city';
    }

}