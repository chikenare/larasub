<?php

namespace Err0r\Larasub\Commands;

use Err0r\Larasub\Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;

class LarasubSeed extends Command
{
    public $signature = 'larasub:seed';

    public $description = 'Seed the database with the necessary data';

    public function handle(): int
    {
        $this->call('db:seed', ['--class' => DatabaseSeeder::class]);

        return self::SUCCESS;
    }
}
