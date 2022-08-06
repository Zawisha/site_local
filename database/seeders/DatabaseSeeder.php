<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' =>1,
            'email' =>'admin@gmail.com',
            'password'=>Hash::make('admin')
        ]);
        DB::table('status')->insert([
            'user_id' =>1,
            'status'=>1
        ]);
    }
}
