<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('wanjia:fix-lumen', function () {

    $file = base_path('vendor/composer/autoload_real.php');
    $php = file_get_contents($file);

    if (strpos($php, '<?php // lumen-fixed') === 0) {
        $this->info("lumen-fixed");
        return;
    }

    $php = substr($php, 5);
    $replacement = <<<eot
\$vendorDir = dirname(dirname(__FILE__));
if (defined('USE_LUMEN')) {
    \$includeFiles = preg_grep('@/laravel/framework/src/Illuminate/Foundation/helpers.php@', \$includeFiles, PREG_GREP_INVERT);
} else {
    \$includeFiles = preg_grep('@/laravel/lumen-framework/src/helpers.php@', \$includeFiles, PREG_GREP_INVERT);
}
foreach (\$includeFiles as \$fileIdentifier => \$file) {
eot;


    //foreach ($includeFiles as $fileIdentifier => $file) {
    $php = str_replace('foreach ($includeFiles as $fileIdentifier => $file) {', $replacement, $php);
    $fixed_php = <<<eot
<?php // lumen-fixed
{$php}
eot;

    file_put_contents($file, $fixed_php);
    $this->info("lumen-fixed");

})->describe('Fix problems for lumen in laravel');



Artisan::command('cache:clear', function () {
    /** @var Illuminate\Foundation\Console\ClosureCommand $this */

    $this->error("Cache:clear 会清空Redis库，禁止使用!");

})->describe('禁用');
