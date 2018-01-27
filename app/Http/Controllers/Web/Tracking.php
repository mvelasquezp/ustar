<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Tracking extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("auth");
        date_default_timezone_set("America/Lima");
    }

    public function buscar() {
        $user = Auth::user();
        extract(Request::input());
        if(isset($dsd, $hst, $prd, $ofc)) {
            $prd = implode(",", $prd);
            $ofc = implode(",", $ofc);
            $doc = isset($doc) ? $doc : "Todos";
            $ref = isset($ref) ? $ref : "Todos";
            $dst = isset($dst) ? $dst : "Todos";
            $resultados = DB::select("call sp_web_tracking_distribu_list(?,?,?,?,?,?,?,?)", [$dsd, $hst, $prd, $ofc, $doc, $dst, $ref, $user->v_Codusuario]);
            return Response::json([
                "state" => "success",
                "data" => [
                    "rows" => $resultados
                ]
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    public function detalle() {
        $user = Auth::user();
        extract(Request::input());
        if(isset($agn,$prc,$ctr)) {
            /*$detalle = DB::select("call sp_web_tracking_distribu_seg(?,?,?)", [$agn,$prc,$ctr]);
            $imagenes = DB::select("call sp_web_imagenes_cargos(?,?,?)", [$agn,$prc,$ctr]);*/
            $detalle = DB::select("call sp_web_tracking_distribu_seg(?,?,?)", [1020212132,19,1]);
            $imagenes = DB::select("call sp_web_imagenes_cargos(?,?,?)", [1020212132,19,1]);
            return Response::json([
                "state" => "success",
                "data" => [
                    "rows" => $detalle,
                    "imgs" => $imagenes
                ]
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

}