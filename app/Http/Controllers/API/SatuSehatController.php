<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SatuSehatModel;

class SatuSehatController extends Controller
{
    public function login()
    {
        $SS = new SatuSehatModel();
        $data = $SS->get_token();
        dd($data);
    }
    public function search_patient_nik(Request $request)
    {
        $SS = new SatuSehatModel();
        $data = $SS->patient_search_nik($request->nik);
        dd($data);
    }
    public function search_practitioner_nik(Request $request)
    {
        $SS = new SatuSehatModel();
        $data = $SS->search_practitioner_nik($request->nik);
        dd($data);
    }
    public function Organization_by_name(Request $request)
    {
        $SS = new SatuSehatModel();
        $data = $SS->Organization_by_name($request->nama);
        dd($data);
    }
    public function Location_by_name(Request $request)
    {
        $SS = new SatuSehatModel();
        $data = $SS->Location_by_name($request->nama);
        dd($data);
    }
    public function Encounter_rajal(Request $request)
    {
        $SS = new SatuSehatModel();
        // $data = $SS->Encounter_rajal($data);
        // dd($data);
    }    
}
