<?php

namespace App\Console\Commands;

use App\Services\InviteService;
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
