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
	Route::get("viewer/{param}", "Intranet@viewer");
	Route::get("export", "Intranet@export");
	Route::get("download/{filename}", "Intranet@download");
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
		Route::group(["prefix" => "ajax"], function() {
			Route::post("buscar", "Tracking@buscar");
			Route::post("detalle", "Tracking@detalle");
			Route::post("export", "Tracking@export");
		});
	});
	//indicadores
	Route::prefix("indicadores")->group(function() {
		Route::get("d-entregas", "Intranet@ind_dis_entregas");
		Route::prefix("ajax")->group(function() {
			Route::post("buscar", "Indicadores@ebuscar");
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