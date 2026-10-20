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
            $section = strtoupper($request->get('section') ?? '');
            // ── DATA HARDCODE SEMENTARA (MP MC 62) ──
            $dataKaryawan = [
                ['nik' => '74522', 'nama' => 'MUHAMMAD RAIHAN', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP MC 62','section' => 'PO'],
                ['nik' => '36769', 'nama' => "IBNI SAB'AH", 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP MC 62','section' => 'PO'],
                ['nik' => '74915', 'nama' => 'DEDE ALDI YANA', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP MC 62','section' => 'PO'],
                ['nik' => '74843', 'nama' => 'MUHAMMAD NUR APRIZAL', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP PENGANTI M RAIHAN','section' => 'PO'],
                ['nik' => '72574', 'nama' => 'NURUL FAHMI', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP PENGANTI IBNI SABAH','section' => 'PO'],
                ['nik' => '74850', 'nama' => 'DIKI NURDIANSYAH', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'MP PENGANTI DEDE ALDI','section' => 'PO'],
                ['nik' => '72865', 'nama' => 'MOHAMMAD RIZAL FAUZI', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'TEAM LEADER','section' => 'PO'],
                ['nik' => '72844', 'nama' => 'MUHAMAD IGUN AGUSTIAN', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'TEAM LEADER','section' => 'PO'],
                ['nik' => '73022', 'nama' => 'MUHAMMAD QUDDUS ULYA', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'TEAM LEADER','section' => 'PO'],
                ['nik' => '70508', 'nama' => 'AGUS BUDIMAN', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'GROUP LEADER','section' => 'PO'],
                ['nik' => '72194', 'nama' => 'CAHYADI HARJANTO', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'GROUP LEADER','section' => 'PO'],
                ['nik' => '71085', 'nama' => 'ABDUL MUTHOLIB', 'dept' => 'FACT 1 - PART PRODUCTION 1', 'divisi' => 'GROUP LEADER','section' => 'PO'],
                ['nik' => '72543', 'nama' => 'ABDUL RIZAL BISRI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '71965', 'nama' => 'ADI PURWANTO', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '71224', 'nama' => 'AGUS BUDI P.', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74061', 'nama' => 'AHMAD HARTADI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70735', 'nama' => 'AHMAD NOOR', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '36705', 'nama' => 'AIZA NUR AZQIA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74573', 'nama' => 'ALDI APRIYANSYAH', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70262', 'nama' => 'ALEXSANDER', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '36707', 'nama' => 'AMANDA PUTRI RAHMADANI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74134', 'nama' => 'AMELIA PUTRI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70282', 'nama' => 'ANANG PURWANTO', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '73960', 'nama' => 'ARIF FAUZI', 'dept' => 'SMT FACT1', 'divisi' => 'NEW PRODUCT', 'section' => 'SMT'],
                ['nik' => '74902', 'nama' => 'ASIDIN ARDIANSAH', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '36717', 'nama' => 'ASRI DAMAYANTI S', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '73069', 'nama' => 'ATIKA NUR OKTAVIANI', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '73417', 'nama' => 'BAGUS YOYO SANTOSO', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '73754', 'nama' => 'BUDI NUR CAHYANTO', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '72315', 'nama' => 'DANU DWIYARTONO', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '74727', 'nama' => 'DENNY SAPUTRO', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74523', 'nama' => 'DEVIRA KIRANTI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70777', 'nama' => 'DEWIFITRI YANTI', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '74339', 'nama' => 'DION RIZKI SANJAYA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '72601', 'nama' => 'DWI WAHYU ISLAMIATI', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '72291', 'nama' => 'EDI WIYONO', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '72510', 'nama' => 'EKO BUDI PRASTYO', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70723', 'nama' => 'EMUL MAULANA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '71416', 'nama' => 'ENDANG UYU', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74839', 'nama' => 'ERGI PUTRA SUTISNA RAMADHANI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '73169', 'nama' => 'FAHMI ALIM', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70526', 'nama' => 'FAIZAL RIFAI', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '74620', 'nama' => 'FEBRIA YANSYAH', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74748', 'nama' => 'FEBY IRAMA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '72632', 'nama' => 'FEGA ANDA LUSIANA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74464', 'nama' => 'FERDI HERMAWAN', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74833', 'nama' => 'HANDIKA PANGESTU', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '72564', 'nama' => 'HERU ATMOKO', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '73238', 'nama' => 'IMROATUSHOLIHAH', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '73138', 'nama' => 'ITA SILVIA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '73049', 'nama' => 'KHAERUNNISA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74035', 'nama' => 'LARAS CANTIKA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70755', 'nama' => 'M. FAIZ', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '72766', 'nama' => 'M. HAEKAL FAHRUS', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70364', 'nama' => 'MAD YANI', 'dept' => 'SMT FACT1', 'divisi' => 'KAIZEN', 'section' => 'SMT'],
                ['nik' => '70225', 'nama' => 'MUHAMAD DAUD', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '60084', 'nama' => 'MUHAMAD SHOLEH', 'dept' => 'SMT FACT1', 'divisi' => 'DOKUMENT CONTROL', 'section' => 'SMT'],
                ['nik' => '70410', 'nama' => 'MUHAMAD SODIK', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '36695', 'nama' => 'MUTIARA CAROLIN', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '73719', 'nama' => 'NABILAH', 'dept' => 'SMT FACT1', 'divisi' => 'PRATIWI DEHAN', 'section' => 'SMT'],
                ['nik' => '74671', 'nama' => 'NADILA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74173', 'nama' => 'NAHRAWAN', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74785', 'nama' => 'NANDA NURAENI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '72633', 'nama' => 'NENG MALASARI', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '72634', 'nama' => 'NUR AN-NISAH P. U', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70604', 'nama' => 'PIAN NOPIAN', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74371', 'nama' => 'RAHMAT SETIAWAN N', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70892', 'nama' => 'RIFAL FAUZI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '36709', 'nama' => 'RIZKA OKTAVIANI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '72537', 'nama' => 'ROBBY IKHWANI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74032', 'nama' => 'RYAN FERDIYANSYAH', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70176', 'nama' => 'SAMAN HUDI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74668', 'nama' => 'SEPTIAN CAHYA KOMAJAYA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74733', 'nama' => 'SETIANI YOGITASARI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '73134', 'nama' => 'SINTA EMILIA', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '36711', 'nama' => 'SITI SALWA ALVERANI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '73086', 'nama' => 'SITI SEMA', 'dept' => 'SMT FACT1', 'divisi' => 'MUSYAROPAH', 'section' => 'SMT'],
                ['nik' => '74574', 'nama' => 'SUCI WULANDARI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '72901', 'nama' => 'SUHARDI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70148', 'nama' => 'SUHENDAR', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70135', 'nama' => 'SUSI LAWATI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '36718', 'nama' => 'SYEFA RAKHMI ASANTI', 'dept' => 'SMT FACT1', 'divisi' => 'DOKUMENT CONTROL', 'section' => 'SMT'],
                ['nik' => '74516', 'nama' => 'USNIAH', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74622', 'nama' => 'VALDA IQMALIA A', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '72572', 'nama' => 'YAYAN SOFYAN RIFAI', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74506', 'nama' => 'YONI ARIYANT JAUHARI', 'dept' => 'SMT FACT1', 'divisi' => 'QUALITAS', 'section' => 'SMT'],
                ['nik' => '36719', 'nama' => 'YULI NINGSIH', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '74538', 'nama' => 'YUN DAVID TAMBUN', 'dept' => 'SMT FACT1', 'divisi' => 'OPERATOR', 'section' => 'SMT'],
                ['nik' => '70289', 'nama' => 'JOKO WALUYO', 'dept' => 'SMT FACT1', 'divisi' => 'SECTION MANAGER', 'section' => 'SMT'],
                ['nik' => '70535', 'nama' => 'M. MAS\'UDI', 'dept' => 'SMT FACT1', 'divisi' => 'GROUP LEADER', 'section' => 'SMT'],
                ['nik' => '70882', 'nama' => 'BEHRI', 'dept' => 'SMT FACT1', 'divisi' => 'GROUP LEADER', 'section' => 'SMT'],
                ['nik' => '71893', 'nama' => 'DUDUNG A.', 'dept' => 'SMT FACT1', 'divisi' => 'GROUP LEADER', 'section' => 'SMT'],
                ['nik' => '70262', 'nama' => 'ALEXSANDER', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '73069', 'nama' => 'ATIKA NUR OKTAVIANI', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '72315', 'nama' => 'DANU DWIYARTONO', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '70777', 'nama' => 'DEWIFITRI YANTI', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '72601', 'nama' => 'DWI WAHYU ISLAMIATI', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '72291', 'nama' => 'EDI WIYONO', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '70526', 'nama' => 'FAIZAL RIFAI', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '70225', 'nama' => 'MUHAMAD DAUD', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '70410', 'nama' => 'MUHAMAD SODIK', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '72633', 'nama' => 'NENG MALASARI', 'dept' => 'SMT FACT1', 'divisi' => 'TEAM LEADER', 'section' => 'SMT'],
                ['nik' => '74030', 'nama' => 'DIDIK RIYANTO', 'dept' => 'PRODUCTION ENGINEERING', 'divisi' => 'PE', 'section' => 'PE'],
                ['nik' => '80045', 'nama' => 'AGUNG SETYAWAN', 'dept' => 'PRODUCTION ENGINEERING', 'divisi' => 'PE', 'section' => 'PE'],
                ['nik' => '74311', 'nama' => 'M. ANDHIKA RAMLI', 'dept' => 'PRODUCTION ENGINEERING', 'divisi' => 'PE', 'section' => 'PE'],
                ['nik' => '73550', 'nama' => 'M. RIZKY FADILLAH', 'dept' => 'PRODUCTION ENGINEERING', 'divisi' => 'PE', 'section' => 'PE'],
            ];

            if (!empty($section)) {
                $dataKaryawan = array_values(array_filter($dataKaryawan, function($row) use ($section) {
                    return $row['section'] === $section;
                }));
            }

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
