<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    protected $signature = 'user:make-admin {email : The user email}';
    protected $description = 'Assign admin role to a user by email';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No user found with email: {$email}");
            return self::FAILURE;
        }

        // Spatie role assignment
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('admin');
            $this->info("Assigned role 'admin' to {$email}");
            return self::SUCCESS;
        }

        $this->error("User model doesn't support assignRole(). Is Spatie installed/configured?");
        return self::FAILURE;
    }
}
