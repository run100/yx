<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/11/15
 * Time: 上午9:57
 */

namespace App\Admin;


use Encore\Admin\Form\Field;

class PassportField extends AbstractFormField
{


    protected static function script()
    {
        return <<<eot
<script>
$(function() {
    $('.wj-passport').each(function() {
        var group = this;
        var dropdown = $('button', group);
        var text = $('input[type=text]', group);
        
        $('.dropdown-menu li a', group).click(function() {
            $('button .btn-txt', group).text($(this).text());
            dropdown.data('value', $(this).data('value'));
            updateHidden();
        });
        
        text.keyup(function() {
            updateHidden();
        });
        
        function updateHidden() {
            var ddVal = dropdown.data('value');
            var txt = text.val();
            $('input[type=hidden]', group).val(txt ? (ddVal + ':' + txt) : '');
        }
    });
});
</script>
eot;
    }

    public function getView()
    {
        return 'admin::form.passport';
    }
}