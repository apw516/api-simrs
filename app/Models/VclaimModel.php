<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class VclaimModel extends Model
{
    // public $baseUrl = 'https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev/';
    public $baseUrl = 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/';
    public static function signature()
    {
        $cons_id =  env('CONS_ID');
        $secretKey = env('SECRET_KEY');
        $userkey = env('USER_KEY');

        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cons_id . "&" . $tStamp, $secretKey, true);
        $encodedSignature = base64_encode($signature);
        $response = array(
            'user_key' => $userkey,
            'x-cons-id' => $cons_id,
            'x-timestamp' => $tStamp,
            'x-signature' => $encodedSignature,
            'decrypt_key' => $cons_id . $secretKey . $tStamp,
        );
        return $response;
    }
    public static function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $output = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $output;
    }
    public function get_peserta_noka($noka, $tanggal)
    {
        $client = new Client();
        $url = $this->baseUrl . "Peserta/nokartu/" . $noka . "/tglSEP/" . $tanggal;
        $signature = $this->signature();       
        // dd($signature); 
        $response = $client->request('GET', $url, [
            'headers' => $signature,
            'allow_redirects' => true,
            'timeout' => 5
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function get_peserta_nik($nik, $tanggal)
    {
        $client = new Client();
        $url = $this->baseUrl . "Peserta/nik/" . $nik . "/tglSEP/" . $tanggal;
        $signature = $this->signature();       
        // dd($signature); 
        $response = $client->request('GET', $url, [
            'headers' => $signature,
            'allow_redirects' => true,
            'timeout' => 10
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_diagnosa($diag)
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/diagnosa/" . $diag;
        $signature = $this->signature();       
        // dd($signature); 
        $response = $client->request('GET', $url, [
            'headers' => $signature,
            'allow_redirects' => true,
            'timeout' => 10
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_poli($poli)
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/poli/" . $poli;
        $signature = $this->signature();       
        // dd($signature); 
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_faskes($nama,$jenis)
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/faskes/" . $nama .'/'. $jenis;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_dpjp($jnspelayanan,$tglpelayanan,$kodespesialis)
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/dokter/pelayanan/" . $jnspelayanan .'/tglPelayanan/'. $tglpelayanan.'/Spesialis/'.$kodespesialis;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_propinsi()
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/propinsi";
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_kabupaten($kodepropinsi)
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/kabupaten/propinsi/".$kodepropinsi;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_kecamatan($kodekabupaten)
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/kecamatan/kabupaten/".$kodekabupaten;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_diagnosa_prb()
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/diagnosaprb";
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_obat_generik_prb($namaobat)
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/obatprb/".$namaobat;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_procedure_prb($namaproce)
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/procedure/".$namaproce;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_kelas_rawat()
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/kelasrawat";
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_dokter($namadokter)
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/dokter/".$namadokter;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_spesialistik()
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/spesialistik";
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_ruang_rawat()
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/ruangrawat";
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_cara_keluar()
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/carakeluar";
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_pasca_pulang()
    {
        $client = new Client();
        $url = $this->baseUrl . "referensi/pascapulang";
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function list_sarana($kode,$tgl)
    {
        $client = new Client();
        $url = $this->baseUrl . "Rujukan/ListSpesialistik/PPKRujukan/".$kode."/TglRujukan/".$tgl;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function Datapoli($jenis,$nomor,$tgl)
    {
        $client = new Client();
        $url = $this->baseUrl . "RencanaKontrol/ListSpesialistik/JnsKontrol/".$jenis."/nomor/".$nomor."/TglRencanaKontrol/".$tgl;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function Datadokter($jenis,$kodepoli,$tgl)
    {
        $client = new Client();
        $url = $this->baseUrl . "RencanaKontrol/JadwalPraktekDokter/JnsKontrol/".$jenis."/KdPoli/".$kodepoli."/TglRencanaKontrol/".$tgl;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function insertsep2($data_sep)
    {
        $client = new Client();
        $data = json_encode($data_sep);
        $url = $this->baseUrl . "SEP/2.0/insert";
        $signature = $this->signature();       
        try{
            $response = $client->request('POST', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20 
                ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        }catch(ClientException){
            return 'RTO';
        }          
    }
    public function InsertSPRI($datasurat)
    {
        $client = new Client();
        $data = json_encode($datasurat);
        $url = $this->baseUrl . "RencanaKontrol/InsertSPRI";
        $signature = $this->signature();       
        $response = $client->request('POST', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;  
    }
    public function InsertPRB($datasurat)
    {
        $client = new Client();
        $data = json_encode($datasurat);
        $url = $this->baseUrl . "PRB/insert";
        $signature = $this->signature();       
        $response = $client->request('POST', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;  
    }
    public function UpdateSPRI($datasurat)
    {
        $client = new Client();
        $data = json_encode($datasurat);
        $url = $this->baseUrl . "RencanaKontrol/UpdateSPRI";
        $signature = $this->signature();       
        $response = $client->request('PUT', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;  
    }
    public function InserRencanakontrol($datasurat)
    {
        $client = new Client();
        $data = json_encode($datasurat);
        $url = $this->baseUrl . "RencanaKontrol/insert";
        $signature = $this->signature();       
        $response = $client->request('POST', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;  
    }
    public function updateRencanakontrol($datasurat)
    {
        $client = new Client();
        $data = json_encode($datasurat);
        $url = $this->baseUrl . "RencanaKontrol/Update";
        $signature = $this->signature();       
        $response = $client->request('PUT', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;  
    }
    public function ListRencanaKontrol_bycard($bulan,$tahun,$nomorkartu,$filter)
    {
        $client = new Client();
        $url = $this->baseUrl . "RencanaKontrol/ListRencanaKontrol/Bulan/".$bulan."/Tahun/".$tahun."/Nokartu/".$nomorkartu."/filter/".$filter;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function ListRencanaKontrol_rs($awal,$akhir,$filter)
    {
        $client = new Client();
        $url = $this->baseUrl . "RencanaKontrol/ListRencanaKontrol/tglAwal/".$awal."/tglAkhir/".$akhir."/filter/".$filter;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function Listrujukan_bycard_faskes1($nomorkartu)
    {
        $client = new Client();
        $url = $this->baseUrl . "Rujukan/List/Peserta/".$nomorkartu;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function Listrujukan_bycard_faskes2($nomorkartu)
    {
        $client = new Client();
        $url = $this->baseUrl . "Rujukan/RS/List/Peserta/".$nomorkartu;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function get_data_klaim($tgl,$jenis,$status)
    {
        $client = new Client();
        $url = $this->baseUrl . "Monitoring/Klaim/Tanggal/".$tgl."/JnsPelayanan/".$jenis."/Status/".$status;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function get_data_klaim_jr($jenis,$awal,$akhir)
    {
        $client = new Client();
        $url = $this->baseUrl . "monitoring/JasaRaharja/JnsPelayanan/".$jenis."/tglMulai/".$awal."/tglAkhir/".$akhir;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function get_data_kunjungan($tgl,$jenis)
    {
        $client = new Client();
        $url = $this->baseUrl . "Monitoring/Kunjungan/Tanggal/".$tgl."/JnsPelayanan/".$jenis;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function get_data_kunjungan_ranap($tgl)
    {
        $client = new Client();
        $url = $this->baseUrl . "Monitoring/Kunjungan/Tanggal/".$tgl."/JnsPelayanan/1";
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function get_data_kunjungan_rajal($tgl)
    {
        $client = new Client();
        $url = $this->baseUrl . "Monitoring/Kunjungan/Tanggal/".$tgl."/JnsPelayanan/2";
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function get_data_kunjungan_peserta($nomorkartu,$tglawal,$tglakhir)
    {
        $client = new Client();
        $url = $this->baseUrl . "monitoring/HistoriPelayanan/NoKartu/".$nomorkartu."/tglMulai/".$tglawal."/tglAkhir/".$tglakhir;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function get_data_list_tanggal_pulang($bulan,$tahun)
    {
        $client = new Client();
        $url = $this->baseUrl . "Sep/updtglplg/list/bulan/".$bulan."/tahun/".$tahun."/";
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function carisep($nosep)
    {
        $client = new Client();
        $url = $this->baseUrl . "SEP/".$nosep;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function carisep_kontrol($nosep)
    {
        $client = new Client();
        $url = $this->baseUrl . "RencanaKontrol/nosep/".$nosep;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function cariprb($prb,$nosep)
    {
        $client = new Client();
        $url = $this->baseUrl . "prb/".$prb."/nosep/".$nosep;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function cariprb_date($tglawal,$tglakhir)
    {
        $client = new Client();
        $url = $this->baseUrl . "prb/tglMulai/".$tglawal."/tglAkhir/".$tglakhir;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function hapussep($datasurat)
    {
        $client = new Client();
        $data = json_encode($datasurat);
        $url = $this->baseUrl . "SEP/2.0/delete";
        $signature = $this->signature();       
        $response = $client->request('DELETE', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response; 
    }
    public function updatetglpulang($datasurat)
    {
        $client = new Client();
        $data = json_encode($datasurat);
        $url = $this->baseUrl . "SEP/2.0/updtglplg";
        $signature = $this->signature();       
        $response = $client->request('PUT', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response; 
    }
    public function updatesep($datasurat)
    {
        $client = new Client();
        $data = json_encode($datasurat);
        $url = $this->baseUrl . "SEP/2.0/update";
        $signature = $this->signature();       
        $response = $client->request('PUT', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response; 
    }
    public function pengajuansep($datasurat)
    {
        $client = new Client();
        $data = json_encode($datasurat);
        $url = $this->baseUrl . "Sep/pengajuanSEP";
        $signature = $this->signature();       
        $response = $client->request('POST', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response; 
    }
    public function aprrovalpengajuan($datasurat)
    {
        $client = new Client();
        $data = json_encode($datasurat);
        $url = $this->baseUrl . "Sep/aprovalSEP";
        $signature = $this->signature();       
        $response = $client->request('POST', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response; 
    }
    public function carisep_internal($nosep)
    {
        $client = new Client();
        $url = $this->baseUrl . "SEP/Internal/".$nosep;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function get_data_finger($tgl)
    {
        $client = new Client();
        $url = $this->baseUrl . "SEP/FingerPrint/List/Peserta/TglPelayanan/".$tgl;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function ListRencanaKontrol_peserta($bulan,$tahun,$noka,$filter)
    {
        $client = new Client();
        $url = $this->baseUrl . "RencanaKontrol/ListRencanaKontrol/Bulan/".$bulan."/Tahun/".$tahun."/Nokartu/".$noka."/filter/".$filter;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function hapus_suratkontrol($data)
    {
        $client = new Client();
        $data = json_encode($data);
        $url = $this->baseUrl . "RencanaKontrol/Delete";
        $signature = $this->signature();       
        $response = $client->request('DELETE', $url, [
            'headers' => $signature,
            'body' => $data
            ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response; 
    }
    public function jumlahseprujukan($faskes,$rujukan)
    {
        $client = new Client();
        $url = $this->baseUrl . "Rujukan/JumlahSEP/".$faskes."/".$rujukan;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function carirujukan_byno($rujukan)
    {
        $client = new Client();
        $url = $this->baseUrl . "Rujukan/".$rujukan;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function carirujukan_byno_rs($rujukan)
    {
        $client = new Client();
        $url = $this->baseUrl . "Rujukan/RS/".$rujukan;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function detailrujukan_keluar($rujukan)
    {
        $client = new Client();
        $url = $this->baseUrl . "Rujukan/Keluar/".$rujukan;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function carirujukan_keluar($tglawal,$tglakhir)
    {
        $client = new Client();
        $url = $this->baseUrl . "Rujukan/Keluar/List/tglMulai/".$tglawal."/tglAkhir/".$tglakhir;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function carirujukan_khusus($bulan,$tahun)
    {
        $client = new Client();
        $url = $this->baseUrl . "Rujukan/Khusus/List/Bulan/".$bulan."/Tahun/".$tahun;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function insertrujukan($data_rujukan)
    {
        $client = new Client();
        $data = json_encode($data_rujukan);
        $url = $this->baseUrl . "Rujukan/2.0/insert";
        $signature = $this->signature();       
        try{
            $response = $client->request('POST', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20 
                ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        }catch(ClientException){
            return 'RTO';
        } 
    }
    public function insertrujukankhusus($data_rujukan)
    {
        $client = new Client();
        $data = json_encode($data_rujukan);
        $url = $this->baseUrl . "Rujukan/Khusus/insert";
        $signature = $this->signature();       
        try{
            $response = $client->request('POST', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20 
                ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        }catch(ClientException){
            return 'RTO';
        } 
    }
    public function updaterujukan($data_rujukan)
    {
        $client = new Client();
        $data = json_encode($data_rujukan);
        $url = $this->baseUrl . "Rujukan/2.0/Update";
        $signature = $this->signature();       
        try{
            $response = $client->request('PUT', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20 
                ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        }catch(ClientException){
            return 'RTO';
        } 
    }
    public function deleterujukan($data_rujukan)
    {
        $client = new Client();
        $data = json_encode($data_rujukan);
        $url = $this->baseUrl . "Rujukan/delete";
        $signature = $this->signature();       
        try{
            $response = $client->request('DELETE', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20 
                ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        }catch(ClientException){
            return 'RTO';
        } 
    }
    public function carisuratkontrol($nomor){
        $client = new Client();
        $url = $this->baseUrl . "RencanaKontrol/noSuratKontrol/".$nomor;
        $signature = $this->signature();       
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function ambilantrian($dataantrian)
    {
        $client = new Client();
        $data = json_encode($dataantrian);
        $url = "https://app.rsudwaled.id/api/ambilantrean";
        $signature = $this->signature();       
        // try{
            $response = $client->request('POST', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20 
                ]);
            // $response = json_decode($response->getBody());
            // if ($response->metaData->code == 200) {
            //     $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            //     $response->response = json_decode($decrypt);
            // }
            return $response;
        // }catch(ClientException){
        //     return 'RTO';
        // } 
    }
}
