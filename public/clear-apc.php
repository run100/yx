<?php
if (in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')) && @$_SERVER['HTTP_APPKEY'] == '543c0bcfdd7d54e200e5499ddddad2dd') {
    opcache_reset();
    apcu_clear_cache();
    exit('done');
}
