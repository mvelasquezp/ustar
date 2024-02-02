<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;

class ApiNatura extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    protected $idTransportista = "15101";
    protected $urlStaging = "https://naturawtm.intrasites.com/nwtperu/EstadoPedidoActionCallbackWS";
    protected $urlProduccion = "https://tracking.natura.com.pe/nwtperu/EstadoPedidoActionCallbackWS";

    public function __construct() {
        $this->middleware("auth");
        date_default_timezone_set("America/Lima");
    }

    public function buscar (Request $request) {
        if ($request->has("fecha")) {
            $fecha = $request->input("fecha");
            $usuario = Auth::user();
            $cocliente = 75;
            // $registros = DB::select("call sp_cron_actu_ws_clientes(str_to_date(?,'%d/%m/%Y'),1,?,?)", [$fecha, $usuario->i_CodCliente, $usuario->user_id]);
            $registros = DB::select("call sp_cron_actu_ws_clientes(str_to_date(?,'%d/%m/%Y'),1,?,?)", [$fecha, $cocliente, $usuario->user_id]);
            return response()->json(compact("registros"), 200);
        }
        return response()->make("Parámetros incorrectos", 400);
    }

    public function enviar_ws (Request $request) {
        if ($request->has(["autogen","proceso","control","pedido","fecha","evento","usuario","motivo","justificacion"])) {
            $idPedido = $request->input("pedido");
            $idTransportista = $this->idTransportista;
            $sfecha = $request->input("fecha");
            $fecha = str_replace(["-"," ",":"], ["","",""], $sfecha);
            $idEvento = $request->input("evento");
            $result = \App\Helpers\Curl::get($this->urlStaging, compact("idPedido","idTransportista","fecha","idEvento"));
            // registra la respuesta
            try {
                DB::table("envios_x_proceso_wscliente")->insert([
                    "CodAutogen" => $request->input("autogen"),
                    "NroProceso" => $request->input("proceso"),
                    "NroControl" => $request->input("control"),
                    "DtmEventoEnvio" => DB::raw("str_to_date('$sfecha','%Y-%m-%d %H:%i:%s')"),
                    "DtmCarga" => DB::raw("now()"),
                    "CodEventoCliente" => $idEvento,
                    "CodUSuCarga" => $request->input("usuario"),
                    "WsRespuestaCarga" => isset($result->error) ? $result->error : $result->mensaje,
                    "CodMotivoEnvio" => $request->input("motivo"),
                    "CodJustiMotivo" => $request->input("justificacion")
                ]);
            }
            catch (\Illuminate\Database\QueryException $ex) {
                $mensaje = $ex->getMessage();
            }
            // fin
            return response()->json(compact("result"), 200);
        }
        return response()->make("Parámetros incorrectos [" . print_r($request->input(),true) . "]", 400);
    }

    public function reporte (Request $request) {
        if ($request->has("fecha")) {
            $fecha = $request->input("fecha");
            $usuario = Auth::user();
            $cocliente = 75;
            // $registros = DB::select("call sp_ws_clientes_consulta_carga(str_to_date(?,'%d/%m/%Y'),?)",[$fecha, $usuario->i_CodCliente]);
            $registros = DB::select("call sp_ws_clientes_consulta_carga(str_to_date(?,'%d/%m/%Y'),?)",[$fecha, $cocliente]);
            return response()->json(compact("registros"), 200);
        }
        return response()->make("Parámetros incorrectos", 400);
    }
}