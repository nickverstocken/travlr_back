<?php

use Illuminate\Database\Seeder;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        //
        $users = array(
            array(
                'name' => 'Verstocken',
                'first_name' => 'Nick',
                'email' => 'verstockennick@gmail.com',
                'password' => Hash::make('test123'),
                'country' => 'Belgium',
                'city' => 'Sint-Niklaas',
                'time_zone' => 'Europe/Brussels',
                'role' => 'admin',
                'is_verified' => true
            ),
            array(
                'name' => 'Verstocken',
                'first_name' => 'Bart',
                'email' => 'verstockenbart@gmail.com',
                'password' => Hash::make('test123'),
                'country' => 'Belgium',
                'city' => 'Antwerpen',
                'time_zone' => 'Europe/Brussels',
                'is_verified' => true
            )
        );
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
