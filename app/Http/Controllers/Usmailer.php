<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Excel;
// use setasign\Fpdi\Fpdi;
use PdfMaker;
use XlsxMaker;

class Usmailer extends Controller {
    
    public function view_upload (Request $request) {
        /*
        $calibri_regular = implode("/", [env("APP_MAILER_FOLDER"), "fonts", "calibri.ttf"]);
        $calibri_bold = implode("/", [env("APP_MAILER_FOLDER"), "fonts", "calibrib.ttf"]);
        $calibri_light = implode("/", [env("APP_MAILER_FOLDER"), "fonts", "calibril.ttf"]);
        require getcwd() . "/../vendor/setasign/fpdf/makefont/makefont.php";
        MakeFont($calibri_regular, "cp1252");
        MakeFont($calibri_bold, "cp1252");
        MakeFont($calibri_light, "cp1252");
        */
        $user = Auth::user();
        $tipos = DB::select("select tp_envio value, de_nombre text from mailer_envios_cliente where i_CodCliente = ?", [$user->i_CodCliente]);
        $arrData = [
            "usuario" => $user,
            "menu" => 6,
            "opcion" => "Mailing > Carga de envíos",
            "tipos" => $tipos
        ];
        return view("intranet.mailer_upload")->with($arrData);
    }
    
    public function view_reporte (Request $request) {
        $user = Auth::user();
        $arrData = [
            "usuario" => $user,
            "menu" => 6,
            "opcion" => "Mailing > Carga de envíos"
        ];
        return view("intranet.mailer_reporte")->with($arrData);
    }

    public function upload_xlsx (Request $request) {
        set_time_limit(0);
        $usuario = Auth::user();
        $xlsx = $request->file("xlsx");
        $tipo_envio = $request->input("tipo");
        $nombre = $xlsx->getClientOriginalName();
        // mover el archivo a la carpeta temporal
        $destinationPath = implode("/", [env("APP_MAILER_FOLDER"), "temp"]);
        $xlsx->move($destinationPath, $xlsx->getClientOriginalName());
        // ahora debo leer el archivo
        $xlsxpath = $destinationPath . "/" . $xlsx->getClientOriginalName();
        $datos = Excel::load($xlsxpath)->get();
        $resultados = [];
        $num_envios = 0;
        // registra en la base de datos
        $codusuario = $usuario->user_id;
        $tpenvio = "68";
        // cuenta los envíos
        $numfilas = 0;
        foreach ($datos as $fila) {
            if (strcmp($fila->envio_electronico_o_fisico, "VIRTUAL") == 0) $numfilas++;
        }
        //
        $optdatos = "";
        $observaciones = "Cargado por " . $usuario->v_Codusuario . ", el " . date("d/m/Y") . " a las " . date("H:i");
        $result = DB::select("select fn_genera_guiaingreso_mailing(?,?,?,?,?) as datos", [$codusuario, $tpenvio, $numfilas, $optdatos, $observaciones])[0]->datos;
        list($codigo, $mensaje) = explode("@", $result);
        $codigo = (int) $codigo;
        if ($codigo == 1) {
            $error = $mensaje;
            return response()->json(compact("error"), 200);
        }
        $guia = $mensaje;
        //
        foreach ($datos as $fila) {
            if (strcmp($fila->envio_electronico_o_fisico,"VIRTUAL") == 0) {
                if (strcmp($tipo_envio,"C") == 0) {
                    $output_carta = PdfMaker::CartaCliente($fila, $guia);
                    $output_certificado_free = PdfMaker::CertificadoClienteLibre($fila, $guia);
                    $output_certificado = PdfMaker::CertificadoClienteClave($guia, $output_certificado_free, $fila->dni_responsable_de_pago, $fila->certificado);
                }
                else {
                    $output_carta = PdfMaker::CartaEmpleado($fila, $guia);
                    $output_certificado_free = PdfMaker::CertificadoEmpleadoLibre($fila, $guia);
                    $output_certificado = PdfMaker::CertificadoEmpleadoClave($guia, $output_certificado_free, $fila->dni_responsable_de_pago, $fila->certificado);
                }
                $json = json_encode($fila);
                DB::statement("call sp_mailer_registra_envio (@codigo,@mensaje,?,?,?,?,?,?,?,?,?)", [$fila->dni_responsable_de_pago, $fila->certificado, $fila->email_responsable_de_pago, $json, $output_carta, $output_certificado, $output_certificado_free, $guia, $tipo_envio]);
                $result = DB::select("select @codigo codigo, @mensaje mensaje")[0];
                // devuelve la respuesta
                $resultados[] = [
                    "guia" => $guia,
                    "certificado" => $fila->certificado,
                    "dni" => $fila->dni_responsable_de_pago,
                    "nombre" => $fila->reponsable_de_pago,
                    "email" => $fila->email_responsable_de_pago,
                    "enviado" => $result->codigo,
                    "observaciones" => $result->mensaje
                ];
                $num_envios++;
            }
        }
        DB::statement("call sp_mailer_registra_guia(?,?)", [$guia, $num_envios]);
        return response()->json(compact("resultados"), 200);
    }

    public function upload_xlsx_compartamos (Request $request) {
        set_time_limit(0);
        $usuario = Auth::user();
        $xlsx = $request->file("xlsx");
        $tipo_envio = "F";
        $nombre = $xlsx->getClientOriginalName();
        // mover el archivo a la carpeta temporal
        $destinationPath = implode("/", [env("APP_MAILER_FOLDER"), "temp"]);
        $xlsx->move($destinationPath, $xlsx->getClientOriginalName());
        // ahora debo leer el archivo
        $xlsxpath = $destinationPath . "/" . $xlsx->getClientOriginalName();
        $datos = Excel::load($xlsxpath)->get();
        $resultados = [];
        $num_envios = 0;
        // registra en la base de datos
        $codusuario = $usuario->user_id;
        $tpenvio = "68";
        // cuenta los envíos
        $numfilas = count($datos);
        //
        $optdatos = "";
        $observaciones = "Cargado por " . $usuario->v_Codusuario . ", el " . date("d/m/Y") . " a las " . date("H:i");
        $result = DB::select("select fn_genera_guiaingreso_mailing(?,?,?,?,?) as datos", [$codusuario, $tpenvio, $numfilas, $optdatos, $observaciones])[0]->datos;
        $vresult = explode("@", $result);
        if (count($vresult) < 2) {
            $error = $result;
            return response()->json(compact("error"), 200);
        }
        list($codigo, $mensaje) = $vresult;
        $codigo = (int) $codigo;
        if ($codigo == 1) {
            $error = $mensaje;
            return response()->json(compact("error"), 200);
        }
        $guia = $mensaje;
        //
        foreach ($datos as $fila) {
            $output_carta = PdfMaker::CartaCompartamos($fila, $guia);
            $json = json_encode($fila);
            DB::statement("call sp_mailer_registra_envio (@codigo,@mensaje,?,?,?,?,?,?,?,?,?)", [$fila->n0_envio, $fila->correlativos, $fila->direccion_autoridad, $json, $output_carta, "-", "-", $guia, $tipo_envio]);
            $result = DB::select("select @codigo codigo, @mensaje mensaje")[0];
            // devuelve la respuesta
            $resultados[] = [
                "guia" => $guia,
                "certificado" => $fila->correlativos,
                "dni" => $fila->n0_envio,
                "nombre" => $fila->nombre_de_la_autoridad,
                "email" => $fila->direccion_autoridad,
                "enviado" => $result->codigo,
                "observaciones" => $result->mensaje
            ];
            $num_envios++;
        }
        DB::statement("call sp_mailer_registra_guia(?,?)", [$guia, $num_envios]);
        return response()->json(compact("resultados"), 200);
    }

    public function upload_xlsx_microseguro (Request $request) {
        set_time_limit(0);
        $usuario = Auth::user();
        $xlsx = $request->file("xlsx");
        $tipo_envio = "P";
        $nombre = $xlsx->getClientOriginalName();
        // mover el archivo a la carpeta temporal
        $destinationPath = implode("/", [env("APP_MAILER_FOLDER"), "temp"]);
        $xlsx->move($destinationPath, $xlsx->getClientOriginalName());
        // ahora debo leer el archivo
        $xlsxpath = $destinationPath . "/" . $xlsx->getClientOriginalName();
        $datos = Excel::load($xlsxpath)->get();
        $resultados = [];
        $num_envios = 0;
        // registra en la base de datos
        $codusuario = $usuario->user_id;
        $tpenvio = "68";
        // cuenta los envíos
        $numfilas = count($datos);
        //
        $optdatos = "";
        $observaciones = "Cargado por " . $usuario->v_Codusuario . ", el " . date("d/m/Y") . " a las " . date("H:i");
        $result = DB::select("select fn_genera_guiaingreso_mailing(?,?,?,?,?) as datos", [$codusuario, $tpenvio, $numfilas, $optdatos, $observaciones])[0]->datos;
        list($codigo, $mensaje) = explode("@", $result);
        $codigo = (int) $codigo;
        if ($codigo == 1) {
            $error = $mensaje;
            return response()->json(compact("error"), 200);
        }
        $guia = $mensaje;
        // validar las columnas
        $error = "El formato del archivo recibido es incorrecto";
        if (count($datos) == 0 || !isset($datos[0]->doc_asegurado)) return response()->json(compact("error"));
        foreach ($datos as $fila) {
            if (strcmp($fila->doc_asegurado, "") != 0) {
                $output_carta_free = PdfMaker::MicroSeguro($fila, $guia);
                // $output_carta = PdfMaker::MicroSeguroClave($guia, $output_carta_free, $fila->doc_asegurado, $fila->certificado);
                $json = json_encode($fila);
                DB::statement("call sp_mailer_registra_envio (@codigo,@mensaje,?,?,?,?,?,?,?,?,?)", [$fila->doc_asegurado, $fila->certificado, $fila->correoelectronico, $json, $output_carta_free, "", "", $guia, $tipo_envio]);
                $result = DB::select("select @codigo codigo, @mensaje mensaje")[0];
                // devuelve la respuesta
                $resultados[] = [
                    "guia" => $guia,
                    "certificado" => $fila->certificado,
                    "dni" => $fila->doc_asegurado,
                    "nombre" => $fila->nombre_aseg,
                    "email" => $fila->correoelectronico,
                    "enviado" => $result->codigo,
                    "observaciones" => $result->mensaje
                ];
                $num_envios++;
            }
        }
        DB::statement("call sp_mailer_registra_guia(?,?)", [$guia, $num_envios]);
        return response()->json(compact("resultados"), 200);
    }

    public function upload_xlsx_microseguro_step2 (Request $request) {
        set_time_limit(0);
        $usuario = Auth::user();
        $xlsx = $request->file("xlsx");
        $tipo_envio = "P";
        $nombre = $xlsx->getClientOriginalName();
        // mover el archivo a la carpeta temporal
        $destinationPath = implode("/", [env("APP_MAILER_FOLDER"), "temp"]);
        $xlsx->move($destinationPath, $xlsx->getClientOriginalName());
        // ahora debo leer el archivo
        $xlsxpath = $destinationPath . "/" . $xlsx->getClientOriginalName();
        $datos = Excel::load($xlsxpath)->get();
        $resultados = [];
        $num_envios = 0;
        // registra en la base de datos
        $codusuario = $usuario->user_id;
        $tpenvio = "68";
        // cuenta los envíos
        $numfilas = count($datos);
        //
        $optdatos = "";
        $observaciones = "Cargado por " . $usuario->v_Codusuario . ", el " . date("d/m/Y") . " a las " . date("H:i");
        $result = DB::select("select fn_genera_guiaingreso_mailing(?,?,?,?,?) as datos", [$codusuario, $tpenvio, $numfilas, $optdatos, $observaciones])[0]->datos;
        list($codigo, $mensaje) = explode("@", $result);
        $codigo = (int) $codigo;
        if ($codigo == 1) {
            $error = $mensaje;
            return response()->json(compact("error"), 200);
        }
        $guia = $mensaje;
        // validar las columnas
        $error = "El formato del archivo recibido es incorrecto";
        if ($numfilas == 0 || !isset($datos[0]->doc_asegurado)) return response()->json(compact("error"));
        foreach ($datos as $fila) {
            if (strcmp($fila->doc_asegurado, "") != 0) {
                $output_carta_free = PdfMaker::MicroSeguroStep2($fila, $guia);
                $json = json_encode($fila);
                DB::statement("call sp_mailer_registra_envio (@codigo,@mensaje,?,?,?,?,?,?,?,?,?)", [$fila->doc_asegurado, $fila->certificado, $fila->correoelectronico, $json, $output_carta_free, "", "", $guia, $tipo_envio]);
                $result = DB::select("select @codigo codigo, @mensaje mensaje")[0];
                // devuelve la respuesta
                $resultados[] = [
                    "guia" => $guia,
                    "certificado" => $fila->certificado,
                    "dni" => $fila->doc_asegurado,
                    "nombre" => $fila->nombre_aseg,
                    "email" => $fila->correoelectronico,
                    "enviado" => $result->codigo,
                    "observaciones" => $result->mensaje
                ];
                $num_envios++;
            }
        }
        DB::statement("call sp_mailer_registra_guia(?,?)", [$guia, $num_envios]);
        return response()->json(compact("resultados"), 200);
    }

    public function reporte_envios (Request $request) {
        if ($request->has(["desde", "hasta", "envio", "leido", "carta", "contrato"])) {
            $user = Auth::user();
            $desde = $request->input("desde") . " 00:00:00";
            $hasta = $request->input("hasta") . " 23:59:59";
            $guia = $request->has("guia") ? $request->input("guia") : "";
            $envio = $request->input("envio");
            $leido = $request->input("leido");
            $carta = $request->input("carta");
            $contrato = $request->input("contrato");
            switch ($user->v_Codusuario) {
                case "posmailing":
                    $envios = DB::select("call sp_mailer_reporte_positiva(?,?,?,?,?,?)", [$desde, $hasta, $envio, $leido, $carta, $guia]);
                    break;
                case "comparfin":
                    $envios = DB::select("call sp_mailer_reporte_compartamos(?,?,?,?,?,?,?)", [$desde, $hasta, $envio, $leido, $carta, $contrato, $guia]);
                    break;
                default:
                    $envios = DB::select("call sp_mailer_reporte(?,?,?,?,?,?,?)", [$desde, $hasta, $envio, $leido, $carta, $contrato, $guia]);
                    break;
            }
            foreach ($envios as $pos => $envio) {
                $ijson = json_decode($envio->jsondata);
                if (isset($ijson->reponsable_de_pago)) {
                    $envios[$pos]->nombre = $ijson->reponsable_de_pago;
                }
                elseif (isset($ijson->correlativos)) {
                    $envios[$pos]->nombre = $ijson->correlativos;
                }
                else {
                    $envios[$pos]->nombre = $ijson->nombre_aseg;
                    $envios[$pos]->tipo = "Cliente";
                }
                // $envios[$pos]->nombre = isset($ijson->reponsable_de_pago) ? $ijson->reponsable_de_pago : $ijson->correlativos;
                $envios[$pos]->token = base64_encode(implode(":", [$user->user_id, $envio->id]));
                unset($envios[$pos]->jsondata);
                $envios[$pos]->json = $ijson;
            }
            return response()->json(compact("envios"), 200);
        }
        $error = "Parámetros incorrectos";
        return response()->json(compact("error"), 200);
    }

    public function ustar_logo (Request $request) {
        if ($request->has("token")) {
            list($id, $certificado) = explode(":", base64_decode($request->input("token")));
            // 1: mail enviado, 2: mail leído, 3: carta leída, 4: certificado leído
            DB::statement("call sp_mailer_confirma_envio(?,?,?)", [2, $id, $certificado]);
        }
        header("Content-Type: image/png");
        if ($request->has("type")) readfile(implode("/", [env("APP_MAILER_FOLDER"), "base", "compartamos.png"]));
        else readfile(implode("/", [env("APP_MAILER_FOLDER"), "base", "logo.png"]));
        die();
    }

    public function ustar_carta (Request $request) {
        if ($request->has("token")) {
            list($id, $certificado) = explode(":", base64_decode($request->input("token")));
            // recupera la ruta del archivo
            $file = DB::table("mailer_archivos_envio")
                ->where("id_envio", $id)
                ->where("de_certificado", $certificado)
                ->select("de_ruta_carta as path")
                ->first();
            DB::statement("call sp_mailer_confirma_envio(?,?,?)", [3, $id, $certificado]);
            $filepath = $file->path;
        }
        else {
            $filepath = implode("/", [env("APP_MAILER_FOLDER"), "demo", "carta.pdf"]);
        }
        header("Content-Type: application/pdf");
        readfile($filepath);
        die();
    }

    public function ustar_contrato (Request $request) {
        if ($request->has("token")) {
            list($id, $certificado) = explode(":", base64_decode($request->input("token")));
            // recupera la ruta del archivo
            $file = DB::table("mailer_archivos_envio")
                ->where("id_envio", $id)
                ->where("de_certificado", $certificado)
                ->select("de_ruta_cert as path")
                ->first();
            DB::statement("call sp_mailer_confirma_envio(?,?,?)", [4, $id, $certificado]);
            $filepath = $file->path;
        }
        else {
            $filepath = implode("/", [env("APP_MAILER_FOLDER"), "demo", "certificado.pdf"]);
        }
        header("Content-Type: application/pdf");
        readfile($filepath);
        die();
    }

    public function preview_carta (Request $request) {
        $user = Auth::user();
        list($usuario, $id) = explode(":", base64_decode($request->input("token")));
        if (strcmp($user->user_id, $usuario) != 0) {
            return "El token de acceso es incorrecto";
        }
        // recupera la ruta del archivo
        $file = DB::table("mailer_archivos_envio")
            ->where("id_envio", $id)
            ->select("de_ruta_carta as path")
            ->first();
        header("Content-Type: application/pdf");
        readfile($file->path);
        die();
    }

    public function preview_contrato (Request $request) {
        $user = Auth::user();
        list($usuario, $id) = explode(":", base64_decode($request->input("token")));
        if (strcmp($user->user_id, $usuario) != 0) {
            return "El token de acceso es incorrecto";
        }
        // recupera la ruta del archivo
        $file = DB::table("mailer_archivos_envio")
            ->where("id_envio", $id)
            ->select("de_ruta_cert_uns as path")
            ->first();
        header("Content-Type: application/pdf");
        readfile($file->path);
        die();
    }

    public function mail_preview (Request $request) {
        if ($request->has("token")) {
            $token = $request->input("token");
            list ($id, $certificado) = explode(":", base64_decode($token));
            return view("usmailer.mail-notificacion")->with(compact("token", "id"));
        }
        return view("usmailer.mail-prueba");
    }

    public function mail_preview_compartamos (Request $request) {
        if ($request->has("token")) {
            $token = $request->input("token");
            list ($id, $certificado) = explode(":", base64_decode($token));
            // recupera el asunto
            $json = DB::table("mailer_envios")->select("de_json as json")->where("id_envio", $id)->first()->json;
            $asunto = json_decode($json)->n0_expediente_carpeta_fiscal_caso;
            // muestra la vista
            return view("usmailer.mail-compartamos")->with(compact("token", "id", "asunto"));
        }
        return view("usmailer.mail-prueba");
    }

    public function mail_preview_lapositiva (Request $request) {
        if ($request->has("token")) {
            $token = $request->input("token");
            list ($id, $certificado) = explode(":", base64_decode($token));
            // recupera el asunto
            $json = DB::table("mailer_envios")->select("de_json as json")->where("id_envio", $id)->first()->json;
            $asunto = "🚨Importante: Comunicación sobre tu póliza de Microseguro Vida Caja Plan III. Aquí 👇";
            // muestra la vista
            return view("usmailer.mail-positiva-dic2022")->with(compact("token", "id", "asunto"));
        }
        return view("usmailer.mail-prueba");
    }

    public function confirma_guia (Request $request) {
        if ($request->has("guia")) {
            $guia = $request->get("guia");
            DB::table("mailer_guias_envio")->where("co_guia", $guia)->update([
                "st_estado_envio" => "Programado"
            ]);
            $success = true;
            return response()->json(compact("success"), 200);
        }
        $error = "Parámetros incorrectos";
        return response()->json(compact("error"), 200);
    }

    public function export_envios (Request $request) {
        if ($request->has(["desde", "hasta", "envio", "leido", "carta", "contrato"])) {
            $user = Auth::user();
            $desde = $request->input("desde") . " 00:00:00";
            $hasta = $request->input("hasta") . " 23:59:59";
            $guia = $request->has("guia") ? $request->input("guia") : "";
            $envio = $request->input("envio");
            $leido = $request->input("leido");
            $carta = $request->input("carta");
            $contrato = $request->input("contrato");
            $folder_name = date("Ymd_His");
            $folder_base = env("APP_MAILER_FOLDER") . "/temp";
            $folder_path = $folder_base . "/" . $folder_name;
            $forcezip = false;
            $xlsxmod = -1;
            switch ($user->i_CodCliente) {
                case 324: // positiva
                    // $envios = DB::select("call sp_mailer_reporte_todo(?,?,?,?,?,?,?)", [$desde, $hasta, $envio, $leido, $carta, $contrato, $guia]);
                    $resultados = DB::select("call sp_mailer_reporte_positiva(?,?,?,?,?,?)", [$desde, $hasta, $envio, $leido, $carta, $guia]);
                    $forcezip = true;
                    $xlsxmod = 1;
                    break;
                case 235: // compartamos
                    $resultados = DB::select("call sp_mailer_reporte_compartamos(?,?,?,?,?,?,?)", [$desde, $hasta, $envio, $leido, $carta, $contrato, $guia]);
                    $xlsxmod = 2;
                    break;
                default:
                    $resultados = DB::select("call sp_mailer_reporte(?,?,?,?,?,?,?)", [$desde, $hasta, $envio, $leido, $carta, $contrato, $guia]);
                    $xlsxmod = 3;
                    break;
            }
            $guia = strcmp($guia,"") == 0 ? "Todas" : $guia;
            switch ($xlsxmod) {
                case 1:
                case 3:
                    XlsxMaker::XlsxLaPositiva($folder_path, "reporte_envios", $resultados, $desde, $hasta, $guia);
                    break;
                case 2:
                    XlsxMaker::XlsxCompartamos($folder_path, "reporte_envios", $resultados, $desde, $hasta, $guia);
                    break;
            }
            if ($forcezip) {
                // genera los cargos
                foreach ($resultados as $pos => $iresultado) PdfMaker::GeneraCargo($iresultado, $folder_path);
                // genera el zip
                exec("cd $folder_base && zip -r $folder_name.zip $folder_name");
                // fin
                $headers = array(
                    "Content-Type: application/octet-stream",
                );
                return response()->download("$folder_base/$folder_name.zip", "sustento.zip", $headers);
            }
            else {
                $headers = array(
                    "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                );
                return response()->download("$folder_path/reporte_envios.xlsx", "sustento.xlsx", $headers);
            }
        }
        $error = "Parámetros incorrectos";
        return response()->json(compact("error"), 200);
    }

    public function export_pdfs (Request $request) {
        if ($request->has(["desde", "hasta", "envio", "leido", "carta", "contrato"])) {
            $user = Auth::user();
            $desde = $request->input("desde") . " 00:00:00";
            $hasta = $request->input("hasta") . " 23:59:59";
            $guia = $request->has("guia") ? $request->input("guia") : "";
            $envio = $request->input("envio");
            $leido = $request->input("leido");
            $carta = $request->input("carta");
            $contrato = $request->input("contrato");
            switch ($user->v_Codusuario) {
                case "posmailing":
                    // $envios = DB::select("call sp_mailer_reporte_todo(?,?,?,?,?,?,?)", [$desde, $hasta, $envio, $leido, $carta, $contrato, $guia]);
                    $resultados = DB::select("call sp_mailer_pdfs_positiva(?,?,?,?,?,?)", [$desde, $hasta, $envio, $leido, $carta, $guia]);
                    break;
                default:
                    return response()->make("Esta opción no se encuentra disponible", 400);
            }
            // crea la carpeta temporal
            $tmp_root_path = env("APP_MAILER_FOLDER") . "/temp";
            $folder_name = "cartas_" . date("YmdHis");
            $tmp_base_path = $tmp_root_path . "/" . $folder_name;
            @mkdir($tmp_base_path, 0744, true);
            // copia los archivos
            foreach ($resultados as $fila) {
                copy($fila->dcarta, $tmp_base_path . "/" . $fila->certificado . ".pdf");
            }
            // comprime la carpeta
            exec("cd $tmp_root_path && zip -r $folder_name.zip $folder_name");
            // devuelve enlace de descarga
            return response()->json(compact("folder_name"), 200);
        }
    }

    public function descarga_pdfs (Request $request) {
        if ($request->has("name")) {
            $folder_name = $request->input("name");
            $file = env("APP_MAILER_FOLDER") . "/temp/$folder_name.zip";
            // descarga!
            $size   = filesize($file);
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=cartas.zip");
            header("Content-length: " . $size);
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($file);
        }
        return response()->error("No se encontró el archivo especificado", 400);
    }

    public function conformidad_guia (Request $request) {
        if ($request->has("guia")) {
            $guia = $request->input("guia");
            DB::table("mailer_guias_envio")->where("co_guia", $guia)->update(["st_conformidad_envio" => "S"]);
            $state = "ok";
            return response()->json(compact("state"), 200);
        }
        return response()->make("Número de guía no existe", 400);
    }

	// mailing natura 25oct2023
    public function mailing_natura (Request $request) {
        $user = Auth::user();
        $arrData = [
            "usuario" => $user,
            "menu" => 6,
            "opcion" => "Mailing > Carga de envíos"
        ];
        return view("intranet.mailer_natura")->with($arrData);
    }

    public function lista_envios_natura (Request $request) {
        if ($request->has(["desde", "hasta"])) {
            $user = Auth::user();
            $desde = $request->input("desde") . " 00:00:00";
            $hasta = $request->input("hasta") . " 23:59:59";
            $data = DB::select("call sp_lista_destinos_mail(str_to_date(?,'%d/%m/%Y %H:%i:%s'),str_to_date(?,'%d/%m/%Y %H:%i:%s'),?,'DESTINATARIO')", [$desde, $hasta, $user->i_CodCliente]);
            $envios = [];
            foreach ($data as $fila) {
                $envios[] = [
                    "guia" => $fila->codguia,
                    "empresa" => $fila->empresa_abreviado,
                    "agente" => $fila->nomagente,
                    "destinatario" => $fila->nomdestinatario,
                    "ciudad" => $fila->NomCiudad,
                    "direccion" => $fila->DirDestinatario,
                    "enviado" => $fila->flgenviado,
                    "key" => encrypt(implode("@", [$fila->codautogen, $fila->nroproceso, $fila->nrocontrol])),
                ];
            }
            return response()->json(compact("envios"), 200);
        }
        return response()->make("Indique un rango de fechas válido para hacer la búsqueda", 400);
    }

    public function enviar_mail_natura_envio (Request $request) {
        if ($request->has("key")) {
            $payload = $request->input("key");
            $key = decrypt($payload);
            list ($autogen, $proceso, $control) = explode("@", $key);
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
                    and cl.FlgEnviarMsgAuto = 'S'
                    and f_obtiene_email_destinatario(enxp.codautogen, enxp.nroproceso, enxp.nrocontrol) <> '0'
                    and empresas.codempresa = 1", [$autogen, $proceso, $control]);
            if (count($datos) == 0) return response()->make("No se encontró información acerca de este envío", 400);
            $datos = $datos[0];
            $email = $datos->email;
            $datos = [
                "key" => $payload,
                "nombre" => $datos->nombre,
                "empresa" => $datos->empresa,
                "pedido" => $datos->pedido,
                "url" => $datos->url
            ];
            // envia el email
            \Mail::send("usmailer.mail-natura-envio", $datos, function($message) use($email) {
                $message->from(env("MAIL_FROM_ADDRESS"), env("MAIL_FROM_NAME"));
                $message->to($email)->subject("🚨 Entrega de pedido AVON/NATURA: Aviso de Visita");
                $message->replyTo("brayanhuaman@unionstar.com.pe");
            });
            // graba en el log
            DB::table("envios_x_proceso_msg")->insert([
                "CodAutogen" => $autogen,
                "NroProceso" => $proceso,
                "NroControl" => $control,
                "CorrSeguim" => 1,
                "TipoMsg" => "MAIL",
                "DestinoMsg" => "DESTINATARIO",
                "Mail" => $email,
                "Observaciones" => "Mail enviado a $email",
                "FlgAutomatico" => "S",
            ]);
            $success = true;
            return response()->json(compact("success"), 200);
        }
        return response()->make("Debe especificar el token de envío", 400);
    }

    public function preview_natura_envio (Request $request) {
        if ($request->has("key")) {
            $payload = $request->input("key");
            $key = decrypt($payload);
            list ($autogen, $proceso, $control) = explode("@", $key);
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
                    and cl.FlgEnviarMsgAuto = 'S'
                    and f_obtiene_email_destinatario(enxp.codautogen, enxp.nroproceso, enxp.nrocontrol) <> '0'
                    and empresas.codempresa = 1", [$autogen, $proceso, $control]);
            if (count($datos) == 0) return response()->make("No se encontró información acerca de este envío", 400);
            $datos = $datos[0];
            $datos = [
                "key" => $payload,
                "nombre" => $datos->nombre,
                "empresa" => $datos->empresa,
                "pedido" => $datos->pedido,
                "url" => $datos->url
            ];
            return view("usmailer.mail-natura-envio")->with($datos);
        }
        return response()->make("Debe especificar el token de envío", 400);
    }

    public function preview_natura_entrega (Request $request) {
        if ($request->has("key")) {
            $payload = $request->input("key");
            $key = decrypt($payload);
            list ($autogen, $proceso, $control) = explode("@", $key);
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
                    and empresas.codempresa = 1", [$autogen, $proceso, $control]);
            if (count($datos) == 0) return response()->make("No se encontró información acerca de este envío", 400);
            $datos = $datos[0];
            $datos = [
                "key" => $payload,
                "nombre" => $datos->nombre,
                "empresa" => $datos->empresa,
                "pedido" => $datos->pedido,
                "url" => $datos->url
            ];
            return view("usmailer.mail-natura-resultado")->with($datos);
        }
        return response()->make("Debe especificar el token de envío", 400);
    }
}
