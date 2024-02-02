<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB as DB;
use Mail;

class ProcesarListaEnvios extends Command {

    protected $signature = "envios:procesar_pendientes";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Envía los correos pendientes";

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
        $log_file = implode("/", [$log_path, date("Ymd_Hi") . ".log"]);
        // carga la lista de envíos
        $envios = DB::select("call sp_mailer_pendientes_positiva");
        foreach ($envios as $pos => $envio) {
            $id = $envio->id;
            $email = $envio->email;
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $token = base64_encode($envio->id . ":" . $envio->certificado);
                Mail::send("usmailer.mail-positiva-dic2022", compact("token","id"), function($message) use($email) {
                    $message->from(env("MAIL_FROM_ADDRESS"), "La Positiva Seguros");
                    $message->to($email)->subject("🚨Importante: Comunicación sobre tu póliza de Microseguro Vida Caja Plan III. Aquí 👇");
                    $message->replyTo("usrservicesmail@lapositiva.com.pe");
                });
                // actualiza flag de envío
                DB::statement("call sp_mailer_confirma_envio(?,?,?)", [1, $envio->id, $envio->certificado]);
                // escribe el log
                $texto = "Envío $id - Destinatario: $email - Hora: " . date("d/m/Y H:i:s") . "\n";
                file_put_contents($log_file, $texto, FILE_APPEND | LOCK_EX);
                // notificar
                echo $texto;
            }
            else {
                DB::table("mailer_envios")->where("id_envio", $id)->update([
                    "nu_intentos" => DB::raw("nu_intentos + 1")
                ]);
                DB::table("mailer_error_log")->where("id_envio", $id)->where("st_ultimo", "S")->update(["st_ultimo" => "N"]);
                DB::table("mailer_error_log")->insert([
                    "id_envio" => $id,
                    "de_certificado" => $envio->certificado,
                    "fe_registro" => DB::raw("now()"),
                    "de_error" => "La dirección de email $email es inválida",
                    "st_ultimo" => "S"
                ]);
                echo "dirección $email incorrecta\n";
            }
        }
        echo "ok\n";
    }
}
