<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SatuSehatModel;

class SatuSehatController extends Controller
{
    public function login()
    {
        $SS = new SatuSehatModel();
        $data = $SS->get_token();
        return $data->access_token;
    }
}
