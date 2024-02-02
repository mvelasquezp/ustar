<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;

class PruebaMail extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "devel:mail";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Envía correo de prueba";

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
        Mail::send("usmailer.mail-prueba", [], function($message) {
            $message->from(env("MAIL_FROM_ADDRESS"), env("MAIL_FROM_NAME"));
            $message->to(["mvelasquezp88@gmail.com"/*, "maritzamoran@unionstar.com.pe", "nelsonagurtoatoche@gmail.com"*/])->subject("Correo masivo");
        });
        echo "OK\n";
    }
}
