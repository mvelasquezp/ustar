<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use App\User as User;

class Autenticacion extends Controller {
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */

    public function __construct() {
        $this->middleware("guest")->except(["logout"]);
        date_default_timezone_set("America/Lima");
    }

    public function form_login() {
        return view("auth.login");
    }

    public function post_login() {
        extract(Request::input());
        if(isset($user, $pswd)) {
            if(Auth::attempt(["v_Codusuario" => $user, "v_clave" => $pswd, "password" => env("APP_DEFAULT_PSW")], true)) {
                return redirect("/");
            }
            else {
                return "usuario y/o clave incorrectos [$user, $pswd]";
            }
        }
        else {
            return "ingrese correctamente su usuario y clave";
        }
    }

    public function logout() {
        Auth::logout();
        return redirect("login");
    }

}