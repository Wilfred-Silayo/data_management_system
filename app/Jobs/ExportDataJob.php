<?php

namespace App\Jobs;

use App\Exports\DataExport;
use App\Models\Data;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportDataJob implements ShouldQueue
{
    use Queueable, Batchable;
    public $chunkIndex;
    public $chunkSize;
    public $filePath;

    public function __construct($chunkIndex, $chunkSize, $filePath)
    {
        $this->chunkIndex = $chunkIndex;
        $this->chunkSize = $chunkSize;
        $this->filePath = $filePath;
    }

    public function handle()
    {
        try {
            $filePath = storage_path('app/public/' . $this->filePath);

            $spreadsheet = null;

            if (Storage::disk('public')->exists($this->filePath)) {
                $spreadsheet = IOFactory::load($filePath);
            } else {
                $spreadsheet = new Spreadsheet();
                $spreadsheet->setActiveSheetIndex(0);
            }

            $sheet = $spreadsheet->getActiveSheet();
            $lastRow = $sheet->getHighestRow(); 

            $dataChunks = Data::orderBy('id', 'asc')
            ->skip($this->chunkIndex * $this->chunkSize)
            ->take($this->chunkSize)
            ->get();

            foreach ($dataChunks as $index => $row) {
                $rowIndex = $lastRow + $index + 1; 
                $sheet->fromArray($row->toArray(), null, 'A' . $rowIndex);
            }

           
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
