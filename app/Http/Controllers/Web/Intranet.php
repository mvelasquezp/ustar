<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use App\User as User;

class Intranet extends Controller {
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

    public function home() {
        $user = Auth::user();
        $arrData = [
            "usuario" => $user,
            "menu" => 0,
            "opcion" => "Inicio"
        ];
        return view("intranet.inicio")->with($arrData);
    }

    public function srv_distribucion() {
        $user = Auth::user();
        $cmb_oficinas = DB::select("call sp_web_combo_areas(?)", [$user->v_Codusuario]);
        $cmb_ccostos = DB::select("call sp_web_combo_ccostos(?)", [$user->v_Codusuario]);
        $cmb_productos = DB::select("call sp_web_combo_tipoenvios(?)", [$user->v_Codusuario]);
        $arrData = [
            "usuario" => $user,
            "menu" => 1,
            "opcion" => "Mis Servicios > Distribución",
            "ofcs" => $cmb_oficinas,
            "ccts" => $cmb_ccostos,
            "prds" => $cmb_productos
        ];
        return view("intranet.sdistribucion")->with($arrData);
    }

    public function tracking() {
        $user = Auth::user();
        $cmb_oficinas = DB::select("call sp_web_combo_areas(?)", [$user->v_Codusuario]);
        $cmb_ccostos = DB::select("call sp_web_combo_ccostos(?)", [$user->v_Codusuario]);
        $cmb_productos = DB::select("call sp_web_combo_tipoenvios(?)", [$user->v_Codusuario]);
        $arrData = [
            "usuario" => $user,
            "menu" => 2,
            "opcion" => "Tracking",
            "ofcs" => $cmb_oficinas,
            "prds" => $cmb_productos
        ];
        return view("intranet.tracking")->with($arrData);
    }

    //adicionales

    public function viewer($param) {
        $file = implode(DIRECTORY_SEPAARTOR, [env("APP_STORAGE_PATH"), str_replace("@", DIRECTORY_SEPAARTOR, $param)]);
        $type = "image/jpeg";
        header("Content-Type:" . $type);
        header("Content-Length: " . filesize($file));
        readfile($file);
    }

}