<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleSheetController;

Route::get('/import', [GoogleSheetController::class, 'importView'])->name('import.view');
Route::get('/import/callback', [GoogleSheetController::class, 'importCallback'])->name('import.callback');
Route::get('/import/data', [GoogleSheetController::class, 'importData'])->name('import.data');
Route::post('/import/fetch-and-insert', [GoogleSheetController::class, 'fetchAndInsertData'])->name('fetch.and.insert');





// Route::get('google/connect', [GoogleSheetController::class, 'connectToGoogle'])->name('google.connect');
// Route::get('google/callback', [GoogleSheetController::class, 'handleGoogleCallback'])->name('google.callback');
// Route::get('google/select-sheet', [GoogleSheetController::class, 'selectSheet'])->name('google.select-sheet');
// Route::post('google/fetch-sheet-data', [GoogleSheetController::class, 'fetchSheetData'])->name('google.fetch-sheet-data');

Route::get('/', function () {
    return view('welcome');
});
