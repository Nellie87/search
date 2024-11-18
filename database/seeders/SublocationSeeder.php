<?php

namespace Database\Seeders;
use App\Models\Sublocation;
use Illuminate\Database\Seeder;

class SublocationSeeder extends Seeder
{
    public function run()
    {
        Sublocation::create(['name' => 'Parklands', 'location_id' => 1]);
        Sublocation::create(['name' => 'Kilimani', 'location_id' => 2]);
    }
}
