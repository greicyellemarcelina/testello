<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PriceTableProcessingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 600;
    private $fileName;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $content = fopen(Storage::path($this->fileName), 'r');
        $header = true;
        $prices = [];

        while (!feof($content)) {
            $line = fgets($content);
            if ($header) {
                $header = false;
                continue;
            }
            $price =  $this->processLine($line);
            if (!is_null($price)) {
                $prices[] = $this->processLine($line);
            }
            if (sizeof($prices) >= 1000) {
                $this->savePrices($prices);
                $prices = [];
            }
        }
        fclose($content);
        $this->savePrices($prices);
    }

    public function savePrices(array $prices)
    {
        DB::table('prices')->upsert(
            $prices,
            [
                'client_id',
                'from_postcode',
                'to_postcode',
                'from_weight',
                'to_weight'
            ],
            ['cost']
        );
    }

    public function processLine(string $line)
    {
        $fields = explode(';', $line);
        if (sizeof($fields) < 5) {
            return null;
        }
        $client_id = 1;
        $from_postcode = $fields[0];
        $to_postcode = $fields[1];
        $from_weight = $this->toDecimal($fields[2]);
        $to_weight = $this->toDecimal($fields[3]);
        $cost = $this->toDecimal($fields[4]);

        $price = [
            'client_id' => $client_id,
            'from_postcode' => $from_postcode,
            'to_postcode' => $to_postcode,
            'from_weight' => $from_weight,
            'to_weight' => $to_weight,
            'cost' => $cost
        ];

        return $price;
    }

    public function toDecimal(string $value)
    {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return floatval($value);
    }

}
