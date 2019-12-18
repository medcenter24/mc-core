<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Console;

use medcenter24\mcCore\App\Console\Commands\CleanInvites;
use medcenter24\mcCore\App\Console\Commands\CleanStorageDev;
use medcenter24\mcCore\App\Console\Commands\CopierCommand;
use medcenter24\mcCore\App\Console\Commands\CreateUserCommand;
use medcenter24\mcCore\App\Console\Commands\SeedInstallerCommand;
use medcenter24\mcCore\App\Console\Commands\SetupEnvironmentCommand;
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
        SetupEnvironmentCommand::class,
        CopierCommand::class,
        SeedInstallerCommand::class,
        CleanStorageDev::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
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
    protected function commands(): void
    {
        require base_path('routes/console.php');
    }
}
