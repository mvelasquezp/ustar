<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Excel;
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
        $this->middleware("auth")->except(["visor_img_manif"]);
        date_default_timezone_set("America/Lima");
    }

    public function imagenes ($autogen, $proceso, $control) {
        $base_path = env("APP_STORAGE_PATH");
        $imagenes = DB::select("call sp_web_imagenes_cargos(?,?,?)", [$autogen, $proceso, $control]);
        $output_path = env("APP_PUBLIC_PATH") . "/tif/$autogen-$proceso-$control";
        if (!file_exists($output_path)) mkdir($output_path, 0744, true);
        // procesa las imagenes
        foreach ($imagenes as $imagen) {
            $imgnombre = $imagen->nomimg;
            $localpath = $output_path . DIRECTORY_SEPARATOR . $imgnombre;
            if (!file_exists($localpath)) {
                $oldimgpath = implode(DIRECTORY_SEPARATOR, [$base_path, str_replace("//", "/", $imagen->rutaimg)]) . $imgnombre;
                $newimgpath = implode(DIRECTORY_SEPARATOR, [$base_path, str_replace("//", "/", $imagen->rutaimgprincipal)]) . $imgnombre;
                if (file_exists($oldimgpath)) {
                    // copiar la imagen a la carpeta temporal
                    copy($oldimgpath, $localpath);
                    // si es un tiff, debo dividirlo
                    $info = pathinfo($oldimgpath);
                    if (strcmp(strtolower($info["extension"]),"tif") == 0) {
                        exec("cd $output_path && convert $imgnombre -set filename:f \"%[t]_%[fx:t+1]\" +adjoin \"%[filename:f].tif\"");
                        unlink($localpath);
                    }
                }
                elseif (file_exists($newimgpath)) {
                    // copiar la imagen a la carpeta temporal
                    copy($newimgpath, $localpath);
                    // si es un tiff, debo dividirlo
                    $info = pathinfo($newimgpath);
                    if (strcmp(strtolower($info["extension"]),"tif") == 0) {
                        exec("cd $output_path && convert $imgnombre -set filename:f \"%[t]_%[fx:t+1]\" +adjoin \"%[filename:f].tif\"");
                        unlink($localpath);
                    }
                }
                else return response()->make("$autogen-$proceso-$control: No se encontraron imágenes asociadas [$oldimgpath - $newimgpath]",400);
            }
        }
        // convertir todos los tifs
        $files = array_slice(scandir($output_path), 2);
        foreach ($files as $ifile) {
            $ruta_imagen = $output_path . DIRECTORY_SEPARATOR . $ifile;
            $info = pathinfo($ruta_imagen);
            if (strcmp($info["extension"], "tif") == 0) {
                $file = str_replace(".tif", ".jpg", $ruta_imagen);
                //header('Content-Disposition: inline; filename=cargo.jpg"');
                $image = new \Imagick($ruta_imagen);
                    $image->setImageFormat("jpeg");
                    $image->setImageCompressionQuality(90);
                    $image->writeImage($file);
                    $image->destroy();
                // adaptar tamaño
                list($width, $height) = getimagesize($file);
                if ($height > $width) {
                    $new_height = 1920;
                    $new_width = 3440;
                    $imgOutput = imagecreatetruecolor($new_width, $new_height);
                    $gray = imagecolorallocate($imgOutput, 32, 32, 32);
                    imagefill($imgOutput, 0, 0, $gray);
                    $imgInput = imagecreatefromjpeg($file);
                    // redimensiona y adapta
                    $scale =  $new_height / $height;
                    $newHeight = $new_height;
                    $newWidth = round($scale * $width);
                    $extra = round(($new_width - $newWidth) / 2);
                    imagecopyresampled($imgOutput, $imgInput, $extra, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    unlink($file);
                    imagedestroy($imgInput);
                    imagejpeg($imgOutput, $file, 75);
                    // Liberar memoria
                    imagedestroy($imgOutput);
                }
                unlink($ruta_imagen);
            }
            else {
                // adaptar tamaño
                list($width, $height) = getimagesize($ruta_imagen);
                if ($height > $width) {
                    $new_height = 1920;
                    $new_width = 3440;
                    $imgOutput = imagecreatetruecolor($new_width, $new_height);
                    $gray = imagecolorallocate($imgOutput, 32, 32, 32);
                    imagefill($imgOutput, 0, 0, $gray);
                    $imgInput = imagecreatefromjpeg($ruta_imagen);
                    // redimensiona y adapta
                    $scale =  $new_height / $height;
                    $newHeight = $new_height;
                    $newWidth = round($scale * $width);
                    $extra = round(($new_width - $newWidth) / 2);
                    imagecopyresampled($imgOutput, $imgInput, $extra, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    unlink($ruta_imagen);
                    imagejpeg($imgOutput, $ruta_imagen, 75);
                    // Liberar memoria
                    imagedestroy($imgInput);
                    imagedestroy($imgOutput);
                }
            }
        }
        // OLD: devuelve lista de imagenes
        $files = array_slice(scandir($output_path), 2);
        return view("intranet.tracking-imagenes")->with(compact("autogen", "proceso", "control", "files"));
    }

    public function visor_img_manif () {
        $key = Request::input("key", "-");
        if (strcmp($key, "-") == 0) return "Llave incorrecta";
        list ($origen, $destino, $manifiesto, $periodo) = explode("|", base64_decode($key));
        $data = DB::select("select f_devuelve_ruta_imag_manif(?,?,?,?) ruta", [$origen, $destino, $periodo, $manifiesto]);
        if (count($data) == 0) return "No se encontró la ruta";
        $path = $data[0]->ruta;
        // actualiza flag de lectura
        DB::statement("call sp_app_whassap_confirma_despacho_leido(?,?,?,?)", [$origen, $destino, $manifiesto, $periodo]);
        // muestra galeria de imagenes
        $fullpath = env("APP_IMG_VIEWER_PATH") . $path;
        $pattern = '~^' . $manifiesto . '-.*\.jpg$~';
        $files = preg_grep($pattern, scandir($fullpath));
        return view("intranet.galeria-despachadores")->with(compact("files", "path", "manifiesto"));
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
            // $resultados = DB::select("call sp_web_tracking_distribu_list(?,?,?,?,?,?,?,?)", [$dsd, $hst, $prd, $ofc, $doc, $dst, $ref, $user->v_Codusuario]);
//            $query = "call sp_web_tracking_distribu_list_pru(?,?,?,?,?,?,?,?)";
            $query = "call sp_web_tracking_distribu_list_final(?,?,?,?,?,?,?,?)";
            $params = [$dsd, $hst, $prd, $ofc, $doc, $dst, $ref, $user->v_Codusuario];
            $resultados = DB::select($query, $params);
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

    public function export() {
        $user = Auth::user();
        extract(Request::input());
        $filename = implode("_", [$user->v_Codusuario, date("YmdHis")]);
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
            // $resultados = DB::select("call sp_web_tracking_distribu_list(?,?,?,?,?,?,?,?)", [$dsd, $hst, $prd, $ofc, $doc, $dst, $ref, $user->v_Codusuario]);
            $resultados = DB::select("call sp_web_tracking_distribu_list_final(?,?,?,?,?,?,?,?)", [$dsd, $hst, $prd, $ofc, $doc, $dst, $ref, $user->v_Codusuario]);
            // PREPARA XLSX CON LA LIBRERIA ALV
            Excel::create($filename, function($excel) use ($resultados) {
                $excel->sheet("datos", function($sheet) use ($resultados) {
                    $sheet->setColumnFormat(array(
                        'B' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
                    ));
                    $sheet->row(1, ["FechaIng", "CodGuia", "Remito", "Control", "Nombre", "Direccion",
                        "Departamento", "Provincia", "Ciudad", "Puesto", "Destinatario", "Tp.Envio",
                        "Contenido", "Servicio", "Fe.Prevista", "Visita 1", "Motivo 1", "Visita 2", "Motivo 2", 
                        "Visita 3", "Motivo 3", "Fecha estado actual", "Estado actual", "Detalle estado actual", "NroDocu.",
                        "Cuenta", "Comprobante", "Empresa", "Origen"
                    ]);
                    $sheet->cells("A1:AC1", function($cells) {
                        $cells->setBackground("#202020");
                        $cells->setFontColor("#ffffff");
                    });
                    $idxRow = 2;
                    foreach($resultados as $idx => $resultado) {
                        $i = $idxRow + $idx;
                        $rData = [
                            $resultado->fecing, $resultado->codguia, $resultado->remito, $resultado->control, $resultado->nombre, $resultado->direccion,
                            $resultado->NomDpto, $resultado->NomProv, $resultado->NomDist, $resultado->puesto, $resultado->idedestin, $resultado->tipoenvio,
                            $resultado->contenido, $resultado->servicio, $resultado->fe_prevista, $resultado->visita1, $resultado->motivo1, $resultado->visita2, $resultado->motivo2,
                            $resultado->visita3, $resultado->motivo3, $resultado->fe_estado_actual, $resultado->estado_actual, $resultado->det_estado_actual, $resultado->nrodocu,
                            $resultado->cuenta, $resultado->comprobante, $resultado->NomEmpresaDesti, $resultado->origen
                        ];
                        $sheet->row($i, $rData);
                        if($i % 2 == 0) {
                            $sheet->row($i, function($row) {
                                $row->setBackground("#f0f0f0");
                            });
                        }
                    }
                });
            })->store("xlsx", env("APP_FILES_PATH"));
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

    public function sv_reclamo() {
        extract(Request::input());
        $user = Auth::user();
        return $user;
        if (isset($autogen, $proceso, $control, $tipo, $titulo, $mensaje)) {
            $ahora = date("Y-m-d H:i:s");
            DB::table("atc_cab")->insert([
                "fRegistro" => $ahora,
                "vUsuRegistra" => $user->v_Codusuario,
                "cTipoGestionAtc" => "R",
                "iMotivoGestionAtc" => $tipo,
                "vEstado" => "PENDIENTE",
                "iCodConclusion" => 5,
                "vAsunto" => $titulo,
                "vDescripcion" => $mensaje,
                "cFlgEnviado" => "S",
                "fEnvio" => $ahora,
                "hEnvio" => $ahora,
                "CodAutogen" => $autogen,
                "NroProceso" => $proceso,
                "NroControl" => $control,
                "iCodCliente" => $user->i_CodCliente
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

}