<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    public function caripasien(Request $request)
    {
        try {
            $data_pasien = DB::connection('mysql2')->select("CALL WSP_PANGGIL_DATAPASIEN('$request->nomorrm','$request->nama','$request->alamat','$request->nomorktp','$request->nomorbpjs')");
            $request->user()->currentAccessToken()->delete();
            return response()->json(
                [
                    'message' => 'Sukses',
                    'data' => $data_pasien
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ],
                401
            );
        }
    }
    public function caridokter(Request $request)
    {
        try {
            $result = DB::connection('mysql2')->table('mt_paramedis')->where('nama_paramedis', 'LIKE', '%' . $request['dokter'] . '%')->where('keilmuan', '=', 'dr')->get();
            return response()->json(
                [
                    'message' => 'Sukses',
                    'data' => $result
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ],
                401
            );
        }
    }
    public function cariunitrajal(Request $request)
    {
        try {
            $result = DB::connection('mysql2')->table('mt_unit')->where('nama_unit', 'LIKE', '%' . $request['unit'] . '%')->where('kelas_unit', '=', '1')->get();;
            return response()->json(
                [
                    'message' => 'Sukses',
                    'data' => $result
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ],
                401
            );
        }
    }
    public function riwayatkunjungan_rs(Request $request)
    {
        try {
            $data_kunjungan = DB::connection('mysql2')->select("CALL SP_RIWAYAT_KUNJUNGAN_RS('$request->tgl_awal','$request->tgl_akhir')");
            return response()->json(
                [
                    'message' => 'Sukses',
                    'data' => $data_kunjungan
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ],
                401
            );
        }
    }
}
