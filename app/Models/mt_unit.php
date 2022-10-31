<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mt_unit extends Model
{
    use HasFactory;
    protected $table = 'mt_unit';
    protected $connection = 'mysql2';
    // public function mt_tarif_detail(){
        // $data = array(
            // return  $this->hasOne(mt_tarif_detail::class,'KODE_TARIF_DETAIL','kode_tarif_adm');
            // 'karcis' =>  $this->hasOne(mt_tarif_detail::class,'KODE_TARIF_DETAIL','kode_tarif_karcis')
        // );
        // return $data;
    // }
    // public function mt_tarif_detail2(){
        // $data = array(
            // return  $this->hasOne(mt_tarif_detail::class,'KODE_TARIF_DETAIL','kode_tarif_karcis');
            // 'karcis' =>  $this->hasOne(mt_tarif_detail::class,'KODE_TARIF_DETAIL','kode_tarif_karcis')
        // );
        // return $data;
    // }
    // public function mt_tarif_detail3(){
        // $data = array(
            // return  $this->hasOne(mt_tarif_detail::class,'KODE_TARIF_HEADER','kode_tarif_adm');
            // 'karcis' =>  $this->hasOne(mt_tarif_detail::class,'KODE_TARIF_DETAIL','kode_tarif_karcis')
        // );
        // return $data;
    // }
}
