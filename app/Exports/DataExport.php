<?php

namespace App\Exports;

use App\Models\Data;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class DataExport implements FromQuery, WithHeadings, ShouldQueue, WithChunkReading
{
    use Exportable, Batchable;

    /**
     * Query the data to be exported.
     */
    public function query()
    {
        return Data::query();
    }

    /**
     * Define the Excel file headings.
     */
    public function headings(): array
    {
        return [
            'cut',
            'color',
            'clarity',
            'carat_weight',
            'cut_quality',
            'lab',
            'symmetry',
            'polish',
            'eye_clean',
            'culet_size',
            'culet_condition',
            'depth_percent',
            'table_percent',
            'meas_length',
            'meas_width',
            'meas_depth',
            'girdle_min',
            'girdle_max',
            'fluor_color',
            'fluor_intensity',
            'fancy_color_dominant_color',
            'fancy_color_secondary_color',
            'fancy_color_overtone',
            'fancy_color_intensity',
            'total_sales_price',
            'Created At', 
            'Updated At'
        ];
    }

    /**
     * Define the chunk size for processing.
     */
    public function chunkSize(): int
    {
        return 1000; 
    }
}
