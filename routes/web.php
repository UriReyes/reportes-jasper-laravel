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

Route::get('/compilar', [JasperController::class, 'compilar'])->name('compilar');
Route::get('/reporte', [JasperController::class, 'reporte'])->name('reporte');
Route::get('/listarParametros', [JasperController::class, 'listarParametros'])->name('listarParametros');
Route::get('/compilarConParametros', [JasperController::class, 'compilarConParametros'])->name('compilarConParametros');
//Generar pdf en automatico
Route::get('/reporteParametros/{customer?}/{zaaid?}', [JasperController::class, 'reporteParametros'])->name('reporteParametros');
