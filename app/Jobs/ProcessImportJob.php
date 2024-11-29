<?php

namespace App\Jobs;

use App\Models\Data;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessImportJob implements ShouldQueue
{
    use Queueable, Batchable;



    /**
     * Create a new job instance.
     */
    public function __construct(public $header, public $data) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->data as $data_) {
            $assoc_data = array_combine($this->header, $data_);
            Data::create($assoc_data);
        }
    }
}
