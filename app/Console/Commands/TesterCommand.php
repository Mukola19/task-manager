<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TesterCommand extends Command
{
    protected $signature = 'tester';

    protected $description = '';


    public function handle()
    {


    }
}
