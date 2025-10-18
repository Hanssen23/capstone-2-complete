<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPasswordCommand extends Command
{
    protected $signature = 'admin:reset-password {email} {password}';
    protected $description = 'Reset admin password';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $user->password = Hash::make($password);
        $user->email_verified_at = now();
        $user->save();

        $this->info("✅ Password reset successfully for {$email}");
        $this->info("New password: {$password}");
        $this->info("User role: {$user->role}");

        return 0;
    }
}
