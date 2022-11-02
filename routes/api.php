<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PendaftaranController;
use App\Http\Controllers\API\BpjsController;
use App\Http\Controllers\api\ErmController;
use App\Http\Controllers\api\SatuSehatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware' => 'auth:sanctum'],function(){
    Route::post('/caripasien',[PendaftaranController::class,'caripasien']);
    Route::post('/caridokter',[PendaftaranController::class,'caridokter']);
    Route::post('/cariunitrajal',[PendaftaranController::class,'cariunitrajal']);
    Route::post('/riwayatkunjungan_rs',[PendaftaranController::class,'riwayatkunjungan_rs']);
    //Erm
    Route::post('/cari_pasien_poli',[ErmController::class,'cari_pasien_poli']);
    Route::post('/cari_pasien_poli_bydok',[ErmController::class,'cari_pasien_poli_bydok']);
    Route::post('/cari_layanan',[ErmController::class,'cari_layanan']);
    Route::post('/simpanlayanan_header',[ErmController::class,'simpanlayanan_header']);
    Route::post('/simpanlayanan_detail',[ErmController::class,'simpanlayanan_detail']);
    Route::post('/tampil_cppt',[ErmController::class,'tampil_cppt']);

    //SATU INDONESIA SEHAT
    Route::post('/authss',[SatuSehatController::class,'login']);
    Route::post('/search_patient_nik',[SatuSehatController::class,'search_patient_nik']);
    Route::post('/search_practitioner_nik',[SatuSehatController::class,'search_practitioner_nik']);
    Route::post('/Organization_by_name',[SatuSehatController::class,'Organization_by_name']);
    Route::post('/Location_by_name',[SatuSehatController::class,'Location_by_name']);
    Route::post('/Encounter_rajal',[SatuSehatController::class,'Encounter_rajal']);

    //BPJS
    Route::post('/infopeserta_kartu',[BpjsController::class,'infopeserta_kartu']);
}); 
Route::post('/login',[AuthController::class,'login']);