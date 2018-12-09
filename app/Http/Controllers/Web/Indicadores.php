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
            $data2 = DB::select("call sp_web_grafica_distribu_list_g2(?,?,?,?,?,?,?)", [$ccl,$prd,$ofc,$user->v_Codusuario,$loc,$nac,$int]);
            $data3 = DB::select("call sp_web_grafica_distribu2_list(?,?,?,?,?,?,?)", [$ccl,$prd,$ofc,$user->v_Codusuario,$loc,$nac,$int]);
            return Response::json([
                "state" => "success",
                "data" => [
                    "data1" => $data1,
                    "data2" => $data2,
                    "data3" => $data3
                ]
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    //

    public function if_entrega_g1() {
        extract(Request::input());
        if(isset($ccl,$grn,$str,$cno,$estado)) {
            $data = DB::table("guias_ingreso as gi")
                ->join("datos_adicionales as da", "gi.codautogen", "=", "da.codautogen")
                ->join("envios_x_proceso as envxp", function($join) {
                    $join->on("envxp.codautogen", "=", "da.Codautogen")
                        ->on("envxp.nroproceso", "=", "da.Nroproceso")
                        ->on("envxp.NroControl", "=", "da.NroControl");
                })
                ->leftJoin("motivos_justifica as justi2", function($join_j2) {
                    $join_j2->on("justi2.CodMotivoEnvio", "=", "envxp.CodMotivoWeb")
                        ->on("justi2.CodJustiMotivo", "=", "envxp.CodJustiWeb");
                })
                ->join("estados_envios", "envxp.CodEstadoWeb", "=", "estados_envios.CodEstadoEnvio")
                ->join("motivos_natura", "envxp.CodMotivoWeb", "=", "motivos_natura.CodMotivoEnvio")
                ->join("guias_ing_procesos as gip", function($join_gip) {
                    $join_gip->on("gip.codautogen", "=", "envxp.codautogen")
                        ->on("gip.nroproceso", "=", "envxp.nroproceso");
                })
                ->join("motivos_justifica as justi", function($join_justi) {
                    $join_justi->on("justi.CodJustiMotivo", "=", "envxp.CodJustiWeb")
                        ->on("justi.CodMotivoEnvio", "=", "envxp.CodMotivoWeb");
                })
                ->join("motivos_natura as moti", "envxp.CodMotivoWeb", "=", "moti.CodMotivoEnvio")
                ->join("estados_envios as estenv", "envxp.CodEstadoWeb", "=", "estenv.CodEstadoEnvio")
                ->leftJoin("direcciones as dire", "envxp.CodDestinatario", "=", "dire.CodDestinatario")
                ->whereRaw("gi.dtmguia >= '2017-01-01'")
                ->where("gip.FlgIngresosReclamo", "N")
                ->where(DB::raw("case justi.Flg_Entrega_efectiva when 'S' then 'Directo' when 'N' then 'Bajo Puerta' else upper(moti.DesMotivoEnvio) end"), $estado)
                ->whereIn("gi.CiCloCorteFactuCliente", $ccl)
                //->whereIn("da.GrupoCliente", $cno)
                //->whereIn("da.NroDocuCliente", $grn)
                //->whereIn("da.Sector", $str)
                ->select(
                    DB::raw("case justi.Flg_Entrega_efectiva when 'S' then 'Directo' when 'N' then 'Bajo Puerta' else upper(moti.DesMotivoEnvio) end as motivo"),
                    "da.IdeDestinatario as codcn",
                    "dire.NomDestinatario as consult",
                    "gi.CiCloCorteFactuCliente as ciclo",
                    "estenv.DesEstadoEnvio as situacion"
                )
                ->orderBy("motivo", "asc")
                ->orderBy("situacion", "asc")
                ->get();
            return Response::json([
                "success" => true,
                "data" => $data
            ]);
        }
        return Response::json([
            "success" => false,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function if_entrega_g2() {
        if(Request::ajax()) {
            extract(Request::input());
            if(isset($ccl,$grn,$str,$cno,$dia,$est)) {
                $data = DB::table("guias_ingreso as gi")
                    ->join("datos_adicionales as da", "gi.codautogen", "=", "da.codautogen")
                    ->join("envios_x_proceso as envxp", function($join) {
                        $join->on("envxp.codautogen", "=", "da.Codautogen")
                            ->on("envxp.nroproceso", "=", "da.Nroproceso")
                            ->on("envxp.NroControl", "=", "da.NroControl");
                    })
                    ->join("estados_envios", "envxp.CodEstadoWeb", "=", "estados_envios.CodEstadoEnvio")
                    ->join("motivos_envios as moti", "moti.CodMotivoEnvio", "=", "envxp.CodMotivoWeb")
                    ->join("motivos_natura", "envxp.CodMotivoWeb", "=", "motivos_natura.CodMotivoEnvio")
                    ->join("motivos_justifica as justi", function($join_justi) {
                        $join_justi->on("justi.CodJustiMotivo", "=", "envxp.CodJustiWeb")
                            ->on("justi.CodMotivoEnvio", "=", "envxp.CodMotivoWeb");
                    })
                    ->join("guias_ing_procesos as gip", function($join) {
                        $join->on("gip.codautogen", "=", "envxp.codautogen")
                            ->on("gip.nroproceso", "=", "envxp.nroproceso");
                    })
                    ->whereRaw("gi.dtmguia >= '2017-01-01'")
                    ->where("gip.FlgIngresosReclamo", "N")
                    ->whereIn("gi.CiCloCorteFactuCliente", $ccl)
                    ->whereIn("da.GrupoCliente", $cno)
                    ->whereIn("da.NroDocuCliente", $grn)
                    ->whereIn("da.Sector", $str)
                    ->where(DB::raw("f_obtiene_numerodia_visita(envxp.codautogen,envxp.nroproceso, DATE(envxp.DtUltVisitaWeb))"), $dia)
                    ->where("estados_envios.DesEstadoEnvio", $est)
                    ->select(
                        DB::raw("case justi.Flg_Entrega_efectiva when 'S' then 'Directo' when 'N' then 'Bajo Puerta' else upper(moti.DesMotivoEnvio) end as motivo"),
                        DB::raw("count(*) as cant"))
                    ->groupBy("motivo")
                    ->get();
                return Response::json([
                    "success" => true,
                    "data" => $data
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

    public function if_entrega_g3() {
        if(Request::ajax()) {
            extract(Request::input());
            if(isset($ccl,$grn,$str,$cno,$est)) {
                $data = DB::table("guias_ingreso as gi")
                    ->join("datos_adicionales as da", "gi.codautogen", "=", "da.codautogen")
                    ->join("envios_x_proceso as envxp", function($join) {
                        $join->on("envxp.codautogen", "=", "da.Codautogen")
                            ->on("envxp.nroproceso", "=", "da.Nroproceso")
                            ->on("envxp.NroControl", "=", "da.NroControl");
                    })
                    ->join("estados_envios", "envxp.CodEstadoWeb", "=", "estados_envios.CodEstadoEnvio")
                    ->join("motivos_envios as moti", "moti.CodMotivoEnvio", "=", "envxp.CodMotivoWeb")
                    ->join("motivos_natura", "envxp.CodMotivoWeb", "=", "motivos_natura.CodMotivoEnvio")
                    ->join("motivos_justifica as justi", function($join_justi) {
                        $join_justi->on("justi.CodJustiMotivo", "=", "envxp.CodJustiWeb")
                            ->on("justi.CodMotivoEnvio", "=", "envxp.CodMotivoWeb");
                    })
                    ->join("guias_ing_procesos as gip", function($join) {
                        $join->on("gip.codautogen", "=", "envxp.codautogen")
                            ->on("gip.nroproceso", "=", "envxp.nroproceso");
                    })
                    ->whereRaw("gi.dtmguia >= '2017-01-01'")
                    ->where("gip.FlgIngresosReclamo", "N")
                    ->whereIn("gi.CiCloCorteFactuCliente", $ccl)
                    ->whereIn("da.GrupoCliente", $cno)
                    ->whereIn("da.NroDocuCliente", $grn)
                    ->where("da.Sector", str_replace("+", " ", $str))
                    ->where("estados_envios.DesEstadoEnvio", $est)
                    ->select(
                        DB::raw("case justi.Flg_Entrega_efectiva when 'S' then 'Directo' when 'N' then 'Bajo Puerta' else upper(moti.DesMotivoEnvio) end as motivo"),
                        DB::raw("count(*) as cant")
                    )
                    ->groupBy("motivo")
                    ->get();
                return Response::json([
                    "success" => true,
                    "data" => $data
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