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
    }

    public function detalle() {
        $user = Auth::user();
        extract(Request::input());
        if(isset($agn,$prc,$ctr)) {
            $detalle = DB::select("call sp_web_tracking_distribu_seg(?,?,?)", [$agn,$prc,$ctr]);
            $imagenes = DB::select("call sp_web_imagenes_cargos(?,?,?)", [$agn,$prc,$ctr]);
            //$detalle = DB::select("call sp_web_tracking_distribu_seg(?,?,?)", [102029932,1,36]);
            //$imagenes = DB::select("call sp_web_imagenes_cargos(?,?,?)", [102029932,1,36]);
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

    public function sv_reclamo() {
        extract(Request::input());
        if(isset($autogen, $proceso, $control, $tipo, $titulo, $mensaje)) {
            $user = Auth::user();
            DB::table("atc_cab")->insert([
                "fRegistro" => date("Y-m-d H:i:s"),
                "vUsuRegistra" => $user->v_Codusuario,
                "cTipoGestionAtc" => "R",
                "iMotivoGestionAtc" => $tipo,
                "vEstado" => "PENDIENTE",
                "iCodConclusion" => 5,
                "vAsunto" => $titulo,
                "vDescripcion" => $mensaje,
                "cFlgEnviado" => "S",
                "fEnvio" => date("Y-m-d"),
                "hEnvio" => date("H:i:s"),
                "CodAutogen" => $autogen,
                "iCodAutogenAtc" => 0,
                "NroProceso" => $proceso,
                "NroControl" => $control,
                "iCodCliente" => $user->i_CodCliente,
            ]);
            return Response::json([
                "state" => "success"
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    public function export() {
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
            //prepara el pinche excel
            $filename = implode("_", [$user->v_Codusuario, date("YmdHis")]);
            $data = "<table>";
            $data .= "<tr>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">FechaIng</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">CodGuia</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Remito</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Control</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Nombre</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Direccion</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Ciudad</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Dedestinatario</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Tp.Envio</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Contenido</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Servicio</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Motivo</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Ult.Visita</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">NroDocu.</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Cuenta</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Comprobante</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Empresa</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">DetalleMotivo</th>
                <th style=\"background:#202020;color:#ffffff;border:1px solid #e0e0e0;\">Origen</th>
            </tr>";
            foreach ($resultados as $idx => $resultado) {
                $data .= "<tr>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->fecing) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->codguia) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->remito) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->control) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->nombre) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->direccion) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->ciudad) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->idedestin) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->tipoenvio) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->contenido) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->servicio) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->motivo) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->ultvisita) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->nrodocu) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->cuenta) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->comprobante) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->NomEmpresaDesti) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->detallemotivo) . "</td>
                    <td style=\"border:1px solid #e0e0e0;vertical-align:middle;" . ($idx % 2 == 0 ? "background:#f2f2f2;" : "background:#ffffff;") . "\">" . utf8_decode($resultado->origen) . "</td>
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