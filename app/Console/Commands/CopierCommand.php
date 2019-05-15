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

namespace medcenter24\mcCore\App\Console\Commands;


use Illuminate\Console\Command;
use medcenter24\mcCore\App\Helpers\FileHelper;

/**
 * Example: copy .doc only
 *
 * php artisan files:copy
 * /user/docs/
 * /user/docs_doc
 * /^.*\.(doc)$/i
 *
 * Class CopierCommand
 * @package medcenter24\mcCore\App\Console\Commands
 */
class CopierCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy something to other place by mask';

    public function handle(): void
    {
        $from = (string) $this->ask('Source directory (to copy from)');
        while (!FileHelper::isDirExists($from)) {
            $from = (string) $this->ask('Source directory which exists! (to copy from)');
        }
        $to = (string) $this->ask('Copy to directory (to copy to)');
        while (!FileHelper::isDirExists($to)) {
            $to = (string) $this->ask('Directory receiver which exists! (to copy to)');
        }
        $regExp = (string) $this->ask('RegExp to match files and dirs (or everything if not set)');

        $self = $this;
        $count = 0;
        $total = 0;
        FileHelper::copy($from, $to, $regExp, static function (string $filePath, $status) use ($self, &$count, &$total) {
            $self->comment($filePath . ' ' . ($status ? 'copied' : 'NOT copied'));
            $total++;
            if ($status) {
                $count++;
            }
        });

        $self->info('Copied ' . $count . ' files of ' . $total. '.');
    }
}
