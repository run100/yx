<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //参考: https://laravel.com/docs/5.4/scheduling
//        $schedule->command('wanjia:jly2018:incr')
//            ->dailyAt('00:00')
//            ->description("金龙鱼专题每天加票");
//        $schedule->command('wanjia:sjbjc:incr')
//            ->dailyAt('00:01')
//            ->description("世界杯竞猜专题每天加预测机会");

    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');

        foreach (glob(module_path('*', 'console.php')) as $file) {
            require $file;
        }
    }
}
