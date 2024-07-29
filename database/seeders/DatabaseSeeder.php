<?php

namespace Database\Seeders;

use App\Actions\Users\CreateUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Artisan::call('permission:crud --r=SuperAdmin --p=super-admin-permission');

        Artisan::call('permission:crud',
            [
                '--r' => ['Admin'],
                '--p' => [
                    'create-category',
                    'view-any-categories',
                    'view-any-category',
                    'delete-any-category',
                    'update-any-category',
                    'delete-any-category',

                    'create-task',
                    'view-any-tasks',
                    'view-any-task',
                    'delete-any-task',
                    'update-any-task',
                    'delete-any-task',

                    'create-task',
                    'view-any-users',
                    'view-any-user',
                    'delete-any-user',

                    'view-any-users',
                    'view-any-user',
                    'delete-any-user',
                ],
            ]);

        Artisan::call('permission:crud',
            [
                '--r' => ['User'],
                '--p' => [
                    'create-category',
                    'view-own-categories',
                    'view-own-category',
                    'delete-own-category',
                    'update-own-category',
                    'delete-own-category',

                    'create-task',
                    'view-own-tasks',
                    'view-own-task',
                    'delete-own-task',
                    'update-own-task',
                    'delete-own-task',
                ],
            ]);


        $user = app(CreateUser::class)->handle([
            'name' => 'Admin',
            'email' => 'a@gmail.com',
            'password' => '123456',
        ]);

        $user->assignRole('Admin');
    }
}
