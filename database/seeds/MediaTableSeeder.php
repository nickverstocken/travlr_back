<?php

use Illuminate\Database\Seeder;
use App\Media;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('media')->delete();
        //
        $media = array(
            array(
                'stop_id' => 1,
                'caption' => 'Moe maar voldaan.',
                'image' => 'https://scontent.fbru1-1.fna.fbcdn.net/v/t31.0-8/22042185_10155949160154701_5193880360554682351_o.jpg?oh=f1164008c467d48e7b0e4d9f0be7ee4c&oe=5A8380BC',
                'image_thumb' => 'https://scontent.fbru1-1.fna.fbcdn.net/v/t31.0-8/22042185_10155949160154701_5193880360554682351_o.jpg?oh=f1164008c467d48e7b0e4d9f0be7ee4c&oe=5A8380BC',
            ),
            array(
                'stop_id' => 1,
                'image' => 'https://scontent.fbru1-1.fna.fbcdn.net/v/t1.0-9/22045710_10155949164114701_4680688605292877010_n.jpg?oh=e4db3bdb7f2ec750797a9f7063eb15c7&oe=5A7A563A',
                'image_thumb' => 'https://scontent.fbru1-1.fna.fbcdn.net/v/t1.0-9/22045710_10155949164114701_4680688605292877010_n.jpg?oh=e4db3bdb7f2ec750797a9f7063eb15c7&oe=5A7A563A',
            )
        );
        foreach ($media as $mediaItem) {
            Media::create($mediaItem);
        }
    }
}
