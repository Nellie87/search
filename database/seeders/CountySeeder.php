<?php

namespace Database\Seeders;
use App\Models\County;
use Illuminate\Database\Seeder;

class CountySeeder extends Seeder
{
    public function run()
    {
        County::create(['name' => 'Nairobi', 'country_id' => 1]);
        County::create(['name' => 'Kisumu', 'country_id' => 1]);
        
    }
}
