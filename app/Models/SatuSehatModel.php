<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class SatuSehatModel extends Model
{
    public $baseUrl = 'https://api-satusehat-dev.dto.kemkes.go.id/fhir-r4/v1';
    public function get_token()
    {
        $AUTH = 'https://api-satusehat-dev.dto.kemkes.go.id/oauth2/v1/accesstoken?grant_type=client_credentials';
        $client = new Client();
        $data = [
            'client_id' => 'zbL4jARVmZatKrhaSwz8ikHZF5EkEbwaML6jt2ppGxAbKIAT',
            'client_secret' => 'NnzHRbEIPbOVHsIOA7DKJtRPczJIUX2cB3h7YXZjVHaXmubez8EvRTBqyjsbj0CZ'
        ];
        $response = $client->request('POST', $AUTH, [
            'form_params' => $data,
            'allow_redirects' => true,
            'timeout' => 20 
            ]);
        $response = json_decode($response->getBody());
        return $response;
    }
    public function signature()
    {
        $get_token = $this->get_token();
        $response = array(
            'authorization' => 'Bearer '.$get_token->access_token
        );
        return $response;
    }
    public function patient_search_nik($nik){
        $client = new Client();
        $signature = $this->signature();   
        $url = $this->baseUrl . "/Patient?identifier=https://fhir.kemkes.go.id/id/nik|".$nik;
        // $token = $this->signature();    
        $response = $client->request('GET', $url, [
            'headers' => $signature,
            'allow_redirects' => true,
            'timeout' => 10
        ]);   
        $response = json_decode($response->getBody());
        return $response;
    }    
    public function search_practitioner_nik($nik){
        $client = new Client();
        $signature = $this->signature();   
        $url = $this->baseUrl . "/Practitioner?identifier=https://fhir.kemkes.go.id/id/nik|".$nik;
        // $token = $this->signature();    
        $response = $client->request('GET', $url, [
            'headers' => $signature,
            'allow_redirects' => true,
            'timeout' => 10
        ]);   
        $response = json_decode($response->getBody());
        return $response;
    }    
    public function Organization_by_name($name){
        $client = new Client();
        $signature = $this->signature();   
        $url = $this->baseUrl . "/Organization?name=".$name;
        // $token = $this->signature();    
        $response = $client->request('GET', $url, [
            'headers' => $signature,
            'allow_redirects' => true,
            'timeout' => 10
        ]);   
        $response = json_decode($response->getBody());
        return $response;
    }    
    public function Location_by_name($name){
        $client = new Client();
        $signature = $this->signature();   
        $url = $this->baseUrl . "/Location?name=".$name;
        // $token = $this->signature();    
        $response = $client->request('GET', $url, [
            'headers' => $signature,
            'allow_redirects' => true,
            'timeout' => 10
        ]);   
        $response = json_decode($response->getBody());
        return $response;
    }    
    public function Encounter_rajal($data){
        $client = new Client();
        $data = json_encode($data);
        dd($data);
        $signature = $this->signature();   
        $url = $this->baseUrl . "/Encounter";     
            $response = $client->request('POST', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20 
                ]);
            $response = json_decode($response->getBody());    
    }    
}
