<?php

namespace Database\Seeders;
use App\Models\Subcounty;
use Illuminate\Database\Seeder;

class SubcountySeeder extends Seeder
{
    public function run()
    {
        Subcounty::create(['name' => 'Westlands', 'county_id' => 1]);
        Subcounty::create(['name' => 'Kilimani', 'county_id' => 1]);
        Subcounty::create(['name' => 'Ahero', 'county_id' => 2]);
        Subcounty::create(['name' => 'Migingo', 'county_id' => 2]);
    }
}
