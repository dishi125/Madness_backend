<?php

namespace Database\Seeders;

use App\Models\AdView;
use Illuminate\Database\Seeder;

class AdViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdView::create([
            'view_name' => 'Single Image Ad View',
            'width' => '1000px',
            'height' => '150px',
        ]);

        AdView::create([
            'view_name' => 'Single Image Ad View',
            'width' => '1920px',
            'height' => '400px',
        ]);

        AdView::create([
            'view_name' => 'Single Image Ad View',
            'width' => '1550px',
            'height' => '512px',
        ]);

        AdView::create([
            'view_name' => 'Double Image Ad View',
            'width' => '750px',
            'height' => '512px',
        ]);

        AdView::create([
            'view_name' => 'Double Image Ad View',
            'width' => '549px',
            'height' => '896px',
        ]);

        AdView::create([
            'view_name' => 'Triple Image Ad View',
            'width' => '512px',
            'height' => '512px',
        ]);

        AdView::create([
            'view_name' => 'Four Image Ad View',
            'width' => '250px',
            'height' => '250px',
        ]);
    }
}
