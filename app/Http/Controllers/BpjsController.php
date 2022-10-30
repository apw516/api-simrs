<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VclaimModel;

class BpjsController extends Controller
{
    public function infopeserta_kartu(Request $request){
        try{
            $v = new VclaimModel();
            $data = $v->get_peserta_noka($request->nomorkartu, date('Y-m-d'));
            return response()->json(
                [
                    'message' => 'Sukses',
                    'data' => $data
                ],
                200
            );
        }catch (\Exception $e) {
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
