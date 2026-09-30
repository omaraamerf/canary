<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LocationSeeder::class);
        $this->call(MarketplaceSeeder::class);
        $this->call(BreedSeeder::class);
        $this->call(GuideSeeder::class);
        $this->call(GeneticsGuideSeeder::class);
        $this->call(PermissionSeeder::class);
    }
}
