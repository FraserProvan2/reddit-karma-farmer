<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => config('reddit_api.username'),
            'email' => 'user@email.com',
            'password' => bcrypt('getmesomekarma'),
        ]);
    }
}
