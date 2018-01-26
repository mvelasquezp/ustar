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
	Route::group(["prefix" => "servicios"], function() {
		Route::group(["prefix" => "distribucion"], function() {
			Route::get("/", "Intranet@srv_distribucion");
			Route::group(["prefix" => "ajax"], function() {
				Route::post("buscar", "Servicios@distribucion_buscar");
				Route::post("detalle", "Servicios@distribucion_detalle");
			});
		});
		Route::get("almacenes", "Intranet@srv_almacenes");
	});
	//tracking
	Route::group(["prefix" => "tracking"], function() {
		Route::get("/", "Tracking@tracking");
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