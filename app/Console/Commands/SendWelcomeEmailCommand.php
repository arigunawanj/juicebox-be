<?php

namespace App\Console\Commands;

use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Console\Command;

class SendWelcomeEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-welcome {user_id : The ID of the user to send welcome email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a welcome email to a specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        SendWelcomeEmailJob::dispatch($user);

        $this->info("Welcome email job dispatched for user: {$user->name} ({$user->email})");

        return 0;
    }
}
