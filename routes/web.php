<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SatuSehatController;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/satusehat', [SatuSehatController::class, 'login']);
Route::get('/get_pasien_nik/{nik}', [SatuSehatController::class, 'search_patient_nik']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
