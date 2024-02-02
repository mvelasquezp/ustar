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

    private $conn = "mysql";
    private $numvers = 17;

    public function __construct() {
        //$this->middleware("ws")->except("home");
        date_default_timezone_set("America/Lima");
        ini_set("max_execution_time",0);
    }

    public function home() {
        return $this->numvers . " web service ustar app";
    }

    public function login() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($usr, $psw)) {
            //c_user,c_nomusuario,CodCliente,CodAreaClie,CodTransportista,CodPlacaVehiculo,iCodContacto
            $user = DB::table("seg_user")
                ->where("v_Codusuario", $usr)
                ->where("v_clave", $psw);
            if($user->count() > 0) {
                $user = $user->select(
                    "v_Codusuario as id",
                    "v_CodPerEmpresa as codper",
                    DB::raw("ifnull(i_CodCliente,-1) as cocli"),
                    DB::raw("ifnull(i_CodContacto,0) as contacto"),
                    "v_Apellidos as apellidos",
                    "v_Nombres as nombres",
                    "v_NroDocide as docid",
                    DB::raw("ifnull(v_Email,'') as mail"),
                    DB::raw("ifnull(v_Telefonos,'') as telefs"),
                    "i_CodTipoPerfil as tipo"
                )
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
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($id)) {
            $ls = DB::connection($conn)->select("call sp_app_acceso_modulos_list(?)", [$id]);
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
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($cpr)) {
            $hoy = date("Y-m-d");
            //
            $count1 = DB::connection($conn)->select("select f_app_confirma_despachos_count(?,?) cont", [$hoy, $cpr]);
            $count2 = DB::connection($conn)->select("select f_app_confirma_recojos_count(?,?) cont", [$hoy, $cpr]);
            $count3 = DB::connection($conn)->select("select f_app_confirma_entregas_count(?,?) cont", [$hoy, $cpr]);
            return Response::json([
                "result" => "success",
                "rqid" => 3,
                "data" => [
                    "cn1" => (int) $count1[0]->cont,
                    "cn2" => (int) $count2[0]->cont,
                    "cn3" => (int) $count3[0]->cont,
                    "numvers" => $this->numvers,
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
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($cpr, $fch, $agencia)) {
            $despachos = DB::connection($conn)->select("call sp_app_confirma_despachos_list(?,?,?)", [$fch, $cpr, $agencia]);
            $proveedores = DB::connection($conn)->table("proveedores")
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
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($prv, $imp, $cpr, $doc, $org, $dst, $prd, $mnf)) {
            if (isset($bultos, $femb, $tipodoc)) {
                DB::statement("call sp_app_update_confirma_despacho (?,?,?,?,?,?,?,?,?,str_to_date(?,'%Y-%m-%d'),?,'-',?)", [
                    $org, $dst, $prd, $mnf, $tipodoc, $doc, $imp, $bultos, $prv, $femb, $clave, $cpr
                ]);
            }
            else {
                DB::connection($conn)->table("manifiesto")
                    ->where("CodAgenciaOrigen", $org)
                    ->where("CodAgenciaDestino", $dst)
                    ->where("CodPeriodo", $prd)
                    ->where("NroManifiesto", $mnf)
                    ->update([
                        "CodProveedor" => $prv,
                        "Importe" => $imp,
                        "NroBultos" => $bultos, 
                        "UserDespachador" => $cpr,
                        "NroDocuProv" => $doc
                    ]);
            }
            // enviar el whatsapp
$log_dir = "/var/www/ustar/logs/" . date("Y/m/d");
@mkdir($log_dir, 0777, true);
file_put_contents($log_dir . "/log.txt", "call sp_app_whassap_confirma_despacho_qry(?,?,?,?);\n" . print_r([$org, $dst, $mnf, $prd], true) . "\n", FILE_APPEND + LOCK_EX);
            $datos = DB::select("call sp_app_whassap_confirma_despacho_qry(?,?,?,?)", [$org, $dst, $mnf, $prd]);
            if (count($datos) > 0) {
                $datos = $datos[0];
                $url = urlencode(base64_encode(implode("|", [$org, $dst, $mnf, $prd])));
                // fecha_recojo, hora_recojo, NroBultos, factura, NomProveedor, nomagencia, TelefonoMensajes, nombrecorto
                $nomcorto = strlen($datos->nombrecorto) > 0 ? $datos->nombrecorto : "AGENTE";
                $bultos = $datos->NroBultos;
                $proveedor = $datos->NomProveedor;
                // $fecha = $datos->fecha_recojo;
                // $hrecojo = strlen($datos->hora_recojo) > 0 ? $datos->hora_recojo : "00:00";
                $fecha_hora = $datos->dia_recojo;
                $json = [
                    "identifier" => implode(":", [env("WABA_UUID"), "51" . $datos->TelefonoMensajes]),
                    "payload" => [
                        "type" => "template",
                        "template" => [
                            "type" => "notification",
                            "notification" => [
                                "name" => env("WABA_DESPACHADOR"),
                                "language" => "es_ES",
                                "components" => [
                                    [
                                        "type" => "header",
                                        "parameters" => [
                                            ["type" => "text", "text" => $nomcorto]
                                        ]
                                    ],
                                    [
                                        "type" => "body",
                                        "parameters" => [
                                            ["type" => "text", "text" => $bultos],
                                            ["type" => "text", "text" => $proveedor],
                                            ["type" => "text", "text" => $fecha_hora]
                                        ]
                                    ],
                                    [
                                        "type" => "button",
                                        "sub_type" => "url",
                                        "index" => "0", 
                                        "parameters" => [
                                            ["type" => "text", "text" => $url]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
                $token = DB::select("select TokenMsgWassap token from empresas where CodEmpresa = ?", [1])[0]->token;
                $ch = curl_init("https://api.messengerpeople.dev/messages");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/vnd.messengerpeople.v1+json",
                    "Accept: application/vnd.messengerpeople.v1+json",
                    "Authorization: Bearer " . $token,
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
                $response = curl_exec($ch);
                curl_close($ch);
file_put_contents($log_dir . "/log.txt", $response . "\n\n", FILE_APPEND + LOCK_EX);
                $response = json_decode($response);
                if (!isset($response->error)) {
                    // WHATSAP' and msg.DestinoMsg = 'DESTINATARIO
                    DB::connection($conn)->statement("call sp_app_whassap_confirma_despacho_envio(?,?,?,?)", [$org, $dst, $mnf, $prd]);
                }
            }
            // fin
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
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($cpr, $fch)) {
            $recojos = DB::connection($conn)->select("call sp_app_confirma_recojos_list(?,?)", [$fch, $cpr]);
            $motivos = DB::connection($conn)->table("motivos_envios")
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
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($car)) {
            $detalle = DB::connection($conn)->select("call sp_app_confirma_recojos_det_list(?)", [$car]);
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
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($car,$cmr,$obs)) {
            DB::connection($conn)->table("guias_ing_recojos")
                ->where("CodAutoRecojo", $car)
                ->update([
                    "CodMotivoRecojo" => $cmr,
                    "DtRecojoRealizado" => date("Y-m-d H:i:s"),
                    "ObsResultadoRecojo" => $obs
                ]);
            // ejecutar sp whatsapp
            return Response::json([
                "result" => "success",
                "rqid" => 303,
                "message" => "Registro actualizado!"
            ]);
        }
// ola ke ase
        if (isset($origen, $destino, $periodo, $manifiesto, $motivo, $observaciones, $usuario)) {
            DB::statement("call sp_app_update_confirma_recojos (?,?,?,?,now(),?,?,?)", [
                $origen, $destino, $periodo, $manifiesto, $motivo, $observaciones, $usuario
            ]);
            // ejecutar sp whatsapp
            return Response::json([
                "result" => "success",
                "rqid" => 303,
                "message" => "Registro actualizado!"
            ]);
        }
        return Response::json([
            "result" => "error",
            "rqid" => 303,
            "message" => "Parámetros incorrectos"
        ]);
    }

    //modulo de descargos

    public function ls_lista_entregas() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($cpr, $fch, $all)) {
            $query = isset($query) ? $query : "";
            $entregas = DB::connection($conn)->select("call sp_app_confirma_entregas_list(?,?,?,?)", [$fch, $cpr, $all, $query]);
            $motivos = DB::connection($conn)->table("motivos_envios")
                ->where("FlgDescargoWAP", "S")
                ->select("CodMotivoEnvio as value", "DesMotivoEnvio as text")
                ->orderBy("DesMotivoEnvio", "asc")
                ->get();
            $justificaciones = DB::connection($conn)->table("motivos_justifica")
                //->where("CodMotivoEnvio","E8")
                ->select("CodJustiMotivo as value", "DesJustiMotivo as text", "CodMotivoEnvio as extra")
                ->get();
            return Response::json([
                "result" => "success",
                "rqid" => 401,
                "data" => [
                    "entregas" => $entregas,
                    "motivos" => $motivos,
                    "justificaciones" => $justificaciones,
                    "cpr" => $cpr
                ]
            ]);
        }
        else return Response::json([
            "result" => "error",
            "rqid" => 401,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function check_version () {
        return Response::make($this->numvers, 200);
    }

    public function upd_entrega() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        // if(isset($cpr,$agn,$prc,$ctr,$mot,$jst,$est,$obs,$lat,$lng)) {
        if(isset($cpr,$agn,$prc,$ctr,$mot,$jst,$est,$obs,$lat,$lng,$fecha,$imagenes)) {
            // nuevo
            $qry_params = [ $agn, $prc, $ctr, $mot, $jst, $est, $fecha, $obs, $cpr, $lat, $lng, $imagenes ];
            DB::statement("call sp_app_update_confirma_entrega (?,?,?,?,?,?,str_to_date(?,'%Y-%m-%d'),?,?,?,?,?,@out)", $qry_params);
file_put_contents("/var/www/files/app/api_log.txt", "[" . date("d/m/Y H:i") . "]\tupd_entrega: " . print_r($qry_params, true), FILE_APPEND | LOCK_EX);
            $salida = DB::select("select @out salida")[0]->salida;
            list($ocodigo, $omensaje) = explode("|", $salida);
            if (strcmp($ocodigo, "1") == 0) return Response::json([
                "result" => "error",
                "rqid" => 402,
                "message" => $omensaje
            ]);
            $telefono = DB::connection($conn)->table("datos_adicionales")
                ->where("CodAutogen", $agn)
                ->where("NroProceso", $prc)
                ->where("NroControl", $ctr)
                ->select("NroTelefDesti as telefono")
                ->get();
            $telefono = count($telefono) > 0 ? ("51" . $telefono[0]->telefono) : "";
            // enviar el correo electrónico
            $datos = DB::select("select
                    dr.nomdestinatario nombre,
                    f_obtiene_email_destinatario(enxp.codautogen, enxp.nroproceso, enxp.nrocontrol) as email,
                    concat(base_url_tracking,enxp.codautogen,'-',enxp.nroproceso,'-',enxp.nrocontrol) as url,
                    da.CodCuentaCliente as pedido,
                    substring_index(cl.NomCliente, ' ', 1) as empresa
                from envios_x_proceso enxp
                    left join direcciones dr on dr.coddestinatario = enxp.coddestinatario
                    left join datos_adicionales da on da.codautogen = enxp.codautogen and da.nroproceso = enxp.nroproceso and da.nrocontrol = enxp.nrocontrol
                    left join guias_ingreso gi on gi.codautogen = da.codautogen
                    left join areas_clientes ac on ac.CodAreaCliente =  gi.CodAreaClie
                    left join clientes cl on cl.codcliente = ac.codcliente,
                    empresas
                where enxp.codautogen = ?
                    and enxp.nroproceso = ?
                    and enxp.nrocontrol = ?
                    and cl.FlgEnviarMailAppResultado = 'S'
                    and f_obtiene_email_destinatario(enxp.codautogen, enxp.nroproceso, enxp.nrocontrol) <> '0'
                    and empresas.codempresa = 1", [$agn, $prc, $ctr]);
            if (count($datos) > 0) {
                $datos = $datos[0];
                $email = $datos->email;
                $datos = [
                    "key" => $payload,
                    "nombre" => $datos->nombre,
                    "empresa" => $datos->empresa,
                    "pedido" => $datos->pedido,
                    "url" => $datos->url
                ];
file_put_contents("/var/www/files/app/api_log_sms.txt", "[" . date("d/m/Y H:i") . "]\datos: " . print_r($datos, true), FILE_APPEND | LOCK_EX);
                // envia el email
                \Mail::send("usmailer.mail-natura-envio", $datos, function($message) use($email) {
                    $message->from(env("MAIL_FROM_ADDRESS"), env("MAIL_FROM_NAME"));
                    $message->to($email)->subject("🚨 Entrega de pedido AVON/NATURA: Pedido entregado");
                    $message->replyTo("brayanhuaman@unionstar.com.pe");
                });
                // graba en el log
                DB::table("envios_x_proceso_msg")->insert([
                    "CodAutogen" => $agn,
                    "NroProceso" => $prc,
                    "NroControl" => $ctr,
                    "CorrSeguim" => 1,
                    "TipoMsg" => "MAIL",
                    "DestinoMsg" => "DESTINATARIO",
                    "Mail" => $email,
                    "Observaciones" => "Mail enviado a $email",
                    "FlgAutomatico" => "S",
                ]);
            }
            // devolver respuesta
            return Response::json([
                "result" => "success",
                "rqid" => 402,
                "data" => [
                    "motivo" => $mot,
                    "justifica" => $jst,
                    "numero" => $telefono
                ],
                "message" => $omensaje
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
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($cpr,$agn,$prc,$ctr,$nom,$base64)) {
            //arma la pinche ruta
            $count = DB::connection($conn)->table("guias_ingreso as gi")
                ->join("tipos_documentos as tdc", "gi.CodTipDocu", "=", "tdc.CodTipDocu")
                ->where("gi.codautogen", $agn)
                ->count();
            if($count > 0) {
                $vPath = DB::connection($conn)->table("guias_ingreso as gi")
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
                DB::connection($conn)->table("envios_x_proceso_img")->insert([
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

    public function ls_datos_adicionales() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($agn, $proc, $ctrl)) {
            $data = DB::connection($conn)->select("select
                    ifnull(NomEmpresaDesti,'x') as empresa,
                    ifnull(NroTelefDesti,'x') as telefonos,
                    ifnull(IdeDestinatario,'x') as docid,
                    ifnull(email,'x') as mail,
                    ifnull(CodCuentaCliente,'x') as codigo1,
                    ifnull(NroDocuCliente,'x') as codigo2,
                    ifnull(NroComprobante,'x') as codigo3,
                    ifnull(Sector,'x') as codigo4,
                    ifnull(GrupoCliente,'x') as codigo5,
                    ifnull(Custom1,'x') as codigo6,
                    ifnull(Custom1,'x') as codigo7,
                    ifnull(Custom3,'x') as codigo8,
                    ifnull(Custom4,'x') as codigo9,
                    ifnull(Custom5,'x') as codigo10,
                    ifnull(Custom6,'x') as codigo11
                from datos_adicionales 
                where codautogen = ? and nroproceso = ? and nrocontrol = ?", [$agn, $proc, $ctrl]);
            $data = count($data) > 0 ? $data[0] : [
                "empresa" => "Sin datos adicionales",
                "telefonos" => "x",
                "docid" => "x",
                "mail" => "x",
                "codigo1" => "x",
                "codigo2" => "x",
                "codigo3" => "x",
                "codigo4" => "x",
                "codigo5" => "x",
                "codigo6" => "x",
                "codigo7" => "x",
                "codigo8" => "x",
                "codigo9" => "x",
                "codigo10" => "x",
                "codigo11" => "x",
            ];
            return Response::json([
                "result" => "success",
                "rqid" => 404,
                "data" => [
                    "datos" => $data
                ]
            ]);
        }
        return Response::json([
            "result" => "error",
            "rqid" => 404,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function ls_galeria_paquete() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($origen, $destino, $periodo, $manifiesto)) {
            $lista = DB::connection($conn)->table("manifiesto_img as mimg")
                ->leftJoin("tipos_documentos as td", "td.CodTipDocu", "=", DB::connection($conn)->raw("'2'"))
                ->where("mimg.CodAgenciaOrigen", $origen)
                ->where("mimg.CodAgenciaDestino", $destino)
                ->where("mimg.CodPeriodo", $periodo)
                ->where("mimg.NroManifiesto", $manifiesto)
                ->select(
                    "mimg.DtmRegistra as fecha",
                    DB::connection($conn)->raw("ifnull(td.nomcarpetaimgActual,'MANIF') as path"),
                    "mimg.NomImagen as nombre"
                )
                ->get();
            foreach ($lista as $key => $row) {
                $imgPath = implode(DIRECTORY_SEPARATOR, [env("APP_STORAGE_PATH"), $row->path, ($periodo . $origen . $destino), $row->nombre]);
                if(file_exists($imgPath)) $lista[$key]->path = base64_encode(file_get_contents($imgPath));
            }
            return Response::json([
                "result" => "success",
                "rqid" => 405,
                "data" => [
                    "lista" => $lista
                ]
            ]);
        }
        return Response::json([
            "result" => "error",
            "rqid" => 405,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function sv_imagen() {
        //APP_STORAGE_PATH
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($origen, $destino, $periodo, $manifiesto, $base64, $usuario)) {
            $nomImg = DB::connection($conn)->table("manifiesto_img")
                ->where("CodAgenciaOrigen", $origen)
                ->where("CodAgenciaDestino", $destino)
                ->where("CodPeriodo", $periodo)
                ->where("NroManifiesto", $manifiesto)
                ->max("NroCorr");
            $corr = $nomImg ? ($nomImg + 1) : 1;
            $nomImg = $manifiesto . "-" . $corr . ".jpg";
            $raiz = DB::connection($conn)->table("tipos_documentos")
                ->where("CodTipDocu", 2)
                ->select("nomcarpetaimgActual as nombre")
                ->first();
            $carpeta = $periodo . $origen . $destino;
            $raiz = $raiz->nombre ? $raiz->nombre : "MANIF";
            $imgPath = implode(DIRECTORY_SEPARATOR, [env("APP_STORAGE_PATH"), $raiz, $carpeta]);
            //aqui permisos
            if(!file_exists(env("APP_STORAGE_PATH") . DIRECTORY_SEPARATOR . $raiz)) {
                chmod(env("APP_STORAGE_PATH"), 0777);
                mkdir(env("APP_STORAGE_PATH") . DIRECTORY_SEPARATOR . $raiz, true, 0777);
            }
            if(!file_exists(env("APP_STORAGE_PATH") . DIRECTORY_SEPARATOR . $raiz . DIRECTORY_SEPARATOR . $carpeta)) {
                chmod(env("APP_STORAGE_PATH") . DIRECTORY_SEPARATOR . $raiz, 0777);
                mkdir(env("APP_STORAGE_PATH") . DIRECTORY_SEPARATOR . $raiz . DIRECTORY_SEPARATOR . $carpeta, true, 0777);
            }
            chmod(env("APP_STORAGE_PATH") . DIRECTORY_SEPARATOR . $raiz . DIRECTORY_SEPARATOR . $carpeta, 0777);
            //
            //@mkdir($imgPath, true, 0777);
            $imgFile = $imgPath . DIRECTORY_SEPARATOR . $nomImg;
            //guarda la imagen
            $b64decoded = base64_decode($base64);
            file_put_contents($imgFile, $b64decoded);
            //guarda en la bd
            DB::connection($conn)->table("manifiesto_img")->insert([
                "CodAgenciaOrigen" => $origen,
                "CodAgenciaDestino" => $destino,
                "CodPeriodo" => $periodo,
                "NroManifiesto" => $manifiesto,
                "NroCorr" => $corr,
                "NomImagen" => $nomImg,
                "UserRegistra" => $usuario,
                "DtmRegistra" => date("Y-m-d H:i:s")
            ]);
            return Response::json([
                "result" => "success",
                "rqid" => 406
            ]);
        }
        return Response::json([
            "result" => "error",
            "rqid" => 406,
            "message" => "Parámetros incorrectos"
        ]);
    }

    public function dt_imagen() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($origen, $destino, $periodo, $manifiesto, $nombre)) {
            $raiz = DB::connection($conn)->table("tipos_documentos")
                ->where("CodTipDocu", 2)
                ->select("nomcarpetaimgActual as nombre")
                ->first();
            $raiz = $raiz->nombre ? $raiz->nombre : "MANIF";
            $carpeta = $periodo . $origen . $destino;
            $imgPath = implode(DIRECTORY_SEPARATOR, [env("APP_STORAGE_PATH"), $raiz, $carpeta, $nombre]);
            $b64 = base64_encode(file_get_contents($imgPath));
            return Response::json([
                "result" => "success",
                "rqid" => 407,
                "data" => [
                    "imagen" => $b64
                ]
            ]);
        }
        return Response::json([
            "result" => "error",
            "rqid" => 407,
            "message" => "Parámetros incorrectos"
        ]);
    }

    //21-05-2019

    public function ls_agencias_flete() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($flete)) {
            $ls_agencias = DB::connection($conn)->select("call sp_app_agenciasxflete(?)", [$flete]);
            return Response::json([
                "success" => "true",
                "rqid" => 11,
                "data" => [
                    "agencias" => $ls_agencias
                ]
            ]);
        }
        else {
            return Response::json([
                "success" => "false",
                "rqid" => 11,
                "message" => "Los parámetros ingresados son incorrectos",
                "errcode" => "SY0000"
            ]);
        }
    }

    public function ls_agencias_flete_directo() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($flete, $codigo)) {
            $ls_agencias = DB::connection($conn)->select("call sp_app_agenciasxflete_empresas(?)", [$flete]);
            //genera el codagencia
            $agencia = DB::connection($conn)->table("personal as per")
                ->join("seg_user as usr", "usr.v_codperempresa", "=", "per.codpersonal")
                ->where("usr.v_codusuario", $codigo)
                ->select("per.codagencia as agencia")
                ->get();
            if(count($agencia) > 0) {
                return Response::json([
                    "success" => "true",
                    "rqid" => 11,
                    "data" => [
                        "agencias" => $ls_agencias,
                        "agencia" => $agencia[0]->agencia
                    ]
                ]);
            }
            return Response::json([
                "success" => "false",
                "rqid" => 11,
                "message" => "ls_agencias_flete_directo: No hay agencia",
                "errcode" => "SY0000"
            ]);
        }
        return Response::json([
            "success" => "false",
            "rqid" => 11,
            "message" => "ls_agencias_flete_directo: Los parámetros ingresados son incorrectos",
            "errcode" => "SY0000"
        ]);
    }

    public function ls_agencias_flete_empresa() {
        //
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($flete)) {
            $ls_agencias = DB::connection($conn)->select("call sp_app_agenciasxflete_directo(?)", [$flete]);
            return Response::json([
                "success" => "true",
                "rqid" => 11,
                "data" => [
                    "agencias" => $ls_agencias
                ]
            ]);
        }
        return Response::json([
            "success" => "false",
            "rqid" => 11,
            "message" => "ls_agencias_flete_empresa: Los parámetros ingresados son incorrectos",
            "errcode" => "SY0000"
        ]);
    }

    public function ls_agentes_agencia() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($agencia)) {
            $ls_agentes = DB::connection($conn)->select("call sp_app_agentesxagencias(?)", [$agencia]);
            return Response::json([
                "success" => "true",
                "rqid" => 12,
                "data" => [
                    "agentes" => $ls_agentes
                ]
            ]);
        }
        else {
            return Response::json([
                "success" => "false",
                "rqid" => 12,
                "message" => "ls_agentes_agencia: Los parámetros ingresados son incorrectos",
                "errcode" => "SY0000"
            ]);
        }
    }

    public function ls_codigo_barras() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($barcode, $agencia)) {
            $out = DB::connection($conn)->select("select f_app_lee_codigobarras(?) as salida", [$barcode]);
            $out = $out[0]->salida;
            if(strcmp($out, "0") != 0) {
                //carga el autogen, proceso y control
                list($autogen, $proceso, $control) = explode("|", $out);
                //validacion nueva
                /*
                select dtinicio into ld_paradespachar from ,
                where  = xxx and nroproceso = yy and `
                and servicios.`codclaseservicio` = 'r'
                */
                $validacion = DB::connection($conn)->table("guias_ing_servproc as gis")
                    ->join("servicios as svs", "svs.codservicio", "=", "gis.CodServicio")
                    ->where("gis.codautogen", $autogen)
                    ->where("gis.nroproceso", $proceso)
                    ->where("svs.CodClaseServicio", "R")
                    ->select(DB::connection($conn)->raw("if(dtinicio < date_add(now(), interval -1 day),date_format(dtinicio,'%d-%m-%Y'),'0') as salida"))
                    ->first();
                if($validacion && $validacion->salida == '0') {
                    //validar la lectura
                    $datos = DB::connection($conn)->select("select f_app_manif_valida_codbarra(?,?,?,?) as salida", [$autogen, $proceso, $control, $agencia]);
                    list($result, $msg) = explode("||", $datos[0]->salida);
                    if(strcmp($result, "1") == 0) {
                        $registro = DB::connection($conn)->select("call sp_app_manif_loc_datagrid(?,?,?)", [$autogen, $proceso, $control]);
                        return Response::json([
                            "success" => "true",
                            "rqid" => 13,
                            "data" => [
                                "autogen" => $autogen,
                                "proceso" => $proceso,
                                "control" => $control,
                                "item" => $registro[0]
                            ]
                        ]);
                    }
                    return Response::json([
                        "success" => false,
                        "rqid" => 13,
                        "message" => "Error: " . $msg
                    ]);
                }
                //
                return Response::json([
                    "success" => false,
                    "rqid" => 13,
                    "message" => ($validacion ? ("El despacho de esta guía está programado para el día " . $validacion->salida) : 'Error al leer el código de barras')
                ]);
            }
            return Response::json([
                "success" => false,
                "rqid" => 13,
                "message" => "El código de barras es incorrecto"
            ]);
        }
        return Response::json([
            "success" => false,
            "rqid" => 13,
            "message" => "Los parámetros ingresados son incorrectos",
            "errcode" => "SY0000"
        ]);
    }

    public function sv_genera_manifiesto() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($origen, $destino, $usuario, $agente, $embala, $flete, $keys)) {
            DB::connection($conn)->table("maniftmp_det")
                ->where("v_codUser", $usuario)
                ->delete();
            $arr_to_insert = [];
            foreach ($keys as $idx => $row) {
                list($autogen, $proceso, $control, $cantidad, $peso) = explode("|", $row);
                $arr_to_insert[] = [
                    "i_CodAutogen" => $autogen,
                    "i_NroProceso" => $proceso,
                    "i_NroControl" => $control,
                    "c_CodOrigen" => $origen,
                    "c_CodDestino" => $destino,
                    "v_codUser" => $usuario,
                    "v_codagente" => $agente,
                    "c_TipoFlete" => $flete,
                    "CanEnvios" => $cantidad,
                    "CanPeso" => $peso
                ];
            }
            DB::connection($conn)->table("maniftmp_det")->insert($arr_to_insert);
            //validacion de nelson
            $validacion = DB::connection($conn)->select("call sp_app_manif_loc_verifica_faltantes(?,?,?,?)", [$origen, $destino, $agente, date("Y-m-d")]);
//file_put_contents(env("APP_STORAGE") . DIRECTORY_SEPARATOR . date("YmdHis") . ".txt", print_r($validacion, true) . "\n\n" . print_r([$origen, $destino, $agente, date("Y-m-d")], true));
            $valivalivali = "";
            foreach($validacion[0] as $valid) {
                $valivalivali = $valid;
            }
            $validacion = $valivalivali;
            if($validacion == "0") {
                $manifiesto = DB::connection($conn)->select("call sp_app_manif_loc_generar(?)", [$usuario]);
                $manimanimani = "";
                foreach($manifiesto[0] as $manif) {
                    $manimanimani = $manif;
                }
                $manifiesto = $manimanimani;
                $periodo = date("Y");
                $cabecera = DB::connection($conn)->select("select
                       agencias_a.DesAgencia origen,
                       agencias_b.DesAgencia destino,
                       manifiesto.NroManifiesto,
                       date_format(manifiesto.DtmManifiesto,'%d/%m/%Y') as DtmManifiesto,
                       manifiesto.TipoTrans,
                       personal.NomPersonal
                    from manifiesto
                       left outer join personal on manifiesto.codagente = personal.CodPersonal,
                       agencias agencias_a,
                       agencias agencias_b
                    where
                       manifiesto.CodAgenciaOrigen = agencias_a.CodAgencia
                       and manifiesto.CodAgenciaDestino = agencias_b.CodAgencia
                       and manifiesto.CodAgenciaOrigen = ?
                       and manifiesto.CodAgenciaDestino = ?
                       and manifiesto.CodPeriodo = ?
                       and manifiesto.NroManifiesto = ?", [$origen, $destino, $periodo, $manifiesto])[0];
                $detalle = DB::connection($conn)->select("select
                       tiposdoc.AbrTipDocu,
                       gi.CodGuia,
                       areas.AbrAreaCliente,
                       tipoenv.AbrTipoEnvio,
                       gip.CodClaseCarga,
                       manifiesto_det.CanEnvios,
                       manifiesto_det.CanPesoVolum,
                       date_format(gi.DtmGuia,'%d-%m') as DtmGuia,
                       gip.CodClaseServicio,
                       gip.TipoTransFac,
                       gip.NroProceso,
                       gis.CanPeso,
                       tiposerv.AbrServicio
                    from envios_x_proceso e_x_p,
                       guias_ingreso gi,
                       guias_ing_procesos gip,
                       tipos_envios tipoenv,
                       areas_clientes areas,
                       guias_ing_servproc gis,
                       servicios tiposerv,
                       tipos_documentos tiposdoc,
                       manifiesto_det
                    where gi.codautogen = e_x_p.codautogen
                       and gip.codautogen = e_x_p.codautogen
                       and gip.nroproceso = e_x_p.nroproceso
                       and gis.codautogen = e_x_p.codautogen
                       and gis.nroproceso = e_x_p.nroproceso
                       and tiposerv.CodClaseServicio = 'R'
                       and e_x_p.CodAgenciaDestino = ?
                       and tipoenv.CodTipoEnvio = gip.CodTipoEnvio
                       and areas.CodAreaCliente = gi.CodAreaClie
                       and tiposerv.CodServicio = gis.CodServicio
                       and tiposdoc.CodTipDocu = gi.CodTipDocu
                       and e_x_p.CodAgenciaDocuRef = ?
                       and e_x_p.NroDocuRef = ?
                       and year(e_x_p.DtmDocuRef) = ?
                       and manifiesto_det.CodAutogen = e_x_p.codautogen
                       and manifiesto_det.nroproceso = e_x_p.nroproceso
                       and manifiesto_det.CodAgenciaOrigen = e_x_p.CodAgenciaDocuRef
                       and manifiesto_det.CodAgenciaDestino = e_x_p.CodAgenciaDestino
                       and manifiesto_det.NroManifiesto = e_x_p.NroDocuRef
                       and CodPeriodoManif = ?
                    group by e_x_p.CodAutogen,e_x_p.NroProceso
                    order by gi.DtmGuia,gip.CodAutogen,gip.NroProceso", [$destino, $origen, $manifiesto, $periodo, $periodo]);
                //
                $arrTipos = [
                    "S" => "Sobres",
                    "P" => "Paquete",
                    "C" => "Caja",
                    "V" => "Valija"
                ];
                $filename = date("YmdHis");
                $file = env("APP_PUBLIC_PATH") . "/pdf/" . $filename . ".pdf";
                $pdf = \PDF::loadView("pdf.manifiesto", ["cabecera" => $cabecera, "detalle" => $detalle, "tipos" => $arrTipos])
                    ->setPaper("a4", "portrait");
                    $pdf->save($file);
                //
                return Response::json([
                    "success" => "true",
                    "data" => [
                        "filename" => $filename,
                        "manifiesto" => (int) $manifiesto
                    ],
                    "rqid" => 14
                ]);
            }
            //
            return Response::json([
                "success" => "true",
                "rqid" => 14,
                "data" => [
                    "alert" => "S",
                    "message" => $validacion
                ]
            ]);
            //
        }
        return Response::json([
            "success" => false,
            "rqid" => 14,
            "message" => "Los parámetros ingresados son incorrectos",
            "errcode" => "SY0000"
        ]);
    }

    public function sv_genera_manifiesto_alv() {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        if(isset($origen, $destino, $usuario, $agente, $embala, $flete, $keys)) {
            DB::connection($conn)->table("maniftmp_det")
                ->where("v_codUser", $usuario)
                ->delete();
            $arr_to_insert = [];
            foreach ($keys as $idx => $row) {
                list($autogen, $proceso, $control, $cantidad, $peso) = explode("|", $row);
                $arr_to_insert[] = [
                    "i_CodAutogen" => $autogen,
                    "i_NroProceso" => $proceso,
                    "i_NroControl" => $control,
                    "c_CodOrigen" => $origen,
                    "c_CodDestino" => $destino,
                    "v_codUser" => $usuario,
                    "v_codagente" => $agente,
                    "c_TipoFlete" => $flete,
                    "CanEnvios" => $cantidad,
                    "CanPeso" => $peso
                ];
            }
            DB::connection($conn)->table("maniftmp_det")->insert($arr_to_insert);
            //guardar
            $manifiesto = DB::connection($conn)->select("call sp_app_manif_loc_generar(?)", [$usuario]);
            $manimanimani = "";
            foreach($manifiesto[0] as $manif) {
                $manimanimani = $manif;
            }
            $manifiesto = $manimanimani;
            $periodo = date("Y");
            $cabecera = DB::connection($conn)->select("select
                   agencias_a.DesAgencia origen,
                   agencias_b.DesAgencia destino,
                   manifiesto.NroManifiesto,
                   date_format(manifiesto.DtmManifiesto,'%d/%m/%Y') as DtmManifiesto,
                   manifiesto.TipoTrans,
                   personal.NomPersonal
                from manifiesto
                   left outer join personal on manifiesto.codagente = personal.CodPersonal,
                   agencias agencias_a,
                   agencias agencias_b
                where
                   manifiesto.CodAgenciaOrigen = agencias_a.CodAgencia
                   and manifiesto.CodAgenciaDestino = agencias_b.CodAgencia
                   and manifiesto.CodAgenciaOrigen = ?
                   and manifiesto.CodAgenciaDestino = ?
                   and manifiesto.CodPeriodo = ?
                   and manifiesto.NroManifiesto = ?", [$origen, $destino, $periodo, $manifiesto])[0];
            $detalle = DB::connection($conn)->select("select
                   tiposdoc.AbrTipDocu,
                   gi.CodGuia,
                   areas.AbrAreaCliente,
                   tipoenv.AbrTipoEnvio,
                   gip.CodClaseCarga,
                   manifiesto_det.CanEnvios,
                   manifiesto_det.CanPesoVolum,
                   date_format(gi.DtmGuia,'%d-%m') as DtmGuia,
                   gip.CodClaseServicio,
                   gip.TipoTransFac,
                   gip.NroProceso,
                   gis.CanPeso,
                   tiposerv.AbrServicio
                from envios_x_proceso e_x_p,
                   guias_ingreso gi,
                   guias_ing_procesos gip,
                   tipos_envios tipoenv,
                   areas_clientes areas,
                   guias_ing_servproc gis,
                   servicios tiposerv,
                   tipos_documentos tiposdoc,
                   manifiesto_det
                where gi.codautogen = e_x_p.codautogen
                   and gip.codautogen = e_x_p.codautogen
                   and gip.nroproceso = e_x_p.nroproceso
                   and gis.codautogen = e_x_p.codautogen
                   and gis.nroproceso = e_x_p.nroproceso
                   and tiposerv.CodClaseServicio = 'R'
                   and e_x_p.CodAgenciaDestino = ?
                   and tipoenv.CodTipoEnvio = gip.CodTipoEnvio
                   and areas.CodAreaCliente = gi.CodAreaClie
                   and tiposerv.CodServicio = gis.CodServicio
                   and tiposdoc.CodTipDocu = gi.CodTipDocu
                   and e_x_p.CodAgenciaDocuRef = ?
                   and e_x_p.NroDocuRef = ?
                   and year(e_x_p.DtmDocuRef) = ?
                   and manifiesto_det.CodAutogen = e_x_p.codautogen
                   and manifiesto_det.nroproceso = e_x_p.nroproceso
                   and manifiesto_det.CodAgenciaOrigen = e_x_p.CodAgenciaDocuRef
                   and manifiesto_det.CodAgenciaDestino = e_x_p.CodAgenciaDestino
                   and manifiesto_det.NroManifiesto = e_x_p.NroDocuRef
                   and CodPeriodoManif = ?
                group by e_x_p.CodAutogen,e_x_p.NroProceso
                order by gi.DtmGuia,gip.CodAutogen,gip.NroProceso", [$destino, $origen, $manifiesto, $periodo, $periodo]);
            //
            $arrTipos = [
                "S" => "Sobres",
                "P" => "Paquete",
                "C" => "Caja",
                "V" => "Valija"
            ];
            $filename = date("YmdHis");
            $file = env("APP_PUBLIC_PATH") . "/pdf/" . $filename . ".pdf";
            $pdf = \PDF::loadView("pdf.manifiesto", ["cabecera" => $cabecera, "detalle" => $detalle, "tipos" => $arrTipos])
                ->setPaper("a4", "portrait");
                $pdf->save($file);
            //
            return Response::json([
                "success" => "true",
                "data" => [
                    "filename" => $filename,
                    "manifiesto" => (int) $manifiesto
                ],
                "rqid" => 14
            ]);
        }
        return Response::json([
            "success" => false,
            "rqid" => 14,
            "message" => "Los parámetros ingresados son incorrectos",
            "errcode" => "SY0000"
        ]);
    }

    public function print_manifiesto($origen, $destino, $periodo, $manifiesto, $nombre) {
    	extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        $val = DB::connection($conn)->statement("call sp_alf(?)", ["holi"]);
        return "<p>" . $val . "</p>";
        /*return Response::make(file_get_contents($file), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="manifiesto.pdf"'
        ]);*/
    }

    public function ls_personal_embala() {
    	extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        $personal = DB::connection($conn)->table("personal")
            ->where("flgsit", "A")
            ->where("codareaempresa", 16)
            ->select("codpersonal as value", "nompersonal as text")
            ->orderBy("nompersonal", "asc")
            ->get();
        return Response::json([
            "success" => "true",
            "rqid" => 15,
            "data" => [
                "embaladores" => $personal
            ]
        ]);
    }

    public function ls_describe() {
    	extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
        $out = DB::connection($conn)->select("select column_name
            from information_schema.columns
            where table_schema = 'expressges2016' and table_name = 'maniftmp_det'");
        return print_r($out, true);
    }

    public function envio_whatsapp() {
        extract(Request::input());
        if (isset($keys)) {
            $conn = isset($conn) ? $conn : $this->conn;
            $keys = explode(",", $keys);
            foreach($keys as $key) {
                list($autogen, $proceso, $control, $numero, $destinatario, $empresa) = explode("|", $key);
                // enviar el sms
                $nnumero = "51" . $numero;
                $pedido = implode("-", [$autogen, $proceso, $control]);
                // lanza el curl
                $token = DB::select("select TokenMsgWassap token from empresas where CodEmpresa = ?", [1])[0]->token;
                $ch = curl_init("https://api.messengerpeople.dev/messages");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/vnd.messengerpeople.v1+json",
                    "Accept: application/vnd.messengerpeople.v1+json",
                    "Authorization: Bearer " . $token,
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    "identifier" => implode(":", [env("WABA_UUID"), $nnumero]),
                    "payload" => [
                        "type" => "template",
                        "template" => [
                            "type" => "notification",
                            "notification" => [
                                "name" => env("WABA_TEMPLATE"),
                                "language" => "es",
                                "components" => [
                                    [
                                        "type" => "body",
                                        "parameters" => [
                                            ["type" => "text", "text" => $destinatario],
                                            ["type" => "text", "text" => $empresa]
                                        ]
                                    ],
                                    [
                                        "type" => "button",
                                        "sub_type" => "url",
                                        "index" => "0", 
                                        "parameters" => [
                                            ["type" => "text", "text" => $pedido]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]));
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response);
                if (!isset($response->error)) {
                    // WHATSAP' and msg.DestinoMsg = 'DESTINATARIO
                    DB::connection($conn)->statement("call sp_registra_envio_whatsapp(?,?,?,?,?,?)", [$autogen, $proceso, $control, "WHATSAP", "DESTINATARIO", $numero]);
                }
            }
            return response()->json([
                "result" => "success",
                "rqid" => 801
            ]);
        }
        $data = [
            "result" => "error",
            "rqid" => 801,
            "message" => "Los parámetros ingresados son incorrectos"
        ];
    	return response()->json($data, 200);
    }

    public function envio_whatsapp_rango() {
        extract(Request::input());
        if (isset($keys, $desde, $hasta)) {
            $conn = isset($conn) ? $conn : $this->conn;
            $keys = explode(",", $keys);
            foreach($keys as $pos => $key) {
                list($autogen, $proceso, $control, $numero, $destinatario, $empresa) = explode("|", $key);
                // enviar el sms
                $nnumero = "51" . $numero;
                $empresa = "*" . $empresa . "*";
                $enlace = "https://wan2.unionstar.com.pe/tracking-pedidos/" . implode("-", [$autogen, $proceso, $control]);
                // lanza el curl
                $token = DB::select("select TokenMsgWassap token from empresas where CodEmpresa = ?", [1])[0]->token;
                $ch = curl_init("https://api.messengerpeople.dev/messages");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/vnd.messengerpeople.v1+json",
                    "Accept: application/vnd.messengerpeople.v1+json",
                    "Authorization: Bearer " . $token,
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    "identifier" => implode(":", [env("WABA_UUID"), $nnumero]),
                    "payload" => [
                        "type" => "template",
                        "template" => [
                            "type" => "notification",
                            "notification" => [
                                "name" => env("WABA_TEMPLATE_RANGO"),
                                "language" => "es",
                                "components" => [
                                    [
                                        "type" => "body",
                                        "parameters" => [
                                            ["type" => "text", "text" => $destinatario],
                                            ["type" => "text", "text" => $empresa],
                                            ["type" => "text", "text" => $desde],
                                            ["type" => "text", "text" => $hasta],
                                            ["type" => "text", "text" => $enlace]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                ]));
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response);
                if (!isset($response->error)) {
                    // WHATSAP' and msg.DestinoMsg = 'DESTINATARIO
                    DB::connection($conn)->statement("call sp_registra_envio_whatsapp(?,?,?,?,?,?)", [$autogen, $proceso, $control, "WHATSAP", "DESTINATARIO", $numero]);
                }
            }
            return response()->json([
                "result" => "success",
                "rqid" => 806
            ]);
        }
        $data = [
            "result" => "error",
            "rqid" => 806,
            "message" => "Los parámetros ingresados son incorrectos"
        ];
    	return response()->json($data, 200);
    }

    public function ls_recojos_guia() {
        extract(Request::input());
        if (isset($fecha, $usuario)) {
            $rows = DB::select("call sp_app_confirma_recojos_guias_list(?,?)", [$fecha, $usuario]);
            $data = [
                "result" => "success",
                "rqid" => 802,
                "data" => $rows
            ];
            return response()->json($data, 200);
        }
        return Response::json([
            "result" => "error",
            "rqid" => 802,
            "message" => "Los parámetros ingresados son incorrectos",
            "errcode" => "SY0000"
        ]);
    }

    public function sv_confirma_recojos_guia() {
        extract(Request::input());
        if (isset($keys, $usuario)) {
            $vkeys = explode("@", $keys);
$out = "";
            foreach ($vkeys as $key) {
                list($autogen, $proceso, $control, $marca) = explode("|", $key);
                DB::statement("call sp_app_confirma_recojo_agente(?,?,?,?,?)", [$autogen, $proceso, $control, $usuario, $marca]);
$out .= ("\n" . $autogen . "-" . $proceso . "-" . $control);
            }
if (file_exists("/var/www/ustar/keys.txt")) {
    unlink("/var/www/ustar/keys.txt");
}
file_put_contents("/var/www/ustar/keys.txt", $out);
            return response()->json([
                "result" => "success",
                "rqid" => 803
            ], 200);
        }
        return Response::json([
            "result" => "error",
            "rqid" => 803,
            "message" => "Los parámetros ingresados son incorrectos",
            "errcode" => "SY0000"
        ]);
    }

    public function registra_llamada () {
        extract(Request::input());
        if (isset($repartidor, $autogen, $proceso, $control, $guia, $destinatario, $telefono)) {
            $guia = explode(" ", $guia)[0];
            DB::statement("call sp_webapp_registra_llamada(?,?,?,?,?,?,?,@id,@mensaje)", [$autogen, $proceso, $control, $repartidor, $guia, $destinatario, $telefono]);
            $out = DB::select("select @id id, @mensaje mensaje");
            return response()->json([
                "result" => "success",
                "data" => ["id" => $out[0]->id],
                "rqid" => 804
            ], 200);
        }
        return Response::json([
            "result" => "error",
            "rqid" => 804,
            "message" => "Los parámetros ingresados son incorrectos",
            "errcode" => "SY0000"
        ]);
    }

    public function registra_resultado_llamada () {
        extract(Request::input());
        if (isset($id, $resultado)) {
            DB::statement("call sp_webapp_resultado_llamada(?,?)", [$id, $resultado]);
            return response()->json([
                "result" => "success",
                "rqid" => 805
            ], 200);
        }
        return Response::json([
            "result" => "error",
            "rqid" => 805,
            "message" => "Los parámetros ingresados son incorrectos",
            "errcode" => "SY0000"
        ]);
    }

    public function validar_barcode () {
        extract(Request::input());
    	$conn = isset($conn) ? $conn : $this->conn;
file_put_contents("/var/www/files/log.txt", print_r(compact("codigo", "barcode"), true), FILE_APPEND + LOCK_EX);
        if (isset($codigo, $barcode)) {
            $all = isset($all) ? $all : "S";
            $descargos = DB::connection($conn)->select("call sp_app_entrega_barcode(?,?,?)", [$codigo, $all, $barcode]);
            if (count($descargos) == 0) {
                return response()->json([
                    "result" => "error",
                    "rqid" => 807,
                    "message" => "Código de barras incorrecto",
                    "errcode" => "SY0000",
                ], 200);
            }
            return Response::json([
                "result" => "success",
                "rqid" => 807,
                "descargo" => $descargos[0]
            ]);
        }
        /*
        
        */
        /*
        $descargo = new \stdClass();
            $descargo->orig = 102;
            $descargo->periodo = 2021;
            $descargo->NumDespacho = 706;
            $descargo->Fecdespacho = "2021-12-06";
            $descargo->Nrobultos = 1;
            $descargo->Cliente = "LPV - PRODUCTOS INDIVIDUALES";
            $descargo->DiceContener = "BASE 1 - ENVIO DE CARTAS";
            $descargo->CodGuiaCliente = "null";
            $descargo->Servicio = "Mens.Nac. 96 Horas";
            $descargo->autogen = 1020448795;
            $descargo->nro = 7196;
            $descargo->proc = 1;
            $descargo->esweb = "P";
            $descargo->esenvio = "P";
            $descargo->guia = "025-9285-1-7196 del 06/12";
            $descargo->destinatario = "SEVILLANO SALVADOR PAULINO";
            $descargo->direccion = "ATAHUALPA N 985 EL PORVENIR";
            $descargo->abrcode = "EL PORVENIR";
            $descargo->motivo = "Pendiente";
            $descargo->fecingreso = "2021-12-06";
            $descargo->msgenviado = "N";
            $descargo->fono = "-";
            $descargo->nombre_abreviado = "SEVILLANO";
            $descargo->empresa_abreviado = "LA";
        return response()->json([
            "result" => "success",
            "rqid" => 807,
            "descargo" => $descargo
        ], 200);
        */
file_put_contents("/var/www/files/log.txt", "parámetros inválidos [" . print_r(Request::input(), true) . "]", FILE_APPEND + LOCK_EX);
        return response()->json([
            "result" => "error",
            "rqid" => 807,
            "message" => "Parámetros incorrectos",
            "errcode" => "SY0000",
        ], 200);
        // {"autogen":1020448795,"bultos":1,"cliente":"LPV - PRODUCTOS INDIVIDUALES","contenido":"BASE 1 - ENVIO DE CARTAS","control":7232,"departamento":"FLORENCIA DE MORA","despacho":706,"destinatario":"MARIN ANDRADE ESTEBAN ESAU","direccion":"18 DE MAYO # 1147 FLORENCIA DE MORA","emprwhatsapp":"LA","estadoEnvio":"P","esweb":"P","fechaDespacho":"2021-12-06","fechaIngreso":"2021-12-06","guia":"025-9285-1-7232 del 06/12","guiaCliente":"null","motivo":"Pendiente","msgenviado":"N","nomwhatsapp":"MARIN","periodo":"2021","proceso":1,"servicio":"Mens.Nac. 96 Horas","telefono":"-"}
    }

    public function lista_agencias () {
        extract(Request::input());
        if (isset($fecha, $despachador)) {
            $agencias = DB::select("call sp_app_agencias_despachadas(?,?)", [$fecha, $despachador]);
            return response()->json([
                "result" => "success",
                "agencias" => $agencias,
                "rqid" => 808
            ], 200);
        }
        return response()->json([
            "result" => "error",
            "rqid" => 808,
            "message" => "Parámetros incorrectos",
            "errcode" => "SY0000",
        ], 200);
    }
}