<?php

namespace Database\Seeders;
use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        Location::create(['name' => 'Central Nairobi', 'subcounty_id' => 1]);
        Location::create(['name' => 'West Nairobi', 'subcounty_id' => 1]);
    }
}
