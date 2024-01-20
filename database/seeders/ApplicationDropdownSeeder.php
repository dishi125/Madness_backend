<?php

namespace Database\Seeders;

use App\Models\ApplicationDropdown;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationDropdownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('application_dropdowns')->truncate();

        ApplicationDropdown::create([
            'title' => 'None',
        ]);

        ApplicationDropdown::create([
            'title' => 'Bank Details',
        ]);

        ApplicationDropdown::create([
            'title' => 'Based on Price(e.g Under 99)',
        ]);

        ApplicationDropdown::create([
            'title' => 'Best Selling Product',
        ]);

        ApplicationDropdown::create([
            'title' => 'Catalog',
        ]);

        ApplicationDropdown::create([
            'title' => 'Catalogs in Wishlist',
        ]);

        ApplicationDropdown::create([
            'title' => 'Category',
        ]);

        ApplicationDropdown::create([
            'title' => 'Edit Profile',
        ]);

        ApplicationDropdown::create([
            'title' => 'Most Shared Catalogs',
        ]);

        ApplicationDropdown::create([
            'title' => 'New Arrival',
        ]);

        ApplicationDropdown::create([
            'title' => 'Refer &  Earn',
        ]);

        ApplicationDropdown::create([
            'title' => 'Support',
        ]);

        ApplicationDropdown::create([
            'title' => 'Top Rated Catalogs',
        ]);

        ApplicationDropdown::create([
            'title' => 'Url',
        ]);
    }
}
