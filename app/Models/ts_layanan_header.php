<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ts_layanan_header extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'ts_layanan_header_order';
    protected $guarded = ['id'];
    // public function ts_layanan_detail(){
    //     return $this->hasMany(ts_layanan_detail::class,'row_id_header','id');
    // }
}
