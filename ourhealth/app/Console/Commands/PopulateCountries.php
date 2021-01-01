<?php

namespace App\Console\Commands;

use App\Http\Services\CountryService;
use App\Models\Country;
use Illuminate\Console\Command;

class PopulateCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloads a list of every country and fills the database';

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
        echo 'Downloading countries...' . PHP_EOL;
        $raw = file_get_contents('https://pkgstore.datahub.io/core/country-list/data_csv/data/d7c9d7cfb42cb69f4422dec222dbbaa8/data_csv.csv');
        $countries = array_slice(explode("\r\n", $raw), 1);
        $data = [];
        $existingCountries = (new CountryService)->getAll()->keyBy('iso_code');
        foreach ($countries as $country) {
            $country = str_getcsv($country);
            if (!isset($country[1])) {
                continue;
            }

            $country[1] = mb_strtolower($country[1]);
            if (isset($existingCountries[$country[1]])) {
                continue;
            }

            $data[] = [
                'name' => $country[0],
                'iso_code' => $country[1],
            ];
        }

        if (!empty($data)) {
            Country::insert($data);
            echo '✅ Created ' . count($data) . (count($data) === 1 ? ' country' : ' countries') . PHP_EOL;
        }

        return 0;
    }
}
