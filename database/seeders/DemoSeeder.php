<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DemoPart1Seeder::class);
        $this->call(DemoPart2Seeder::class);
        $this->call(DemoPart3Seeder::class);
        $this->call(DemoPart4Seeder::class);
    }
}
