<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Indicadores extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
        date_default_timezone_set("America/Lima");
    }/*

    public function distribucion_entregas() {
        $user = Auth::user();
        extract(Request::input());
        if(isset($dsd, $hst, $prd, $ofc)) {
            $vDsd = explode("/", $dsd);
            $vHst = explode("/", $hst);
            $dsd = implode("-", [$vDsd[2], $vDsd[1], $vDsd[0]]);
            $hst = implode("-", [$vHst[2], $vHst[1], $vHst[0]]);
            $prd = implode(",", $prd);
            $ofc = implode(",", $ofc);
            $doc = isset($doc) ? "\"" . $doc . "\"" : "Todos";
            $ref = isset($ref) ? $ref : "Todos";
            $dst = isset($dst) ? $dst : "Todos";
            $resultados = DB::select("call sp_web_tracking_distribu_list(?,?,?,?,?,?,?,?)", [$dsd, $hst, $prd, $ofc, $doc, $dst, $ref, $user->v_Codusuario]);
            return Response::json([
                "state" => "success",
                "data" => [
                    "rows" => $resultados,
                    "doc" => $doc
                ]
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }*/

    public function ebuscar() {
        $user = Auth::user();
        extract(Request::input());
        if(isset($ccl,$ofc,$prd,$loc,$nac,$int)) {
            $ccl = implode(",", $ccl);
            $prd = implode(",", $prd);
            $ofc = implode(",", $ofc);
            $data1 = DB::select("call sp_web_grafica_distribu1_list(?,?,?,?,?,?,?)", [$ccl,$prd,$ofc,$user->v_Codusuario,$loc,$nac,$int]);
            $data2 = DB::select("call sp_web_grafica_distribu2_list(?,?,?,?,?,?,?)", [$ccl,$prd,$ofc,$user->v_Codusuario,$loc,$nac,$int]);
            return Response::json([
                "state" => "success",
                "data" => [
                    "data1" => $data1,
                    "data2" => $data2
                ]
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

}