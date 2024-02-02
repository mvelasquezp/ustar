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
	Route::any("{query}", function () {
		$result = "error";
        $rqid = 0;
        $message = "Actualice el app para acceder";
        return response()->json(compact("result", "rqid", "message"), 200);
	})->where("query", ".*");
});
Route::get("prueba", function () {
	mkdir("/var/www/html/imagenes/tif/GI/888-8895");
	return "listo";
});