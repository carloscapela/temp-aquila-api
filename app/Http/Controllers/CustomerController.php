<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class CustomerController extends Controller
{
    public function index($customer)
    {
        if (!$customer) return [ 'message' => 'Não encontrado!' ];

        // Checks if the client json exists
        $path = database_path("timeline_$customer.json");
        // error handling
        if (!file_exists($path)) {
            return [ 'message' => 'Dados não encontrado!' ];
        }
        $content = json_decode( file_get_contents($path) );

        $data = $this->filterToDate(request(), $content);

        return $data;
    }
    // FORMAT =======================================================
    // accession_number : 0
    // avg_exam_duration : 3.325245098
    // avg_grade : 0
    // complete_musc_evident : 0
    // customer_name : 9
    // detected_imf : 0
    // exam_real_duration : 0
    // general_score : 0
    // id : "0"
    // lat : 0
    // long : 0
    // medial_lateral_area_relation : 0
    // minor_pec_detected : 0
    // muscle_convex : 0
    // n_images : 0
    // name : 9
    // nipple_centralized : 0
    // nipple_in_profile_CC : 0
    // nipple_in_profile_MLO : 0
    // nps_score : 0
    // operator_name : "none"
    // pectoralis_width : 0
    // prod_score : 0.715875
    // qual_score : 0.6478873239
    // safety_score : 0
    // sagging_breast_mlo : 0
    // start_time : "none"
    // study_date : "2022-10-10T00:00:00.000Z"
    // study_description : 0
    // symmetry_cc_images : 0
    // symmetry_left_images : 0
    // symmetry_mlo_images : 0
    // symmetry_right_images : 0
    // total_exams : 80
    // type : "Customer:"
    // type_of_exam : "SCREENING"
    // unit_name : "none"
    // ===========================================================

    // Format YYYY-MM-DD
    // protected function filterToDate(Request $request, $json)
    // {
    //     list($start, $end) = $this->getDates($request);

    //     // Keys
    //     $keys = [];

    //     // Filter date
    //     foreach ($json->study_date as $k => $d) {
    //         $date = Carbon::parse($d);
    //         if (
    //             $date->between($start, $end) ||
    //             $start->format('Y-m-d') === $date->format('Y-m-d')
    //         ){
    //             $keys[$k] = $k;
    //         }
    //     }

    //     $data = [];
    //     foreach ($json as $field => $array) {
    //         foreach ($array as $key => $value) {
    //             if (Arr::exists($keys, $key)) {
    //                 $data[$field][$key] = $value;
    //             }
    //         }
    //     }

    //     return $data;
    // }

    protected function filterToDate(Request $request, $json)
    {
        list($start, $end) = $this->getDates($request);

        // Keys
        $keys = [];

        // Filter date
        foreach ($json->study_date as $k => $d) {
            $date = Carbon::parse($d);
            if (
                $date->between($start, $end) ||
                $start->format('Y-m-d') === $date->format('Y-m-d')
            ){
                $keys[$k] = $k;
            }
        }

        $data = [];
        foreach ($json as $field => $array) {
            foreach ($array as $key => $value) {
                if (Arr::exists($keys, $key)) {
                    $data[$key][$field] = $value;
                }
            }
        }

        return $data;
    }

    protected function getDates(Request $request)
    {
        $date_start = now()->subMonth();
        $date_end = now();

        // Date End
        $start = $request->input('start')
            ? Carbon::parse($request->input('start'))
            : $date_start;

        // Date Start
        $end = $request->input('end')
            ? Carbon::parse($request->input('end'))
            : $date_end;

        return [
            $start,
            $end,
        ];
    }
}
