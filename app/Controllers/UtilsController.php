<?php

namespace App\Controllers;

use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\Http\Http;
use Bpjs\Framework\Helpers\Response;
use Bpjs\Framework\Helpers\View;

class UtilsController extends BaseController
{
    // Controller logic here
    // public function getEmployee(Request $request)
    // {
    //     try {
    //         $api = Http::new()->get(env('API_DATA').'action=getSimpleEmp'.'&api_key='.env('API_KEY'));
    //         $result = $api->json();
    //         return Response::json(['status' => 200, 'data' => $result]);
    //     } catch (\Exception $e){
    //         return Response::json(['status' => 500, 'message' => 'Server Error']);
    //     }
    // }

        public function getEmployee(Request $request)
    {
        try {
            // ── DATA HARDCODE SEMENTARA (MP MC 62) ──
            $dataKaryawan = [
                ['nik' => '74522', 'nama' => 'MUHAMMAD RAIHAN', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP MC 62'],
                ['nik' => '36769', 'nama' => "IBNI SAB'AH", 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP MC 62'],
                ['nik' => '74915', 'nama' => 'DEDE ALDI YANA', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP MC 62'],
                ['nik' => '74843', 'nama' => 'MUHAMMAD NUR APRIZAL', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP PENGANTI M RAIHAN'],
                ['nik' => '72574', 'nama' => 'NURUL FAHMI', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP PENGANTI IBNI SABAH'],
                ['nik' => '74850', 'nama' => 'DIKI NURDIANSYAH', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP PENGANTI DEDE ALDI'],
                ['nik' => '72865', 'nama' => 'MOHAMMAD RIZAL FAUZI', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'TEAM LEADER'],
                ['nik' => '72844', 'nama' => 'MUHAMAD IGUN AGUSTIAN', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'TEAM LEADER'],
                ['nik' => '73022', 'nama' => 'MUHAMMAD QUDDUS ULYA', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'TEAM LEADER'],
                ['nik' => '70508', 'nama' => 'AGUS BUDIMAN', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'GROUP LEADER'],
                ['nik' => '72194', 'nama' => 'CAHYADI HARJANTO', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'GROUP LEADER'],
                ['nik' => '71085', 'nama' => 'ABDUL MUTHOLIB', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'GROUP LEADER'],
            ];

            // Bungkus ke dalam struktur yang sama dengan API aslinya
            $result = [
                'data' => $dataKaryawan,
                'rowCount' => count($dataKaryawan)
            ];

            return Response::json(['status' => 200, 'data' => $result]);

        } catch (\Exception $e){
            return Response::json(['status' => 500, 'message' => 'Server Error']);
        }
    }

    public function getAllDept(Request $request)
    {
        try{
            $api = Http::new()->get(env('API_DATA').'action=getAllDept'.'&api_key='.env('API_KEY'));
            $result = $api->json();
            if (is_string($result)) {
                $result = json_decode($result, true) ?? [];
            }

            if (!is_array($result)) {
                $result = [];
            }

            $filtered = array_values(array_filter($result, function ($item) {
                if (!is_array($item)) return false;

                $dept = $item['dept'] ?? '';
                $dept = preg_replace('/[\x00-\x1F\x7F]/u', '', $dept);
                $dept = strtoupper(trim(preg_replace('/\s+/', ' ', $dept)));

                return in_array($dept, ['FACTORY 1', 'FACTORY 2'], true);
            }));
            return $this->json(['status'=>200,'data'=>$filtered],200);
        } catch(\Exception $e){
            return $this->json(['status'=>500,'message'=>'Server Error'.$e->getMessage()],500);
        }
    }
}
