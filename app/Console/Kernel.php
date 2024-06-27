<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function scheduleTimezone()
    {
        return 'Asia/Tehran';
    }

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('tab:alarm')->dailyAt('21:35');
        $schedule->command('tab:create')->dailyAt('00:05');
        $schedule->command('tab:end')->dailyAt('08:00');

        $schedule->command('tab:alarmday')->dailyAt('13:01');
        $schedule->command('tab:createday')->dailyAt('14:01');
        $schedule->command('tab:end')->dailyAt('15:00');
        $schedule->command('send:messages')
            ->everyFiveMinutes();
        $schedule->command('divar:update')
            ->everyFifteenMinutes();


        foreach (['14:40', '01:15', '03:17', '05:19', '07:21',] as $time) {
            $schedule->command('tab:guard')->dailyAt($time);
        }

        $schedule->command('tab:validate')
            ->everyThirtyMinutes()->unlessBetween('8:00', '18:59');

        foreach (['8:05', '00:01',] as $time) {
            $schedule->command('send:messagesdaily')->dailyAt($time);
        }
        foreach (['11:35', /*'14:35', '17:35', '20:35',*/ '23:35',] as $time) {
            $schedule->command('send:productsdaily')->dailyAt($time);
        }

        foreach ([/*'8:14', '11:14', '14:34',*/ '17:14',/* '20:14', '23:14', '03:14'*/] as $time) {
            $schedule->command('send:randomdivar')->dailyAt($time);
        }

        foreach ([/*'8:20',*/ '14:20'/*, '20:20'*/,] as $time) {
            $schedule->command('send:messagesfun')->dailyAt($time);
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
