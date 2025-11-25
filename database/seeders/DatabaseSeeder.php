<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create demo users
        User::create(['name'=>'John Employee','email'=>'john@local.test','password'=>Hash::make('password'),'role'=>'employee']);
        User::create(['name'=>'Jane Supervisor','email'=>'jane@local.test','password'=>Hash::make('password'),'role'=>'supervisor']);
        User::create(['name'=>'Robert MD','email'=>'robert@local.test','password'=>Hash::make('password'),'role'=>'md']);
    }
}
