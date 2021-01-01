<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Populate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the database with the minimum required data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $commands = [
            PopulateCountries::class,
            PopulateRegions::class,
        ];
        foreach ($commands as $command) {
            $command = new $command;
            $command->handle();
        }
        return 0;
    }
}
