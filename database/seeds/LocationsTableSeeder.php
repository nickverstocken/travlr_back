<?php

use Illuminate\Database\Seeder;
use App\location;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')->delete();
        //
        $locations = array(
            array(
                'country_code' => 62,
                'name' => 'Kota Jakarta Pusat, Indonesia',
                'city' => 'Menteng',
                'province' => 'Jakarta',
                'country' => 'Indonesia',
                'time_zone' => 'Asia/Jakarta',
                'lat' => -6.1935351,
                'lng' => 106.8391351
            ),
            array(
                'country_code' => 62,
                'name' => 'Kota Yogyakarta, Indonesia',
                'city' => 'Mergangsan',
                'province' => 'Yogyakarta',
                'country' => 'Indonesia',
                'time_zone' => 'Asia/Jakarta',
                'lat' => -7.819295,
                'lng' => 110.371296
            ),
            array(
                'country_code' => 62,
                'name' => 'Jl.Badrawati, Borobudur, Magelang, Jawa Tengah, Indonesia',
                'city' => 'Borobudur',
                'province' => 'Yogyakarta',
                'country' => 'Indonesia',
                'time_zone' => 'Asia/Jakarta',
                'lat' => -7.6078738,
                'lng' => 110.2037513
            ),
            array(
                'country_code' => 62,
                'name' => 'Gunung Bromo, Tosari,Pasuruan, Oost-Java,Indonesië',
                'city' => 'Tosari',
                'province' => 'Pasuruan',
                'country' => 'Indonesia',
                'time_zone' => 'Asia/Jakarta',
                'lat' => -7.942494,
                'lng' => 112.953012
            ),
        );
        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
