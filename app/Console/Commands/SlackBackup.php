<?php

namespace App\Console\Commands;

use App\Http\Controllers\Service\SlackBackupService;
use Illuminate\Console\Command;

class SlackBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slack:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'it will backup all public and private channels . without private messages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        auth()->loginUsingId("UKUHECPQR"); // change this to your own account #todo: move this to the database
        $service = new SlackBackupService();
        $service->BackupEveryThing();
        auth()->logout();
    }
}
