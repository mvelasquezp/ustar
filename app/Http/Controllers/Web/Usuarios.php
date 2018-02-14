<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use Response;
use App\User as User;

class Usuarios extends Controller {
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

    public function cmb_contacto() {
        $user = Auth::user();
        extract(Request::input());
        if(isset($ctc)) {
            $data = DB::table("clientes_contactos as cc")
                ->join("areas_clientes as ac", "ac.CodAreaCliente", "=", "cc.CodAreaCliente")
                ->where("cc.CodContacto", $ctc)
                ->select("cc.NomContacClie as nombre", "cc.CodEmail as email", "cc.NumTelefono as telefono", "cc.DniContacto as dni", "ac.CodCliente as codcli")
                ->first();
            return Response::json([
                "state" => "success",
                "data" => $data
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

    public function ins_usuario() {
        $user = Auth::user();
        extract(Request::input());
        if(isset($als,$nom,$dni,$eml,$tlf,$eid,$ccl,$ctc,$psw,$prf)) {
            $cd1 = isset($cd1) ? $cd1 : "";
            $cd2 = isset($cd2) ? $cd2 : "";
            $cd3 = isset($cd3) ? $cd3 : "";
            DB::table("seg_user")->insert([
                "v_Codusuario" => $als,
                "v_Nombres" => $nom,
                "c_TipoDocide" => "1",
                "v_NroDocide" => $dni,
                "v_Email" => $eml,
                "v_Telefonos" => $tlf,
                "v_IdPerCliente" => $eid,
                "v_PerClienteAgrupa1" => $cd1,
                "v_PerClienteAgrupa2" => $cd2,
                "v_PerClienteAgrupa3" => $cd3,
                "i_CodCliente" => $ccl,
                "i_CodContacto" => $ctc,
                "c_TipoUsuario" => "E",
                "v_Clave" => $psw,
                "v_CodEstado" => "Vigente",
                "i_CodTipoPerfil" => $prf
            ]);
            $usuarios = DB::select("call sp_web_usuarios_list(?,?,?)", [$user->v_Codusuario, "Todos", ""]);
            return Response::json([
                "state" => "success",
                "data" => $usuarios
            ]);
        }
        return Response::json([
            "state" => "error",
            "message" => "Parámetros de búsqueda incorrectos"
        ]);
    }

}