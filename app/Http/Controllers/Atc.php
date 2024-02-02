<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

class Atc extends Controller {

    public function atc (Request $request) {
        $user = Auth::user();
        $motivos = DB::select("call sp_web_combo_motireclamo_rec('R')");
        return view("atc.formulario")->with(compact("motivos"));
    }

    public function validar_login (Request $request) {
        if ($request->has("dni")) {
            $codigo = $request->input("dni");
            $datos = DB::select("select
                    ifnull(i_CodContacto, 0) contacto,
                    i_CodCliente cliente,
                    v_codusuario coduser,
                    concat(ifnull(v_nombres,''),' ',ifnull(v_apellidos,'')) as nombre,
                    cl.RazonSocial as nomcliente
                from seg_user
                   join clientes cl on cl.codcliente = seg_user.i_CodCliente
                where (
                        v_codusuario = ?
                        or v_codperempresa = ?
                        or v_nrodocide = ?
                        or v_email = ?
                        or v_idpercliente = ?
                    )
                    and v_CodEstado = 'Vigente'", [$codigo,$codigo,$codigo,$codigo,$codigo]);
            if (count($datos) == 0) return response()->make("No se encontraron datos para el código seleccionado", 400);
            $datos = $datos[0];
            // carga los ciclos
            $ciclos = DB::select("call sp_web_combo_periodos(?)", [$codigo]);
            // carga tipos de envio
            $tipos = DB::select("call sp_web_combo_tipoenvios_rec(?)", [$codigo]);
            //encripta los datos
            $key = encrypt(implode("|", [$datos->contacto, $datos->cliente, $datos->coduser]));
            // fin
            return response()->json(compact("datos", "ciclos", "tipos", "key"), 200);
        }
        return response()->make("Ingrese un DNI/código válido", 400);
    }

    public function verificar_revistas (Request $request) {
        if ($request->has(["key", "ciclo", "vendedor"])) {
            $key = $request->input("key");
            list($contacto, $cliente, $usuario) = explode("|", decrypt($key));
            $ciclo = $request->input("ciclo");
            $vendedor = $request->input("vendedor");
            $datos = DB::select("select distinct
                    da.codautogen autogen,
                    da.nroproceso proceso,
                    da.nrocontrol control
                from guias_ingreso gi
                    join guias_ing_procesos gip on gip.codautogen = gi.codautogen
                    join areas_clientes ac on ac.codareacliente = gi.codareaclie
                    left join datos_adicionales da on da.codautogen = gi.codautogen and da.nroproceso = gip.nroproceso
                where ac.codcliente = ?
                    and gip.FlgIngresosReclamo = 'N'
                    and gi.CiCloCorteFactuCliente = ?
                    and da.IdeDestinatario = ?", [$cliente, $ciclo, $vendedor]);
            if (count($datos) == 0) return response()->make("El código solicitado no tiene revistas asignadas", 400);
            $datos = $datos[0];
            return response()->json(compact("datos"), 200);
        }
        return response()->make("Parámetros incorrectos", 400);
    }

    public function registra_reclamos (Request $request) {
        if ($request->has(["key", "tipo", "ciclo", "motivo", "data"])) {
            $key = $request->input("key");
            list($contacto, $cliente, $usuario) = explode("|", decrypt($key));
            $tipo = $request->input("tipo");
            $ciclo = $request->input("ciclo");
            $motivo = $request->input("motivo");
            $data = explode("|", $request->input("data"));
            $asunto = "No llegó la revista";
            $mensaje = "No llegó la revista";
            foreach ($data as $reclamo) {
                list ($autogen, $proceso, $control) = explode("@", $reclamo);
                DB::table("atc_cab")->insert([
                    "iCodContacto" => $contacto,
                    "fRegistro" => DB::raw("now()"),
                    "vUsuRegistra" => $usuario,
                    "cTipoGestionAtc" => "R",
                    "iMotivoGestionAtc" => $motivo,
                    "vEstado" => "PENDIENTE",
                    "iCodConclusion" => 5,
                    "vAsunto" => $asunto,
                    "vDescripcion" => $mensaje,
                    "cFlgEnviado" => "N",
                    "CodAutogen" => $autogen,
                    "NroProceso" => $proceso,
                    "NroControl" => $control,
                    "CiCloCorteFactuCliente" => $ciclo,
                    "iCodAutogenAtc" => $autogen,
                    "iCodCliente" => $cliente,
                ]);
            }
            $mensaje = "Se ha registrado un total de: " . count($data) . " reclamo(s)";
            return response()->json(compact("mensaje"), 200);
        }
        return response()->make("Parámetros incorrectos", 400);
    }
}
