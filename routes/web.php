<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleSheetsController;

Route::get('import', [GoogleSheetsController::class, 'import'])->name('import');
Route::get('auth', [GoogleSheetsController::class, 'auth'])->name('auth');
Route::get('auth-callback', [GoogleSheetsController::class, 'authCallback'])->name('auth-Callback');
Route::post('fetch-sheet-data', [GoogleSheetsController::class, 'fetchSheetData'])->name('fetchSheetData');



// Route::get('google/connect', [GoogleSheetController::class, 'connectToGoogle'])->name('google.connect');
// Route::get('google/callback', [GoogleSheetController::class, 'handleGoogleCallback'])->name('google.callback');
// Route::get('google/select-sheet', [GoogleSheetController::class, 'selectSheet'])->name('google.select-sheet');
// Route::post('google/fetch-sheet-data', [GoogleSheetController::class, 'fetchSheetData'])->name('google.fetch-sheet-data');

Route::get('/', function () {
    return view('welcome');
});
