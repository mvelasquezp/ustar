<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class WebService extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        //$this->middleware("ws")->except("home");
        date_default_timezone_set("America/Lima");
    }

    public function home() {
        return "web service ustar app";
    }

    public function login() {
        extract(Request::input());
        if(isset($usr, $psw)) {
            //c_user,c_nomusuario,CodCliente,CodAreaClie,CodTransportista,CodPlacaVehiculo,iCodContacto
            $user = DB::table("seg_user")
                ->where("v_Codusuario", $usr)
                ->where("v_clave", $psw);
            if($user->count() > 0) {
                $user = $user->select("v_Codusuario as id", "v_CodPerEmpresa as codper", DB::raw("ifnull(i_CodCliente,-1) as cocli"),
                    DB::raw("ifnull(i_CodContacto,0) as contacto"), "v_Apellidos as apellidos", "v_Nombres as nombres",
                    "v_NroDocide as docid", DB::raw("ifnull(v_Email,'') as mail"), DB::raw("ifnull(v_Telefonos,'') as telefs"))
                ->first();
                $permisos = DB::table("seg_user as usr")
                    ->join("seg_modulos_x_perfiles as mxp", "mxp.i_CodTipoPerfil", "=", "usr.i_CodTipoPerfil")
                    ->where("usr.v_Codusuario", $user->id)
                    ->select(DB::raw("group_concat(mxp.i_CodModulo separator ',') as mods"))
                    ->first();
                return Response::json([
                    "result" => "success",
                    "rqid" => 1,
                    "data" => [
                        "usuario" => $user,
                        "modulos" => $permisos
                    ]
                ]);
            }
            else return Response::json([
                "result" => "error",
                "rqid" => 1,
                "message" => "Credenciales incorrectas. Intente nuevamente."
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 1,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function ls_acceso_modulos() { //esta function irá embebida en "login", y utilizara id = codper
        extract(Request::input());
        if(isset($id)) {
            $ls = DB::select("call sp_app_acceso_modulos_list(?)", [$id]);
            return Response::json([
                "result" => "success",
                "rqid" => 2,
                "data" => [
                    "accesos" => $ls
                ]
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 2,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function ls_resumen_pariente() {
        extract(Request::input());
        if(isset($cpr)) {
            $hoy = date("Y-m-d");
            //
            $count1 = DB::select("select f_app_confirma_despachos_count(?,?) cont", [$hoy, $cpr]);
            $count2 = DB::select("select f_app_confirma_recojos_count(?,?) cont", [$hoy, $cpr]);
            $count3 = DB::select("select f_app_confirma_entregas_count(?,?) cont", [$hoy, $cpr]);
            return Response::json([
                "result" => "success",
                "rqid" => 3,
                "data" => [
                    "cn1" => (int) $count1[0]->cont,
                    "cn2" => (int) $count2[0]->cont,
                    "cn3" => (int) $count3[0]->cont
                ]
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 3,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function ls_lista_despachos() {
        extract(Request::input());
        if(isset($cpr, $fch)) {
            $despachos = DB::select("call sp_app_confirma_despachos_list(?,?)", [$fch, $cpr]);
            $proveedores = DB::table("proveedores")
                ->select("CodProveedor as value", "NomProveedor as text")
                ->orderBy("NomProveedor", "asc")
                ->get();
            return Response::json([
                "result" => "success",
                "rqid" => 2,
                "data" => [
                    "despachos" => $despachos,
                    "proveedores" => $proveedores
                ]
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 2,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function upd_despacho() {
        extract(Request::input());
        if(isset($prv, $imp, $cpr, $doc, $org, $dst, $prd, $mnf)) {
            DB::table("manifiesto")
                ->where("CodAgenciaOrigen", $org)
                ->where("CodAgenciaDestino", $dst)
                ->where("CodPeriodo", $prd)
                ->where("NroManifiesto", $mnf)
                ->update([
                    "CodProveedor" => $prv,
                    "Importe" => $imp,
                    "UserDespachador" => $cpr,
                    "NroDocuProv" => $doc
                ]);
            return Response::json([
                "result" => "success",
                "rqid" => 201,
                "message" => "Manifiesto " . $mnf . " actualizado!"
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 201,
            "message" => "Parámetros incorrectos"
        ]);
    }

    //modulo recojos

    public function ls_lista_recojos() {
        extract(Request::input());
        if(isset($cpr, $fch)) {
            $recojos = DB::select("call sp_app_confirma_recojos_list(?,?)", [$fch, $cpr]);
            $motivos = DB::table("motivos_envios")
                ->where("CodEstadoEnvio", "O")
                ->select("CodMotivoEnvio as value", "DesMotivoEnvio as text")
                ->orderBy("DesMotivoEnvio", "asc")
                ->get();
            return Response::json([
                "result" => "success",
                "rqid" => 301,
                "data" => [
                    "recojos" => $recojos,
                    "motivos" => $motivos
                ]
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 301,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function ls_detalle_recojo() {
        extract(Request::input());
        if(isset($car)) {
            $detalle = DB::select("call sp_app_confirma_recojos_det_list(?)", [$car]);
            return Response::json([
                "result" => "success",
                "rqid" => 302,
                "data" => [
                    "detalle" => $detalle
                ]
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 302,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function upd_recojo() {
        extract(Request::input());
        if(isset($car,$cmr,$obs)) {
            DB::table("guias_ing_recojos")
                ->where("CodAutoRecojo", $car)
                ->update([
                    "CodMotivoRecojo" => $cmr,
                    "DtRecojoRealizado" => date("Y-m-d H:i:s"),
                    "ObsResultadoRecojo" => $obs
                ]);
            return Response::json([
                "result" => "success",
                "rqid" => 303,
                "message" => "Registro actualizado!"
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 303,
            "message" => "Parámetros incorrectos"
        ]);
    }

    //modulo de descargos

    public function ls_lista_entregas() {
        extract(Request::input());
        if(isset($cpr, $fch, $all)) {
            $entregas = DB::select("call sp_app_confirma_entregas_list(?,?,?)", [$fch, $cpr,$all]);
            $motivos = DB::table("motivos_envios")
                ->where("FlgDescargoWAP", "S")
                ->select("CodMotivoEnvio as value", "DesMotivoEnvio as text")
                ->orderBy("DesMotivoEnvio", "asc")
                ->get();
            $justificaciones = DB::table("motivos_justifica")
                //->where("CodMotivoEnvio","E8")
                ->select("CodJustiMotivo as value", "DesJustiMotivo as text", "CodMotivoEnvio as extra")
                ->get();
            return Response::json([
                "result" => "success",
                "rqid" => 401,
                "data" => [
                    "entregas" => $entregas,
                    "motivos" => $motivos,
                    "justificaciones" => $justificaciones
                ]
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 401,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function upd_entrega() {
        extract(Request::input());
        if(isset($cpr,$agn,$prc,$ctr,$mot,$jst,$est,$obs)) {
            DB::table("envios_x_proceso")
                ->where("CodAutogen", $agn)
                ->where("NroProceso", $prc)
                ->where("NroControl", $ctr)
                ->update([
                    "CodMotivoWeb" => $mot,
                    "CodJustiWeb" => $jst,
                    "CodEstadoWeb" => $est,
                    "DtUltVisitaWeb" => date("Y-m-d H:i:s"),
                    "FlgCargoPendiente" => "S"
                ]);
            DB::table("resultados_detalles")->insert([
                "CodAutogen" => $agn,
                "NroProceso" => $prc,
                "NroControl" => $ctr,
                "TxtObserv" => $obs,
                "CodControlista" => $cpr,
                "FecVisita" => date("Y-m-d H:i:s")
            ]);
            return Response::json([
                "result" => "success",
                "rqid" => 402,
                "message" => "Registro actualizado!"
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 402,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function upd_imagen() {
        extract(Request::input());
        if(isset($cpr,$agn,$prc,$ctr,$nom,$base64)) {
            //arma la pinche ruta
            $count = DB::table("guias_ingreso as gi")
                ->join("tipos_documentos as tdc", "gi.CodTipDocu", "=", "tdc.CodTipDocu")
                ->where("gi.codautogen", $agn)
                ->count();
            if($count > 0) {
                $vPath = DB::table("guias_ingreso as gi")
                    ->join("tipos_documentos as tdc", "gi.CodTipDocu", "=", "tdc.CodTipDocu")
                    ->where("gi.codautogen", $agn)
                    ->select("gi.codguia as guia", "tdc.AbrTipDocu as abr", "tdc.nomcarpetaimg as nomcp", "tdc.nomcarpetaimgActual as nomact")
                    ->first();
                //verifica la ruta de almacenamiento
                $fullpath = implode(DIRECTORY_SEPARATOR, [env("APP_STORAGE_PATH"), $vPath->nomact, $vPath->guia]);
                //$fullpath = implode(DIRECTORY_SEPARATOR, [env("APP_STORAGE_PATH"), date("Ymd")]);
                @mkdir($fullpath, 0777, true);
                $filename = $prc . "-" . $ctr . date("His") . ".jpg";
                $fullname = $fullpath . DIRECTORY_SEPARATOR . $filename;
                //guarda la iamgen
                file_put_contents($fullname, base64_decode($base64));
                //inserta registro en la bd
                DB::table("envios_x_proceso_img")->insert([
                    "CodAutogen" => $agn,
                    "NroProceso" => $prc,
                    "NroControl" => $ctr,
                    "NomImagen" => $filename,
                    "CodUsuario" => $cpr
                ]);
                return Response::json([
                    "result" => "success",
                    "rqid" => 403,
                    "message" => "Imagen actualizada!"
                ]);
            }
            else return Response::json([
                "result" => "error",
                "rqid" => 403,
                "message" => "No se pudo generar la ruta de almacenamiento para la imagen"
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 403,
            "message" => "Parámetros incorrectos"
        ]);
    }

}