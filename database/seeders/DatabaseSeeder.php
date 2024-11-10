<?php

namespace Err0r\Larasub\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SubscriptionStatusSeeder::class,
        ]);
    }
}
