<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:make-admin {email : The email address of the user to promote} {--revoke : Remove admin access instead}')]
#[Description('Grant or revoke admin access for a user by email')]
class MakeUserAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No user found with that email address.');

            return self::FAILURE;
        }

        $user->is_admin = ! $this->option('revoke');
        $user->save();

        $this->info(sprintf(
            '%s is %s an administrator.',
            $user->email,
            $user->is_admin ? 'now' : 'no longer',
        ));

        return self::SUCCESS;
    }
}
