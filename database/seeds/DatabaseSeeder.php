<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UserTableSeeder::class);
        $this->call(LocationsTableSeeder::class);
        $this->call(TripsTableSeeder::class);
        $this->call(StopsTableSeeder::class);
        $this->call(MediaTableSeeder::class);
    }
}
