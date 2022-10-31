<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ts_layanan_detail extends Model
{
    use HasFactory;
    protected $table = 'ts_layanan_detail_order';
    protected $connection = 'mysql2';
    protected $guarded = ['id'];
    // public function mt_tarif_detail(){
    //     return $this->hasOne(mt_tarif_detail::class,'kode_tarif_detail','KODE_TARIF_DETAIL');
    // }
}
