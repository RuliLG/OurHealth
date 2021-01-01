<?php

namespace App\Console\Commands;

use App\Http\Services\RegionService;
use App\Models\Region;
use Illuminate\Console\Command;

class PopulateRegions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:regions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloads and imports the regions of each country';

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
        echo 'Downloading regions...' . PHP_EOL;
        $raw = file_get_contents('https://gist.githubusercontent.com/mindplay-dk/4755200/raw/2930f0c786a32c873ddcd7d51defbf6ca0846600/cdh_state_codes.txt');
        $regions = array_slice(explode("\n", $raw), 1);
        $data = [];
        $allowedTypes = array_flip(['province']);
        $existingRegions = (new RegionService)->getAll()
            ->groupBy('country_iso_code')
            ->map(function ($records) {
                return $records->map(function ($record) {
                    return $record->name;
                });
            })
            ->toArray();
        foreach ($regions as $region) {
            $region = str_getcsv($region, "\t");
            // Check if the region has an iso code
            if (empty($region[8])) {
                continue;
            }

            // Check region type against allowed list.
            // Currently we only support "province", as it's the Spanish type of region we want to use
            $type = mb_strtolower($region[3]);
            if (!isset($allowedTypes[$type])) {
                continue;
            }

            $isoCode = mb_strtolower(trim($region[8]));
            $name = trim($region[2]);
            if (isset($existingRegions[$isoCode]) && in_array($name, $existingRegions[$isoCode])) {
                continue;
            }

            $data[] = [
                'country_iso_code' => $isoCode,
                'name' => $name
            ];
        }

        if (!empty($data)) {
            Region::insert($data);
            echo '✅ Created ' . count($data) . (count($data) === 1 ? ' region' : ' regions') . PHP_EOL;
        }
        return 0;
    }
}
