<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|

Route::get('/', function () {
    return view('welcome');
});
*/
Route::group(["namespace" => "Web"], function() {
	Route::get("/", "Intranet@home");
	Route::get("viewer/{param}/{oldpath?}", "Intranet@viewer");
	Route::get("export", "Intranet@export");
	Route::get("download/{filename}", "Intranet@download");
	Route::get("make-pdf", "Intranet@pdf");
	//servicios
	Route::group(["prefix" => "servicios"], function() {
		Route::group(["prefix" => "distribucion"], function() {
			Route::get("/", "Intranet@srv_distribucion");
			Route::group(["prefix" => "ajax"], function() {
				Route::post("buscar", "Servicios@distribucion_buscar");
				Route::post("detalle", "Servicios@distribucion_detalle");
				Route::post("export", "Servicios@export");
			});
		});
		Route::get("almacenes", "Intranet@srv_almacenes");
	});
	//tracking
	Route::group(["prefix" => "tracking"], function() {
		Route::get("/", "Intranet@tracking");
		Route::get("imagenes/{autogen}/{proceso}/{control}", "Tracking@imagenes");
		Route::get("visor-img", "Tracking@visor_img_manif");
		Route::group(["prefix" => "ajax"], function() {
			Route::post("buscar", "Tracking@buscar");
			Route::post("detalle", "Tracking@detalle");
			Route::post("sv-reclamo", "Tracking@sv_reclamo");
			Route::post("export", "Tracking@export");
		});
	});
	//indicadores
	Route::prefix("indicadores")->group(function() {
		Route::get("d-entregas", "Intranet@ind_dis_entregas");
		Route::prefix("ajax")->group(function() {
			Route::post("buscar", "Indicadores@ebuscar");
			Route::post("if-entrega-distrito", "Indicadores@if_entrega_distrito");
		});
	});
	//usuarios
	Route::prefix("usuarios")->group(function() {
		Route::get("/", "Intranet@usuarios");
		Route::prefix("ajax")->group(function() {
			Route::post("cmb-contacto","Usuarios@cmb_contacto");
			Route::post("ins-usuario","Usuarios@ins_usuario");
			Route::post("dt-usuario","Usuarios@dt_usuario");
			Route::post("upd-usuario","Usuarios@upd_usuario");
			Route::post("del-usuario","Usuarios@del_usuario");
			Route::post("act-usuario","Usuarios@act_usuario");
		});
	});
	//reclamos
	Route::prefix("reclamos")->group(function() {
		Route::get("/", "Intranet@reclamos");
		Route::prefix("ajax")->group(function() {
			Route::post("buscar", "Reclamos@buscar");
			Route::post("detalle", "Reclamos@detalle");
			Route::post("elimina", "Reclamos@elimina");
			Route::post("combo", "Reclamos@combo");
			Route::post("dt-reclamo", "Reclamos@dt_reclamo");
			Route::post("sv-reclamo", "Reclamos@sv_reclamo");
			Route::post("upd-reclamo", "Reclamos@upd_reclamo");
			Route::post("export", "Reclamos@export");
		});
	});
	// conexion web service
	Route::prefix("masivos")->group(function() {
		Route::get("/", "Intranet@masivos");
		Route::get("reporte", "Intranet@reporte_masivos");
		Route::prefix("ajax")->group(function() {
			Route::get("buscar", "ApiNatura@buscar");
			Route::post("enviar-ws", "ApiNatura@enviar_ws");
			Route::get("reporte", "ApiNatura@reporte");
		});
	});
	//autenticacion de usuarios
	Route::group(["prefix" => "login"], function() {
		Route::get("/", ["as" => "login", "uses" => "Autenticacion@form_login"]);
		Route::post("verificar", "Autenticacion@post_login");
		Route::get("logout", "Autenticacion@logout");
	});
});
//politica de privacidad
Route::get("privacy", function() {
	return view("privacy");
});
// extranet clientes
Route::group(["prefix" => "scan-api"], function() {
	Route::post("image-submit", "ScanApi@upload");
	Route::get("image-process/{key}", "ScanApi@process");
	Route::get("perspective", "ScanApi@perspective");
});
Route::group(["prefix" => "tracking-pedidos"], function() {
	Route::get("{tracking?}", function ($tracking = null) {
		if ($tracking != null) {
			$dias = ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"];
            $meses = ["", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Set", "Oct", "Nov", "Dic"];
            $key = $tracking;
            $tracking = DB::select("select f_devuelve_key_envio(?) nro_tracking", [$tracking])[0]->nro_tracking;
            $tipo = strcmp($key, $tracking) == 0 ? "Pedido" : "Guía";
            $cabecera = DB::select("call sp_tracking_cab(?)", [$tracking])[0];
            $eventos = DB::select("call sp_tracking_det(?)", [$tracking]);
			$mensajero = DB::select("select f_devuelve_mensajero_actual(?) codigo", [$tracking]);
			$imagenes = DB::select("call sp_web_imagenes_cargos(?,?,?)", explode("-", $tracking));
            $data = [
                "meses" => $meses,
                "dias" => $dias,
                "tracking" => $tracking,
                "fechaing" => implode(" ", [substr($cabecera->fechaing, 8, 2), $meses[(int) substr($cabecera->fechaing, 5, 2)], substr($cabecera->fechaing, 0, 4)]),
                "destino" => ucwords(strtolower(str_replace("-", ", ", $cabecera->ciudad))),
                "contenido" => trim($cabecera->txtcontenido),
                "contacto" => ucwords(strtolower($cabecera->NomDestinatario)),
                "direccion" => ucwords(strtolower($cabecera->DirDestinatario)),
                "telefono" => $cabecera->NroTelefDesti,
                "eventos" => $eventos,
				"mensajero" => $mensajero[0]->codigo,
				"imagenes" => $imagenes,
                "busca" => $key,
                "tipo" => $tipo
            ];
		}
		else $data = compact("tracking");
		return view("extranet.home")->with($data);
	});
});
Route::group(["prefix" => "mailer"], function() {
	Route::get("upload", "Usmailer@view_upload")->name("mailer_upload");
	Route::get("reporte", "Usmailer@view_reporte")->name("mailer_reporte");
	Route::post("upload-xlsx", "Usmailer@upload_xlsx");
	Route::post("upload-xlsx-compartamos", "Usmailer@upload_xlsx_compartamos");
	Route::post("upload-xlsx-microseguro", "Usmailer@upload_xlsx_microseguro");
	Route::post("upload-xlsx-microseguro-step2", "Usmailer@upload_xlsx_microseguro_step2");
	Route::get("reporte-envios", "Usmailer@reporte_envios");
	Route::get("ustar-logo", "Usmailer@ustar_logo");
	Route::get("mail-preview", "Usmailer@mail_preview");
	Route::get("mail-preview-compartamos", "Usmailer@mail_preview_compartamos");
	Route::get("mail-preview-lapositiva", "Usmailer@mail_preview_lapositiva");
	Route::post("confirma-guia", "Usmailer@confirma_guia");
	Route::get("exporta-reporte", "Usmailer@export_envios");
	Route::patch("conformidad-guia", "Usmailer@conformidad_guia");
	Route::group(["prefix" => "usdocs"], function() {
		Route::get("carta", "Usmailer@ustar_carta");
		Route::get("contrato", "Usmailer@ustar_contrato");
		Route::get("prev-carta", "Usmailer@preview_carta");
		Route::get("prev-contrato", "Usmailer@preview_contrato");
		Route::get("prev-mail", "Usmailer@preview_email");
		Route::post("exportar-pdfs", "Usmailer@export_pdfs");
		Route::get("descarga-pdfs", "Usmailer@descarga_pdfs");
	});
	Route::get("prueba-mail", function() {
		$token = base64_encode("4:0000000918");
		return view("usmailer.mail-prueba")->with(compact("token"));
	});
	// mailing natura 25oct2023
	Route::get("mailing-natura", "Usmailer@mailing_natura")->name("mailing_natura");
	Route::get("lista-envios-natura", "Usmailer@lista_envios_natura");
	Route::get("preview-natura-envio", "Usmailer@preview_natura_envio");
	Route::get("preview-natura-entrega", "Usmailer@preview_natura_entrega");
	Route::post("procesar-mail-envio", "Usmailer@enviar_mail_natura_envio");
});

Route::group(["prefix" => "atc"], function () {
	Route::get("/", "Atc@atc");
	Route::group(["prefix" => "ajax"], function () {
		// Route::post("registra-reclamo", "Publico@post_atc");
		Route::post("validar-login", "Atc@validar_login");
		Route::get("verificar-revistas", "Atc@verificar_revistas");
		Route::post("registra-reclamos", "Atc@registra_reclamos");
	});
});