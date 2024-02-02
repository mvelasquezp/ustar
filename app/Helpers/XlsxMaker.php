<?php

namespace App\Helpers;

use Excel;

class XlsxMaker {

    public static function XlsxLaPositiva ($folder, $filename, $resultados, $desde, $hasta, $guia) {
        Excel::create($filename, function($excel) use ($resultados, $desde, $hasta, $guia) {
            $excel->sheet("datos", function($sheet) use ($resultados, $desde, $hasta, $guia) {
                $sheet->setColumnFormat(array(
                    'B' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
                    'C' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
                ));
                $sheet->row(1, ["Guía(s)", $guia]);
                $sheet->row(2, ["Desde", $desde, "Hasta", $hasta]);
                $sheet->row(4, ["Guía", "#Certificado", "DNI", "Nombre", "Tipo", "e-mail",
                    "Fecha envío", "Estado envío", "Fecha lectura email", "Estado lectura email", "Fecha lectura adjunto 1", "Estado lectura adjunto 1",
                    "Fecha lectura adjunto 2", "Estado lectura adjunto 2", "Observaciones"
                ]);
                $sheet->cells("A4:O4", function($cells) {
                    $cells->setBackground("#0d47a1");
                    $cells->setFontColor("#ffffff");
                });
                $idxRow = 5;
                foreach ($resultados as $idx => $envio) {
                    $i = $idxRow + $idx;
                    $ijson = json_decode($envio->jsondata);
                    if (isset($ijson->reponsable_de_pago)) {
                        $nombre = $ijson->reponsable_de_pago;
                    }
                    else {
                        $nombre = $ijson->nombre_aseg;
                    }
                    $rData = [
                        $envio->guia, $envio->certificado, $envio->dni, $nombre, $envio->tipo, $envio->email,
                        $envio->envio, $envio->esenvio, $envio->feleido, $envio->esleido, $envio->fecarta, $envio->escarta,
                        strcmp($envio->escontrato, "x") != 0 ? $envio->fecontrato : "", strcmp($envio->escontrato, "x") != 0 ? $envio->escontrato : "", $envio->observaciones
                    ];
                    $sheet->row($i, $rData);
                    if($i % 2 == 0) {
                        $sheet->row($i, function($row) {
                            $row->setBackground("#f0f0f0");
                        });
                    }
                    if (strcmp($envio->esenvio, "N") == 0) {
                        $sheet->row($i, function($row) {
                            $row->setFontColor("#f44336");
                        });
                    }
                }
                $sheet->row(3, ["Envíos", count($resultados)]);
            });
        })->store("xlsx", $folder);
    }

    public static function XlsxCompartamos ($folder, $filename, $resultados, $desde, $hasta, $guia) {
        Excel::create($filename, function($excel) use ($resultados, $desde, $hasta, $guia) {
            $excel->sheet("datos", function($sheet) use ($resultados, $desde, $hasta, $guia) {
                /*
                $sheet->setColumnFormat(array(
                    'B' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
                    'C' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
                ));
                */
                $sheet->row(1, ["Guía(s)", $guia]);
                $sheet->row(2, ["Desde", $desde, "Hasta", $hasta]);
                $sheet->row(4, [
                    "Guía", "Nº envío", "Fecha de envío", "Nº Expediente SBS", "Correlativos",
                    "N° Tipo Solicitud", "N° Expediente / Carpeta Fiscal / Caso", "Entidad Solicitante", "Nombre de la Autoridad", "N° Oficio de la Autoridad",
                    //
                    "Estado envío", "Fecha lectura email", "Estado lectura email", "Fecha lectura adjunto 1", "Estado lectura adjunto 1",
                    "Fecha lectura adjunto 2", "Estado lectura adjunto 2", "Observaciones"
                ]);
                $sheet->cells("A4:R4", function($cells) {
                    $cells->setBackground("#0d47a1");
                    $cells->setFontColor("#ffffff");
                });
                $idxRow = 5;
                foreach ($resultados as $idx => $envio) {
                    $i = $idxRow + $idx;
                    $ijson = json_decode($envio->jsondata);
                    $rData = [
                        $envio->guia, $ijson->n0_envio, $ijson->fecha_de_envio, $ijson->n0_expediente_sbs, $ijson->correlativos,
                        $ijson->n0_tipo_solicitud, $ijson->n0_expediente_carpeta_fiscal_caso, $ijson->entidad_solicitante, $ijson->nombre_de_la_autoridad, $ijson->n0_oficio_de_la_autoridad,
                        //
                        $envio->esenvio, $envio->feleido, $envio->esleido, $envio->fecarta, $envio->escarta,
                        strcmp($envio->escontrato, "x") != 0 ? $envio->fecontrato : "", strcmp($envio->escontrato, "x") != 0 ? $envio->escontrato : "", $envio->observaciones
                    ];
                    $sheet->row($i, $rData);
                    if($i % 2 == 0) {
                        $sheet->row($i, function($row) {
                            $row->setBackground("#f0f0f0");
                        });
                    }
                    if (strcmp($envio->esenvio, "N") == 0) {
                        $sheet->row($i, function($row) {
                            $row->setFontColor("#f44336");
                        });
                    }
                }
                $sheet->row(3, ["Envíos", count($resultados)]);
            });
        })->store("xlsx", $folder);
    }
}