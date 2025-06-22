<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $user = new User();
        $user->username = 'admin';
        $user->firstname = 'Admin';
        $user->lastname = 'User';
        $user->email = "mail@example.com";
        $user->password = bcrypt('admin');
        $user->role = UserRole::Admin;
        $user->save();

        $this->info('Admin user created successfully.');
        $this->info('Email: mail@example.com');
        $this->info('Password: admin');
    }
}
