<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Jobs\ExportDataJob;
use App\Jobs\ProcessImportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Exception;


class DataController extends Controller
{
    public function index()
    {
        return view('welcome');
    }


    public function exportData()
    {
        try {


            
            // $batch = Bus::batch([new ExportDataJob()])->dispatch();

            // return response()->json([
            //     'success' => true,
            //     'batchId' => $batch->id,
            //     'message' => 'Export process started successfully.',
            // ]);
        } catch (Exception $e) {
         
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function downloadExport()
    {
        $filePath = storage_path('data.xlsx');

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
}
