<?php

namespace App\Jobs;

use App\Models\PriceUpload;
use Illuminate\Bus\Queueable;
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
    private $uuid;
    private $fileName;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $uuid, string $fileName)
    {
        $this->uuid = $uuid;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $upload = PriceUpload::findOrFail($this->uuid);
        $upload->status = 'processing';
        $upload->save();

        $content = fopen(Storage::path($this->fileName), 'r');
        $header = true;
        $prices = [];

        while (!feof($content)) {
            $line = fgets($content);
            if ($header) {
                $header = false;
                continue;
            }
            $price =  $this->processLine($line, $upload->client_id);
            if (!is_null($price)) {
                $prices[] = $price;
            }
            if (sizeof($prices) >= 1000) {
                $this->savePrices($prices);
                $prices = [];
            }
        }
        fclose($content);
        $this->savePrices($prices);
        $upload->status = 'done';
        $upload->save();
    }

    public function failed() {
        $upload = PriceUpload::findOrFail($this->uuid);
        $upload->status = 'fail';
        $upload->save();

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

    public function processLine(string $line, int $client_id)
    {
        $fields = explode(';', $line);
        if (sizeof($fields) < 5) {
            return null;
        }
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
