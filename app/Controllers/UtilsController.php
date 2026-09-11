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
    public function getEmployee(Request $request)
    {
        try {
            $api = Http::new()->get(env('API_DATA').'action=getSimpleEmp'.'&api_key='.env('API_KEY'));
            $result = $api->json();
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
