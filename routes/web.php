<?php

use App\Http\Controllers\JasperController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',  [JasperController::class, 'index'])->name('home');
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');
Route::get('/export-all',  [JasperController::class, 'exportAll'])->name('export-all');
Route::get('/downloaded-information',  [JasperController::class, 'downloadedInformationMSP'])->name('downloaded-information');
Route::get('/folder',  [JasperController::class, 'folder'])->name('folder');
Route::get('/administracion',  [JasperController::class, 'administracion'])->name('administracion');
// Route::post('tests', [JasperController::class, 'tests'])->name('tests');
