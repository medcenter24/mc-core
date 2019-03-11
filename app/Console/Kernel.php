<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Console;

use App\Console\Commands\CleanInvites;
use App\Console\Commands\CreateUserCommand;
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
        CleanInvites::class,
        CreateUserCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // clearing redundant invites
        $schedule->command('invite:clean')->weekly();
        // creating backups
        $schedule->command('db:backup --database=mysql --destination=dropbox --compression=gzip --destinationPath=/'.
            config('app.env').'/'.config('app.name')
            .' --timestamp=Y_m_d_H_i_s')->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
