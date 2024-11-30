<?php

namespace App\Http\Controllers;

use App\Jobs\ExportDataJob;
use App\Jobs\ProcessImportJob;
use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Exception;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DataController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function fetchData(Request $request)
    {
        try {

            $search = $request->input('search', '');
            $perPage = $request->input('perPage');
            $sortColumn = $request->input('sortColumn','id');
            $sortOrder = $request->input('sortOrder','asc');

            $query = Data::query();

            if ($search !== '') {
                $query = $query->where('cut', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%")
                    ->orWhere('clarity', 'like', "%{$search}%")
                    ->orWhere('carat_weight', 'like', "%{$search}%")
                    ->orWhere('cut_quality', 'like', "%{$search}%")
                    ->orWhere('lab', 'like', "%{$search}%")
                    ->orWhere('symmetry', 'like', "%{$search}%")
                    ->orWhere('polish', 'like', "%{$search}%")
                    ->orWhere('eye_clean', 'like', "%{$search}%")
                    ->orWhere('culet_size', 'like', "%{$search}%")
                    ->orWhere('culet_condition', 'like', "%{$search}%")
                    ->orWhere('depth_percent', 'like', "%{$search}%")
                    ->orWhere('table_percent', 'like', "%{$search}%")
                    ->orWhere('meas_length', 'like', "%{$search}%")
                    ->orWhere('meas_width', 'like', "%{$search}%")
                    ->orWhere('meas_depth', 'like', "%{$search}%")
                    ->orWhere('girdle_min', 'like', "%{$search}%")
                    ->orWhere('girdle_max', 'like', "%{$search}%")
                    ->orWhere('fluor_color', 'like', "%{$search}%")
                    ->orWhere('fluor_intensity', 'like', "%{$search}%")
                    ->orWhere('fancy_color_dominant_color', 'like', "%{$search}%")
                    ->orWhere('fancy_color_secondary_color', 'like', "%{$search}%")
                    ->orWhere('fancy_color_overtone', 'like', "%{$search}%")
                    ->orWhere('fancy_color_intensity', 'like', "%{$search}%")
                    ->orWhere('total_sales_price', 'like', "%{$search}%");
            }
       
                $query = $query->orderBy($sortColumn, $sortOrder);
           

            $data = $query->paginate($perPage);
            $pagination = view('pagination', ['data' => $data])->render();

            return response()->json([
                'data' => $data,
                'pagination' => $pagination,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching data.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Export data 
    public function exportData()
    {
        try {
            $chunkSize = 1000;
            $totalRecords = Data::count();
            $numberOfChunks = ceil($totalRecords / $chunkSize);
            $filePath = "exports/data.xlsx";


            $absolutePath = storage_path('app/public/' . $filePath);
            if (!file_exists($absolutePath)) {
                $spreadsheet = new Spreadsheet();
                $spreadsheet->setActiveSheetIndex(0);
                $writer = new Xlsx($spreadsheet);
                $writer->save($absolutePath);
            }


            $batch = Bus::batch([])->name('Export Data')->dispatch();

            for ($i = 0; $i < $numberOfChunks; $i++) {
                $batch->add(new ExportDataJob($i, $chunkSize, $filePath));
            }

            return response()->json([
                'success' => true,
                'batchId' => $batch->id,
                'message' => 'Export process started successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage(),
            ]);
        }
    }
    // download xlsx
    public function downloadExport()
    {
        $filePath = storage_path('app/public/exports/data.xlsx');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->download($filePath, 'data.xlsx');
    }

    //upload csv file
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv'
        ]);

        try {
            if (request()->has('file')) {

                $header = [];

                $data = file(request()->file);

                //chunking data 
                $chunks = array_chunk($data, 1000);

                //dispach a batch of jobs

                $batch = Bus::batch([])->dispatch();

                foreach ($chunks as $key => $chunk) {
                    $data = array_map('str_getcsv', $chunk);

                    if ($key == 0) {
                        $header = $data[0];
                        unset($data[0]);
                    }


                    $batch->add(new ProcessImportJob($header, $data));
                }
                return response()->json([
                    'success' => true,
                    'jobId' => $batch->id,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Import failed: ' . $e->getMessage(),
            ]);
        }
    }

    // check upload status
    public function checkJobStatus($jobId)
    {
        $batch = Bus::findBatch($jobId);

        if ($batch) {
            return response()->json([
                'finished' => $batch->finished(),
                'progress' => $batch->progress(),
            ]);
        }

        return response()->json(['error' => 'Job not found'], 404);
    }
    // Get the columns of your table dynamically
    public function fetchColumns()
    {
        $columns = Schema::getColumnListing('data');
        return response()->json($columns);
    }
}
