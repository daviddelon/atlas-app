<?php

namespace Database\Seeders;

use DantSu\OpenStreetMapStaticAPI\Circle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use League\Csv\Statement;
use \DantSu\OpenStreetMapStaticAPI\OpenStreetMap;
use \DantSu\OpenStreetMapStaticAPI\LatLng;
use \DantSu\OpenStreetMapStaticAPI\Polygon;
use \DantSu\OpenStreetMapStaticAPI\Markers;
use Illuminate\Support\Facades\Storage;

class MapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // No table seeded, used only to generate static maps


        $stream = fopen('/var/www/html/tmp/test_reduit.csv', 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setEscape(''); //required in PHP8.4+ to avoid deprecation notices


        $records=$csv->getRecords();


        $i=0;
        foreach ($records as $record) {
            print "$i\n";
            $i++;
            if ($i>1) exit;

            $content=(new OpenStreetMap(new LatLng(43.78, 3.76), 11, 300, 300))
                ->addDraw(
                new Circle(new LatLng($record['latitude'], $record['longitude']),"#4A90E2",5,"#4A90E2")
                )
            ->getImage()->getDataPNG();

            Storage::disk('public')->put($record['taxon_id'].'_map.jpg',  $content);
        }


    }

}
