<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\PHPIMAP\ClientManager as ClientManager;
use Webklex\PHPIMAP\Client as Client;
use DB;

class RevisarEnviosFallidos extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "envios:revisar";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Revisa si hubo envíos fallidos";

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
        $log_file = implode("/", [$log_path, date("Ymd_Hi") . "_errores.log"]);
        // go
        $cm = new ClientManager();
        $client = $cm->make([
            "host" => env("IMAP_HOST"),
            "port" => env("IMAP_PORT"),
            "encryption" => env("IMAP_ENCRYPTION"),
            "validate_cert" => env("IMAP_VALIDATE_CERT"),
            "username" => env("IMAP_USERNAME"),
            "password" => env("IMAP_PASSWORD"),
            "protocol" => env("IMAP_PROTOCOL"),
        ]);
        $client->connect();
        $folders = $client->getFolders();
        foreach($folders as $folder) {
            $messages = $folder->messages()->unseen()->get();
            foreach($messages as $message) {
                $id_envio = -1;
                $de_motivo = "El envío falló";
                $texto = $message->getTextBody();
                // busca motivo: dirección falló
                $posfailed = strpos($texto, "The following address(es) failed");
                if ($posfailed !== false) $de_motivo = "No se pudo resolver la dirección del destinatario";
                // busca motivo: cola
                $poslimit = strpos($texto, "exceeded the max emails per hour");
                if ($poslimit !== false) $de_motivo = "Cola de correos excedida. Reprogramado para el siguiente envío";
                // busca el id del envío en los adjuntos
                foreach ($message->getAttachments() as $attachment) {
                    $texto = $attachment->content;
                    if (preg_match('/envio_([0-9]+)_/', $texto, $matches)) {
                        $id_envio = (int) $matches[1];
                    }
                }
                if ($id_envio > -1) {
                    $texto = "no envió " . $id_envio . " - Motivo: $de_motivo\n";
                    file_put_contents($log_file, $texto, FILE_APPEND | LOCK_EX);
                    echo $texto;
                    DB::statement("call sp_mailer_notifica_no_envio(?,?)", [$id_envio, $de_motivo]);
                }
                //
                $message->setFlag("Seen");
            }
        }
    }
}
