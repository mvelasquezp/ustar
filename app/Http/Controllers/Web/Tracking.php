<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Request;
use App\User as User;

class Tracking extends Controller {
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

    public function tracking() {
        $user = Auth::user();
        $arrData = [
            "usuario" => $user,
            "menu" => 1,
            "opcion" => "Tracking"
        ];
        return view("intranet.tracking")->with($arrData);
    }

}