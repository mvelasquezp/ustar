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

    //ajax

    public function export() {
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
            //prepara el pinche excel
            $filename = implode("_", [$user->v_Codusuario, date("YmdHis")]);
            $data = "<table>";
            $data .= "<tr>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Origen</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">FechaIng</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Docing</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Remito</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Servicio</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Contenido</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Cliente</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Cant.</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Peso</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Observaciones</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Referencia</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Contacto</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">C.Costo</th>
            </tr>";
            foreach ($resultados as $idx => $resultado) {
                $data .= "<tr>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->origen) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->fechaing) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->docing) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->remito) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->servicio) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->contenido) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->cliente) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->canenvios) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->peso) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->TxtObserv) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->refcli) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->nomcontacto) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->nomccosto) . "</td>
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