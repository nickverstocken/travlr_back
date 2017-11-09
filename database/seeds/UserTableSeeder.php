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
                'country' => 'Belgium (BE)',
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
                'country' => 'Belgium (BE)',
                'city' => 'Antwerpen',
                'time_zone' => 'Europe/Brussels',
                'is_verified' => true
            ),
            array(
                'name' => 'Verhoeven',
                'first_name' => 'Eveline',
                'email' => 'verhoeveneveline@gmail.com',
                'password' => Hash::make('test123'),
                'country' => 'Belgium (BE)',
                'city' => 'Belsele',
                'time_zone' => 'Europe/Brussels',
                'is_verified' => true
            ),
            array(
                'name' => 'Cerfontaine',
                'first_name' => 'Simon',
                'email' => 'simoncerfontaine@gmail.com',
                'password' => Hash::make('test123'),
                'country' => 'Belgium (BE)',
                'city' => 'Sinaai',
                'time_zone' => 'Europe/Brussels',
                'is_verified' => true
            ),
            array(
                'name' => 'Van Cleuvenbergen',
                'first_name' => 'Jens',
                'email' => 'jensken@gmail.com',
                'password' => Hash::make('test123'),
                'country' => 'Belgium (BE)',
                'city' => 'Sinaai',
                'time_zone' => 'Europe/Brussels',
                'is_verified' => true
            ),
            array(
                'name' => 'Van Der Linden',
                'first_name' => 'Ann',
                'email' => 'annvdl@gmail.com',
                'password' => Hash::make('test123'),
                'country' => 'Belgium (BE)',
                'city' => 'Eksaarde',
                'time_zone' => 'Europe/Brussels',
                'is_verified' => true
            )
        );
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
