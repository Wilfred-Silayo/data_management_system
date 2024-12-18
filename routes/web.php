<?php

use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DataController::class,'index'])->name('data.index');
Route::post('/upload-file', [DataController::class, 'uploadFile']);
Route::get('/job-status/{jobId}', [DataController::class, 'checkJobStatus']);
Route::get('/export/xls', [DataController::class, 'exportData']);
Route::get('/export/xls/download', [DataController::class, 'downloadExport']);
Route::get('/fetch-data', [DataController::class, 'fetchData'])->name('fetch.data');
Route::get('/fetch-columns', [DataController::class, 'fetchColumns'])->name('fetch.columns');
