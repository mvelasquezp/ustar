<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Reclamos extends Controller {
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
        extract(Request::input());
        if(isset($dsd, $hst, $pnd, $prc, $npr)) {
            $user = Auth::user();
            $vDesde = explode("/", $dsd);
            $desde = implode("-", [$vDesde[2], $vDesde[1], $vDesde[0]]);
            $vHasta = explode("/", $hst);
            $hasta = implode("-", [$vHasta[2], $vHasta[1], $vHasta[0]]);
            $registros = DB::select("call sp_web_reclamos_list(?,?,?,?,?,?)", [$user->v_Codusuario, $desde, $hasta, $pnd, $prc, $npr]);
            return Response::json([
                "state" => "success",
                "data" => [
                    "rows" => $registros
                ]
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    public function detalle() {
        extract(Request::input());
        if(isset($agn)) {
            $detalle = DB::select("call sp_web_reclamos_det(?)", [$agn]);
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

    public function elimina() {
        extract(Request::input());
        if(isset($agn)) {
            DB::table("atc_cab")->where("iCodAutogenAtc", $agn)->delete();
            return Response::json([
                "state" => "success"
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    public function combo() {
        extract(Request::input());
        if(isset($tpo)) {
            $tipos = DB::select("call sp_web_combo_motireclamo(?)", [$tpo]);
            return Response::json([
                "state" => "success",
                "data" => [
                    "motivos" => $tipos
                ]
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    public function dt_reclamo() {
        extract(Request::input());
        if(isset($agn)) {
            $reclamo = DB::table("atc_cab")
                ->where("iCodAutogenAtc", $agn)
                ->select("cTipoGestionAtc as tpg", "iMotivoGestionAtc as mtg", "vAsunto as asg", "vDescripcion as dsg")
                ->first();
            $motivos = DB::select("call sp_web_combo_motireclamo(?)", [$reclamo->tpg]);
            return Response::json([
                "state" => "success",
                "data" => [
                    "reclamo" => $reclamo,
                    "motivos" => $motivos
                ]
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    public function sv_reclamo() {
        extract(Request::input());
        if(isset($tpo, $mtv, $asn, $dsc)) {
            $user = Auth::user();
            DB::table("atc_cab")->insert([
                "fRegistro" => date("Y-m-d H:i:s"),
                "vUsuRegistra" => $user->v_Codusuario,
                "cTipoGestionAtc" => $tpo,
                "iMotivoGestionAtc" => $mtv,
                "vEstado" => "PENDIENTE",
                "iCodConclusion" => 1,
                "vAsunto" => $asn,
                "vDescripcion" => $dsc
            ]);
            return Response::json([
                "state" => true
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    public function upd_reclamo() {
        extract(Request::input());
        if(isset($agn, $tpo, $mtv, $asn, $dsc)) {
            $user = Auth::user();
            DB::table("atc_cab")
                ->where("iCodAutogenAtc", $agn)
                ->update([
                    "cTipoGestionAtc" => $tpo,
                    "iMotivoGestionAtc" => $mtv,
                    "vAsunto" => $asn,
                    "vDescripcion" => $dsc
                ]);
            return Response::json([
                "state" => true
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    public function export() {
        extract(Request::input());
        if(isset($dsd, $hst, $pnd, $prc, $npr)) {
            $user = Auth::user();
            $vDesde = explode("/", $dsd);
            $desde = implode("-", [$vDesde[2], $vDesde[1], $vDesde[0]]);
            $vHasta = explode("/", $hst);
            $hasta = implode("-", [$vHasta[2], $vHasta[1], $vHasta[0]]);
            $registros = DB::select("call sp_web_reclamos_list(?,?,?,?,?,?)", [$user->v_Codusuario, $desde, $hasta, $pnd, $prc, $npr]);
            //prepara el pinche excel
            $filename = implode("_", [$user->v_Codusuario, date("YmdHis")]);
            $data = "<table>";
            $data .= "<tr>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">FechaIng</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Asunto</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Motivo</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Estado</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Registrado</th>
            </tr>";
            foreach ($registros as $idx => $resultado) {
                $data .= "<tr>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->fecharesultado) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->detallereclamo) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->respuesta) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->estado) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->registra) . "</td>
                </tr>";
            }
            $data .= "</table>";
            @mkdir(env("APP_FILES_PATH"), 0777, true);
            $f = fopen(implode(DIRECTORY_SEPARATOR, [env("APP_FILES_PATH"), $filename . ".xls"]) , "wb");
            fwrite($f, $data);
            fclose($f);
            //devuelve respuesta
            return Response::json([
                "state" => "success",
                "id" => $filename
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

}