<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\FileViewFinder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app('view')->addExtension('html', 'blade');

        $this->loadViewsFrom(public_path('web'), "web");
        $this->loadViewsFrom(resource_path('views/vendor/zhuanti'), 'zhuanti');

        \Blade::directive('json', function ($exp) {
            return "<?php echo wj_json_encode($exp)?>";
        });

        \Blade::directive('route', function ($name, $parameters = '[]', $absolute = 'true') {
            return "<?php echo route($name, $parameters, $absolute)?>";
        });

        \Blade::directive('jsonattr', function ($exp) {
            return "<?php echo htmlspecialchars(wj_json_encode($exp), ENT_COMPAT, 'UTF-8', false)?>";
        });

        \Blade::directive('lconfig', function ($exp) {
            return "<?php \$__layout = array_merge($exp, @\$__layout ?: [])?>";
        });

        \Blade::directive('upload_url', function ($exp) {
            return "<?php echo htmlspecialchars(upload_url($exp), ENT_COMPAT, 'UTF-8', false)?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}

