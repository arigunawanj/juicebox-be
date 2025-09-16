<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Queueable;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // For demo purposes, we'll just log the welcome email
            // In a real application, you would send an actual email
            Log::info('Welcome email sent to user', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'name' => $this->user->name
            ]);

            // Example of how to send actual email (uncomment when mail is configured):
            // Mail::to($this->user->email)->send(new WelcomeEmail($this->user));

        } catch (\Exception $e) {
            Log::error('Welcome email job failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
