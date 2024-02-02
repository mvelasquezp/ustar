<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB as DB;
use Mail;

class ProcesarEnviosCompartamos extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "envios:envios_compartamos";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Procesar los envíos para Compartamos Financiera";

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
        // definir folder para el log
        $log_path = implode("/", [env("APP_MAILER_FOLDER"), "logs"]);
        if (!file_exists($log_path)) mkdir($log_path, 0755, true);
        $log_file = implode("/", [$log_path, date("Ymd_Hi") . "-compartamos.log"]);
        // carga la lista de envíos
        $envios = DB::select("call sp_mailer_pendientes_compartamos");
        foreach ($envios as $pos => $envio) {
            $token = base64_encode($envio->id . ":" . $envio->certificado);
            $email = $envio->email;
            $id = $envio->id;
            $json = DB::table("mailer_envios")->select("de_json as json")->where("id_envio", $id)->first()->json;
            $asunto = json_decode($json)->n0_expediente_carpeta_fiscal_caso;
            Mail::send("usmailer.mail-compartamos", compact("token","id","asunto"), function($message) use($email, $asunto) {
                $message->from(env("MAIL_FROM_ADDRESS"), env("MAIL_FROM_NAME"));
                $vemail = explode("/", $email);
                foreach ($vemail as $i => $iemail) {
                    $vemail[$i] = trim($iemail);
                }
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $message->to($vemail)->subject($asunto);
                    $message->replyTo("ctapiap@compartamos.pe");
                }
                else echo "dirección '$email' inválida\n";
            });
            // actualiza flag de envío
            DB::statement("call sp_mailer_confirma_envio(?,?,?)", [1, $envio->id, $envio->certificado]);
            // escribe el log
            $texto = "$id enviado a $email\n";
            file_put_contents($log_file, $texto, FILE_APPEND | LOCK_EX);
            // notificar
            echo $texto;
        }
        echo "ok\n";
    }
}
