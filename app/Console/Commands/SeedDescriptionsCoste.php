<?php

namespace App\Console\Commands;

use Database\Seeders\DescriptionFromCosteSeeder;
use Illuminate\Console\Command;

class SeedDescriptionsCoste extends Command
{
    protected $signature = 'seed:descriptions-coste {sqlitePath : Path to the bdtfx SQLite database file}';
    protected $description = 'Seed descriptions from Coste flora SQLite database';

    public function handle(): int
    {
        $seeder = new DescriptionFromCosteSeeder();
        $seeder->sqlitePath = $this->argument('sqlitePath');
        $seeder->run();
        return 0;
    }
}
