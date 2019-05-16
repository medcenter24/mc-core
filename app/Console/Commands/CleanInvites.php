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

use medcenter24\mcCore\App\Services\InviteService;
use Illuminate\Console\Command;

class CleanInvites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invite:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete invites which already outdated';

    /**
     * @var InviteService
     */
    private $inviteService;

    /**
     * Create a new command instance.
     *
     * CleanInvites constructor.
     * @param InviteService $service
     */
    public function __construct(InviteService $service)
    {
        parent::__construct();
        $this->inviteService = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->inviteService->clean();
    }
}
