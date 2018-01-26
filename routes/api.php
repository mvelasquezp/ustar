<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(["prefix" => "ustar"], function() {
	Route::any("home", "WebService@home");
	Route::any("login", "WebService@login");
	Route::any("accesos", "WebService@ls_acceso_modulos");
	Route::any("resumen", "WebService@ls_resumen_pariente");
	//modulo de despachos
	Route::any("despachos", "WebService@ls_lista_despachos");
	Route::any("upd-despacho", "WebService@upd_despacho");
	//modulo de recojos
	Route::any("recojos", "WebService@ls_lista_recojos");
	Route::any("det-recojos", "WebService@ls_detalle_recojo");
	Route::any("upd-recojo", "WebService@upd_recojo");
	//modulo de entregas
	Route::any("entregas", "WebService@ls_lista_entregas");
	Route::any("upd-entrega", "WebService@upd_entrega");
	Route::any("upd-imagen", "WebService@upd_imagen");
});