<?php

use Illuminate\Database\Seeder;
use App\trip;

class TripsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('trips')->delete();
        //
        $trips = array(
            array(
                'user_id' => 1,
                'name' => 'Indonesië',
                'start_date' => '2017-09-02',
                'total_km' => 10250
            ),
            array(
                'user_id' => 1,
                'name' => 'Australie',
                'start_date' => '2017-10-12',
                'total_km' => 7000
            )
        );
        foreach ($trips as $trip) {
            Trip::create($trip);
        }
    }
}
