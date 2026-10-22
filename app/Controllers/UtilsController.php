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
            $section = strtoupper((string) ($request->get('section') ?? ''));

            $data = $this->loadKaryawan();

            if ($section !== '' && isset($data[$section])) {
                $rows = array_map(
                    fn($r) => array_merge($r, ['section' => $section]),
                    $data[$section]
                );
            } elseif ($section !== '') {
                $rows = [];   // section tidak dikenal
            } else {
                // Tanpa section → gabungkan semua dengan field section
                $rows = [];
                foreach ($data as $sec => $items) {
                    foreach ($items as $r) {
                        $rows[] = array_merge($r, ['section' => $sec]);
                    }
                }
            }

            return Response::json([
                'status' => 200,
                'data'   => [
                    'data'     => $rows,
                    'rowCount' => count($rows),
                ],
            ]);

        } catch (\Throwable $e) {
            error_log('[getEmployee] ' . $e->getMessage());
            return Response::json(['status' => 500, 'message' => 'Server Error'], 500);
        }
    }

    /** Cache static supaya JSON hanya dibaca sekali per request */
    private static ?array $karyawanCache = null;

    private function loadKaryawan(): array
    {
        if (self::$karyawanCache !== null) {
            return self::$karyawanCache;
        }

        $file = public_path('data/karyawan.json');   // ← public/data/karyawan.json

        if (!is_file($file)) {
            throw new \RuntimeException('File karyawan.json tidak ditemukan di ' . $file);
        }

        $raw = @file_get_contents($file);
        if ($raw === false) {
            throw new \RuntimeException('Gagal membaca karyawan.json');
        }

        $json = json_decode($raw, true);
        if (!is_array($json)) {
            throw new \RuntimeException('JSON karyawan tidak valid: ' . json_last_error_msg());
        }

        return self::$karyawanCache = $json;
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
