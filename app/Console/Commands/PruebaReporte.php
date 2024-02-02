<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class PruebaReporte extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "prueba:reporte";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $datos = DB::select("call sp_web_tracking_distribu_list_final ('2022-12-01', '2023-01-31', 'Todos', 'Todos', 'Todos', 'Todos','Todos', 'oriflame')");
        echo "se encontró " . count($datos) . " datos!\n";
    }
}
