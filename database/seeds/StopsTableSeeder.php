<?php

use Illuminate\Database\Seeder;
use App\stop;

class StopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stops')->delete();
        //
        $stops = array(
            array(
                'name' => 'Jakarta',
                'description' => 'Aangekomen in Jakarte in Six Degrees Hostel!',
                'trip_id' => 1,
                'location_id' => 1,
                'arrival_time' => '2017-09-04 14:25:20'
            ),
            array(
                'name' => 'Jogjakarta',
                'description' => 'Na een kleine vlucht eindelijk in Jogjakarta klaar voor een niew avontuur',
                'trip_id' => 1,
                'location_id' => 2,
                'arrival_time' => '2017-09-06 12:00:15'
            ),
            array(
                'name' => 'Borbodur',
                'trip_id' => 1,
                'location_id' => 3,
                'arrival_time' => '2017-09-07 14:00:15'
            ),
            array(
                'name' => 'Mnt. Bromo',
                'description' => 'Vulkaantje checken...',
                'trip_id' => 1,
                'location_id' => 4,
                'arrival_time' => '2017-09-10 04:10:25'
            ),
        );
        foreach ($stops as $stop) {
            Stop::create($stop);
        }
    }
}
