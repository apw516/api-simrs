<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\mt_unit;
use App\Models\ts_layanan_detail;
use App\Models\ts_layanan_header;

class ErmController extends Controller
{
    public function cari_pasien_poli(Request $request)
    {
        try {
            $data_pasien = DB::connection('mysql2')->select('select a.kode_kunjungan,fc_nama_px(a.no_rm) as nama,a.no_rm,fc_umur(a.no_rm) as umur, fc_alamat4(a.no_rm) as alamat , fc_nama_unit1(a.kode_unit) as unit,a.tgl_masuk, a.kelas, a.counter, b.kode_kunjungan as kj, fc_nama_unit1(a.ref_unit) as asalunit from ts_kunjungan a left outer join erm_assesmen_keperawatan_rajal b on b.kode_kunjungan = a.kode_kunjungan where a.kode_unit = ? and a.status_kunjungan = ? and DATE(a.tgl_masuk ) = ?', [$request->kode_unit, 1, $request->tanggal_masuk]);
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
    public function cari_pasien_poli_bydok(Request $request)
    {
        try {
            $data_pasien = DB::connection('mysql2')->select('select a.kode_kunjungan,fc_nama_px(a.no_rm) as nama,a.no_rm,fc_umur(a.no_rm) as umur,fc_alamat4(a.no_rm) as alamat , fc_nama_unit1(a.kode_unit) as unit,a.tgl_masuk, a.kelas, a.counter,b.ttv_tekanan_darah as tekanan_darah,fc_nama_unit1(a.ref_unit) as asalunit,c.kode_kunjungan as kjdok,b.kode_kunjungan as kjper FROM ts_kunjungan a LEFT OUTER JOIN erm_assesmen_keperawatan_rajal b ON b.kode_kunjungan= a.kode_kunjungan LEFT OUTER JOIN erm_assesmen_awal_medis_rajal c ON c.kode_kunjungan = a.kode_kunjungan WHERE a.kode_unit=? AND DATE(a.tgl_masuk)= ? AND a.status_kunjungan= ?', [$request->kode_unit, $request->tgl_masuk, 1]);
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
    public function cari_layanan(Request $request)
    {
        try {
            $layanan = DB::connection('mysql2')->select("CALL SP_PANGGIL_TARIF_TINDAKAN_RS('$request->kelas','$request->layanan','$request->unit')");
            return response()->json(
                [
                    'message' => 'Sukses',
                    'data' => $layanan
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
    public function tampil_cppt(Request $request)
    {
        try {
            $data_pasien = DB::connection('mysql2')->select('SELECT *,b.kode_kunjungan AS kunjungan_2 ,a.sumber_data AS sumber_data_askep,b.sumber_data AS sumber_data_asmed, b.`keluhan_utama`AS keluhan_utamadokter, fc_nama_dpjp(b.dpjp) as nama_dokter, a.`keluhan_utama`AS keluhan_utamaperawat FROM `erm_assesmen_keperawatan_rajal` a LEFT OUTER JOIN erm_assesmen_awal_medis_rajal b ON b.no_rm = a.no_rm WHERE a.no_rm = ?', [$request->nomorrm]);
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
    public function simpanlayanan_header(Request $request)
    {
        try {
            $kunjungan = DB::connection('mysql2')->select('SELECT * from ts_kunjungan where kode_kunjungan = ?', [$request->kodekunjungan]);
            // $penjamin = $kunjungan[0]->kode_penjamin;
            $unit = mt_unit::where('kode_unit', '=', "$request->kode_unit")->get();
            $kode_layanan_header = $this->createLayananheader($unit[0]['prefix_unit']);
            $data_layanan_header = [
                'kode_layanan_header' => $kode_layanan_header,
                'tgl_entry' =>   $request->tgl_masuk,
                'kode_kunjungan' => $request->kodekunjungan,
                'kode_unit' => $request->kode_unit,
                'unit_pengirim' => $request->unit_pengirim,
                'dokter_pengirim' => $request->dokter_pengirim,
                'kode_tipe_transaksi' => $request->tipe_transaksi,
                'pic' => $request->user,
                'status_layanan' => '3',
                'status_retur' => 'OPN',
                'status_pembayaran' => 'OPN'
            ]; //data yg diinsert ke ts_layanan_header
            //simpan ke layanan header
            $ts_layanan_header = ts_layanan_header::create($data_layanan_header);
            return response()->json(
                [
                    'message' => 'Sukses',
                    'data' => $ts_layanan_header
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
    public function simpanlayanan_detail(Request $request)
    {
        try {
            $kunjungan = DB::connection('mysql2')->select('SELECT * from ts_kunjungan where kode_kunjungan = ?', [$request->kodekunjungan]);
            $penjamin = $kunjungan[0]->kode_penjamin;
            $id_detail = $this->createLayanandetail();
            if ($penjamin == 'P01') {
                $save_detail = [
                    'id_layanan_detail' => $id_detail,
                    'kode_layanan_header' => $request->kode_layanan_header,
                    'kode_tarif_detail' => $request->kodelayanan,
                    'total_tarif' => $request->tarif,
                    'jumlah_layanan' => $request->qty,
                    'diskon_layanan' => $request->diskon,
                    'total_layanan' =>  $request->tarif * $request->qty,
                    'grantotal_layanan' =>  $request->tarif * $request->qty,
                    'status_layanan_detail' => 'OPN',
                    'kode_dokter1' => $request->dokterpemeriksa,
                    'tgl_layanan_detail' => $request->tgl_masuk,
                    'tagihan_pribadi' =>  $request->tarif * $request->qty,
                    'tgl_layanan_detail_2' => $request->tgl_masuk,
                    'row_id_header' => $request->row_id_header
                ];                
                $header = DB::connection('mysql2')->select('SELECT * from ts_layanan_header_order where id = ?', [$request->row_id_header]);
                $grand_total_tarif = $header[0]->total_layanan + $request->tarif * $request->qty;
                ts_layanan_header::where('id', $request->row_id_header)->update(['total_layanan' => $grand_total_tarif, 'tagihan_pribadi' => $grand_total_tarif]);
            } else {
                $save_detail = [
                    'id_layanan_detail' => $id_detail,
                    'kode_layanan_header' => $request->kode_layanan_header,
                    'kode_tarif_detail' => $request->kodelayanan,
                    'total_tarif' => $request->tarif,
                    'jumlah_layanan' => $request->qty,
                    'diskon_layanan' => $request->diskon,
                    'total_layanan' =>  $request->tarif * $request->qty,
                    'grantotal_layanan' =>  $request->tarif * $request->qty,
                    'status_layanan_detail' => 'OPN',
                    'kode_dokter1' => $request->dokterpemeriksa,
                    'tgl_layanan_detail' => $request->tgl_masuk,
                    'tagihan_penjamin' =>  $request->tarif * $request->qty,
                    'tgl_layanan_detail_2' => $request->tgl_masuk,
                    'row_id_header' => $request->row_id_header
                ];
                $header = DB::connection('mysql2')->select('SELECT * from ts_layanan_header_order where id = ?', [$request->row_id_header]);
                $grand_total_tarif = $header[0]->total_layanan + $request->tarif * $request->qty;
                ts_layanan_header::where('id', $request->row_id_header)->update(['total_layanan' => $grand_total_tarif, 'tagihan_penjamin' => $grand_total_tarif]);
            }
            $ts_layanan_header = ts_layanan_detail::create($save_detail);            
            return response()->json(
                [
                    'message' => 'Sukses',
                    'data' => $save_detail
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
    public function createLayananheader($pref)
    {
        $q = DB::connection('mysql2')->select('SELECT id,kode_layanan_header,RIGHT(kode_layanan_header,6) AS kd_max  FROM ts_layanan_header_order 
        WHERE DATE(tgl_entry) = CURDATE()
        ORDER BY id DESC
        LIMIT 1');
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%06s", $tmp);
            }
        } else {
            $kd = "000001";
        }
        date_default_timezone_set('Asia/Jakarta');
        return $pref . date('ymd') . $kd;
    }
    public function createLayanandetail()
    {
        $q = DB::connection('mysql2')->select('SELECT id,id_layanan_detail,RIGHT(id_layanan_detail,6) AS kd_max  FROM ts_layanan_detail_order 
        WHERE DATE(tgl_layanan_detail) = CURDATE()
        ORDER BY id DESC
        LIMIT 1');
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%06s", $tmp);
            }
        } else {
            $kd = "000001";
        }
        date_default_timezone_set('Asia/Jakarta');
        return 'DET' . date('ymd') . $kd;
    }
}
