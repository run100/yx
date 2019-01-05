<?php
foreach ($validator['rules'] as $field => &$rule) {
    if (config('jsvalidation.disable_remote_validation')) {
        unset($rule['laravelValidationRemote']);
    }
}

echo wj_json_encode($validator['selector']) . ': ' . wj_json_encode($validator['rules']);
