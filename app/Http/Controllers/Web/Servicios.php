<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Servicios extends Controller {
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

    public function distribucion_buscar() {
        $user = Auth::user();
        extract(Request::input());
        if(isset($dsd, $hst, $ofc, $ccs, $prd, $loc, $nac, $int)) {
            $vDsd = explode("/", $dsd);
            $vHst = explode("/", $hst);
            $dsd = implode("-", [$vDsd[2], $vDsd[1], $vDsd[0]]);
            $hst = implode("-", [$vHst[2], $vHst[1], $vHst[0]]);
            $sofc = implode(",", $ofc);
            $sccs = implode(",", $ccs);
            $sprd = implode(",", $prd);
            $resultados = DB::select("call sp_web_servicios_distribu_list(?,?,?,?,?,?,?,?,?)", [$dsd, $hst, $sprd, $sofc, $sccs, $user->v_Codusuario, $loc, $nac, $int]);
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

    public function distribucion_detalle() {
        $user = Auth::user();
        extract(Request::input());
        if(isset($agn,$rem)) {
            $detalle = DB::select("call sp_web_servicios_distribu_det_list(?,?,?)", [$agn,$rem,3]);
            return Response::json([
                "state" => "success",
                "data" => [
                    "rows" => $detalle
                ]
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

}