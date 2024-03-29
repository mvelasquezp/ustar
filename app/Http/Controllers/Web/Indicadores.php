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
        ini_set("max_execution_time", 0);
        extract(Request::input());
        if(isset($ccl,$ofc,$prd,$loc,$nac,$int)) {
            $documento = isset($doc) ? $doc : "Todos";
            $ccl = implode(",", $ccl);
            $prd = implode(",", $prd);
            $ofc = implode(",", $ofc);
            $data1 = DB::select("call sp_web_grafica_distribu1_list(?,?,?,?,?,?,?,?)", [$ccl,$prd,$ofc,$user->v_Codusuario,$loc,$nac,$int,$documento]);
            $data2 = DB::select("call sp_web_grafica_distribu_list_g2(?,?,?,?,?,?,?,?)", [$ccl,$prd,$ofc,$user->v_Codusuario,$loc,$nac,$int,$documento]);
            $data3 = DB::select("call sp_web_grafica_distribu2_list(?,?,?,?,?,?,?,?)", [$ccl,$prd,$ofc,$user->v_Codusuario,$loc,$nac,$int,$documento]);
            //
            $datamap = [];
            if(strcmp($loc, "S") == 0) {
                $distritos = DB::select("call sp_web_grafica_mapa1(?,?,?,?,?,?,?,?)", [$ccl,$prd,$ofc,$user->v_Codusuario,$loc,$nac,$int,$documento]);
                //
                $datamap = [];
                $curr_distrito = -1;
                $curr_codigo = -1;
                $curr_entregado = 0;
                $curr_total = 0;
                foreach($distritos as $idx => $distrito) {
                    if($distrito->cdist != $curr_codigo) {
                        $path = implode(DIRECTORY_SEPARATOR, [env("APP_PUBLIC_PATH"),"jsdistritos",$curr_codigo . ".json"]);
                        if($idx > 0) {
                            $datamap[] = [
                                "codigo" => $curr_codigo,
                                "nombre" => $curr_distrito,
                                "total" => $curr_total,
                                "perc" => round(100 * $curr_entregado / $curr_total, 2),
                                "puntos" => file_exists($path) ? json_decode(file_get_contents($path), true)["dots"] : []
                            ];
                        }
                        $curr_codigo = $distrito->cdist;
                        $curr_distrito = $distrito->ndist;
                        $curr_total = 0;
                        $curr_entregado = 0;
                    }
                    if(strcmp($distrito->estado, "Entregado") == 0) $curr_entregado = $distrito->cant;
                    $curr_total += $distrito->cant;
                }
            }
            return Response::json([
                "state" => "success",
                "data" => [
                    "data1" => $data1,
                    "data2" => $data2,
                    "data3" => $data3,
                    "datamap" => $datamap
                ]
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    //

    public function if_entrega_distrito() {
        $user = Auth::user();
        if(Request::ajax()) {
            extract(Request::input());
            if(isset($ccl,$ofc,$prd,$loc,$nac,$int,$dst)) {
                $ccl = implode(",", $ccl);
                $prd = implode(",", $prd);
                $ofc = implode(",", $ofc);
                $doc = isset($doc) ? $doc : "Todos";
                $distritos = DB::select("call sp_web_grafica_mapa1_det(?,?,?,?,?,?,?,?,?)", [$ccl,$prd,$ofc,$user->v_Codusuario,$loc,$nac,$int,$doc,$dst]);
                $arr_distritos = [];
                $arr_motivos = [];
                $curr_estado = "x";
                $curr_total = 0;
                $ultra_total = 0;
                foreach ($distritos as $idx => $distrito) {
                    if(strcmp($curr_estado, $distrito->estado) != 0) {
                        if($idx > 0) {
                            $arr_distritos[] = [
                                "estado" => $curr_estado,
                                "cantidad" => $curr_total,
                                "motivos" => $arr_motivos
                            ];
                        }
                        $curr_estado = $distrito->estado;
                        $curr_total = 0;
                        $arr_motivos = [];
                    }
                    $curr_total += $distrito->cant;
                    $arr_motivos[] = [
                        "motivo" => $distrito->motivo,
                        "cantidad" => $distrito->cant
                    ];
                    $ultra_total += $distrito->cant;
                }
                $arr_distritos[] = [
                    "estado" => $curr_estado,
                    "cantidad" => $curr_total,
                    "motivos" => $arr_motivos
                ];
                return Response::json([
                    "success" => true,
                    "data" => $arr_distritos,
                    "todo" => $ultra_total
                ]);
            }
            else return Response::json([
                "success" => false,
                "message" => "Parámetros incorrectos"
            ]);
        }
        else return Response::json([
            "success" => false,
            "message" => "No tiene permisos para acceder aquí"
        ]);
    }

}