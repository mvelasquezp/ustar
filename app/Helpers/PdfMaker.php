<?php

namespace App\Helpers;

use setasign\Fpdi\Fpdi;
use setasign\FpdiProtection\FpdiProtection;

class PdfMaker {

    static $meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"];

    // documentos de clientes
    
    public static function CartaCliente ($fila, $guia) {
        // prepara la ruta del archivo base
        $base_carta = implode("/", [env("APP_MAILER_FOLDER"), "base", "clientes-carta.pdf"]);
        // prepara ruta destino
        $output_folder = implode("/", [env("APP_MAILER_FOLDER"), $guia, "clientes"]);
        if (!file_exists($output_folder)) mkdir($output_folder, 0755, true);
        // crea el archivo destino
        $filename = $fila->dni_responsable_de_pago . "_" . $fila->certificado;
        $output_carta = $output_folder . "/" . $filename . "_carta.pdf";
        // prepara la carta
        $fecha_hoy = "San Isidro, " . date("d") . " de " . self::$meses[(int) date("m")] . " " . date("Y");
        $pdf = new Fpdi();
            $pdf->setSourceFile($base_carta);
        $tpl = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
            $pdf->AddFont("calibri", "", "calibri.php", true);
            $pdf->AddFont("calibrib", "", "calibrib.php", true);
            $pdf->SetFont("calibri");
            $pdf->SetFontSize("12");
            $pdf->SetXY(29.5,40);
            $pdf->Cell(50, 10, $fecha_hoy, 0, 0, 'L'); // fecha
            $pdf->SetFont("calibrib");
            $pdf->SetXY(29.5,54);
            $pdf->Cell(50, 10, $fila->reponsable_de_pago, 0, 0, 'L'); // nombre
            $pdf->SetFont("calibri");
            $pdf->SetXY(29.5,59);
            $pdf->Cell(50, 10, "(" . $fila->direccion_responsable_de_pago . " - " . $fila->distrito_responsable_de_pago . " -", 0, 0, 'L'); // direccion 1
            $pdf->SetXY(29.5,64);
            $pdf->Cell(50, 10, $fila->provincia_responsable_de_pago . " - " . $fila->departamento_responsable_de_pago . ")", 0, 0, 'L'); // direccion 2
        $tpl = $pdf->importPage(2);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
        $tpl = $pdf->importPage(3);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
        $pdf->Output($output_carta, "F");
        return $output_carta;
    }

    public static function CertificadoClienteLibre ($fila, $guia) {
        // prepara la ruta del archivo base
        $base_certificado = implode("/", [env("APP_MAILER_FOLDER"), "base", "clientes-certificado.pdf"]);
        // prepara ruta destino
        $output_folder = implode("/", [env("APP_MAILER_FOLDER"), $guia, "clientes"]);
        if (!file_exists($output_folder)) mkdir($output_folder, 0755, true);
        // crea el archivo destino
        $filename = $fila->dni_responsable_de_pago . "_" . $fila->certificado;
        $output_certificado = $output_folder . "/" . $filename . "_cert_libre.pdf";
        // prepara el certificado
        $pdf = new Fpdi();
            $pdf->setSourceFile($base_certificado);
        $tpl = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
            $pdf->AddFont("arial", "", "arial.php", true);
            $pdf->SetFont("arial");
            $pdf->SetFontSize("10");
            $pdf->SetXY(18,42);
            $pdf->Cell(50, 10, $fila->certificado, 0, 0, 'L'); // nro certificado
            $pdf->SetXY(45,42);
            $pdf->Cell(50, 10, $fila->condicion_cliente_o_empleado, 0, 0, 'L'); // condicion
            $pdf->SetXY(97,42);
            $femision = "f._de_emision";
            $pdf->Cell(50, 10, $fila->$femision, 0, 0, 'L'); // fecha emision
            $pdf->SetXY(41,110);
            $pdf->Cell(50, 10, $fila->reponsable_de_pago, 0, 0, 'L'); // responsable de pago
            $pdf->SetXY(46,114.5);
            $pdf->Cell(50, 10, $fila->dni_responsable_de_pago, 0, 0, 'L'); // dni responsable de pago
            $pdf->SetXY(25,119);
            $pdf->Cell(20, 10, $fila->direccion_responsable_de_pago, 0, 0, 'L'); // direccion responsable de pago
            $pdf->SetXY(22,123.5);
            $pdf->Cell(20, 10, $fila->distrito_responsable_de_pago, 0, 0, 'L'); // distrito responsable de pago
            $pdf->SetXY(90,123.5);
            $pdf->Cell(20, 10, $fila->provincia_responsable_de_pago, 0, 0, 'L'); // provincia responsable de pago
            $pdf->SetXY(160,123.5);
            $pdf->Cell(20, 10, $fila->departamento_responsable_de_pago, 0, 0, 'L'); // departamento responsable de pago
            $ybase = 143;
            $ydelta = 13.36;
            // asegurado 01
            $it = 0;
            $pdf->SetXY(40,143);
            $pdf->Cell(20, 10, $fila->titular, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,147.5);
            $pdf->Cell(20, 10, $fila->dni_titular, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,147.5);
            $fnacimiento = "f._nacimiento_titular";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 02
            $it = 1;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_1, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_1, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_1";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 03
            $it = 2;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_2, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_2, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_2";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 04
            $it = 3;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_3, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_3, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_3";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 05
            $it = 4;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_4, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_4, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_4";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 06
            $it = 5;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_5, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_5, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_5";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 07
            $it = 6;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_6, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_6, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_6";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 08
            $it = 7;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_7, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_7, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_7";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 09
            $it = 8;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_8, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_8, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_8";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            $tpl = $pdf->importPage(2);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
            $pdf->SetXY(42,238);
            $pdf->Cell(20, 10, $fila->inicio_de_vigencia, 0, 0, 'L'); // inicio vigencia
        for ($i = 3; $i <= 11; $i++) {
            $tpl = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
        }
        $pdf->Output($output_certificado, "F");
        return $output_certificado;
    }

    public static function CertificadoClienteClave ($guia, $pdf_origen, $dni, $certificado) {
        $output_folder = implode("/", [env("APP_MAILER_FOLDER"), $guia, "clientes"]);
        if (!file_exists($output_folder)) mkdir($output_folder, 0755, true);
        // crea el archivo destino
        $filename = $dni . "_" . $certificado;
        $output_certificado_clave = $output_folder . "/" . $filename . "_cert.pdf";
        // crea el archivo
        $pdf = new FpdiProtection();
        // carga el certificado sin clave
        $pdf->setSourceFile($pdf_origen);
        for ($i = 1; $i <= 11; $i++) {
            $tpl = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
        }
        $pdf->SetProtection([], $dni);
        $pdf->Output($output_certificado_clave, "F");
        return $output_certificado_clave;
    }

    // documentos de empleados
    
    public static function CartaEmpleado ($fila, $guia) {
        // prepara la ruta del archivo base
        $base_carta = implode("/", [env("APP_MAILER_FOLDER"), "base", "empleados-carta.pdf"]);
        // prepara ruta destino
        $output_folder = implode("/", [env("APP_MAILER_FOLDER"), $guia, "empleados"]);
        if (!file_exists($output_folder)) mkdir($output_folder, 0755, true);
        // crea el archivo destino
        $filename = $fila->dni_responsable_de_pago . "_" . $fila->certificado;
        $output_carta = $output_folder . "/" . $filename . "_carta.pdf";
        // prepara la carta
        $fecha_hoy = "San Isidro, " . date("d") . " de " . self::$meses[(int) date("m")] . " " . date("Y");
        $pdf = new Fpdi();
            $pdf->setSourceFile($base_carta);
        $tpl = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
            $pdf->AddFont("calibri", "", "calibri.php", true);
            $pdf->AddFont("calibrib", "", "calibrib.php", true);
            $pdf->SetFont("calibri");
            $pdf->SetFontSize("12");
            $pdf->SetXY(29.5,40);
            $pdf->Cell(50, 10, $fecha_hoy, 0, 0, 'L'); // fecha
            $pdf->SetFont("calibrib");
            $pdf->SetXY(29.5,54);
            $pdf->Cell(50, 10, $fila->reponsable_de_pago, 0, 0, 'L'); // nombre
            $pdf->SetFont("calibri");
            $pdf->SetXY(29.5,59);
            $pdf->Cell(50, 10, "(" . $fila->direccion_responsable_de_pago . " - " . $fila->distrito_responsable_de_pago . " -", 0, 0, 'L'); // direccion 1
            $pdf->SetXY(29.5,64);
            $pdf->Cell(50, 10, $fila->provincia_responsable_de_pago . " - " . $fila->departamento_responsable_de_pago . ")", 0, 0, 'L'); // direccion 2
        $tpl = $pdf->importPage(2);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
        $tpl = $pdf->importPage(3);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
        $pdf->Output($output_carta, "F");
        return $output_carta;
    }

    public static function CertificadoEmpleadoLibre ($fila, $guia) {
        // prepara la ruta del archivo base
        $base_certificado = implode("/", [env("APP_MAILER_FOLDER"), "base", "empleados-certificado.pdf"]);
        // prepara ruta destino
        $output_folder = implode("/", [env("APP_MAILER_FOLDER"), $guia, "empleados"]);
        if (!file_exists($output_folder)) mkdir($output_folder, 0755, true);
        // crea el archivo destino
        $filename = $fila->dni_responsable_de_pago . "_" . $fila->certificado;
        $output_certificado = $output_folder . "/" . $filename . "_cert_libre.pdf";
        // prepara el certificado
        $pdf = new Fpdi();
            $pdf->setSourceFile($base_certificado);
        $tpl = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
            $pdf->AddFont("arial", "", "arial.php", true);
            $pdf->SetFont("arial");
            $pdf->SetFontSize("10");
            $pdf->SetXY(18,42);
            $pdf->Cell(50, 10, $fila->certificado, 0, 0, 'L'); // nro certificado
            $pdf->SetXY(45,42);
            $pdf->Cell(50, 10, $fila->condicion_cliente_o_empleado, 0, 0, 'L'); // condicion
            $pdf->SetXY(97,42);
            $femision = "f._de_emision";
            $pdf->Cell(50, 10, $fila->$femision, 0, 0, 'L'); // fecha emision
            $pdf->SetXY(41,110);
            $pdf->Cell(50, 10, $fila->reponsable_de_pago, 0, 0, 'L'); // responsable de pago
            $pdf->SetXY(46,114.5);
            $pdf->Cell(50, 10, $fila->dni_responsable_de_pago, 0, 0, 'L'); // dni responsable de pago
            $pdf->SetXY(25,119);
            $pdf->Cell(20, 10, $fila->direccion_responsable_de_pago, 0, 0, 'L'); // direccion responsable de pago
            $pdf->SetXY(22,123.5);
            $pdf->Cell(20, 10, $fila->distrito_responsable_de_pago, 0, 0, 'L'); // distrito responsable de pago
            $pdf->SetXY(90,123.5);
            $pdf->Cell(20, 10, $fila->provincia_responsable_de_pago, 0, 0, 'L'); // provincia responsable de pago
            $pdf->SetXY(160,123.5);
            $pdf->Cell(20, 10, $fila->departamento_responsable_de_pago, 0, 0, 'L'); // departamento responsable de pago
            $ybase = 143;
            $ydelta = 13.36;
            // asegurado 01
            $it = 0;
            $pdf->SetXY(40,143);
            $pdf->Cell(20, 10, $fila->titular, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,147.5);
            $pdf->Cell(20, 10, $fila->dni_titular, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,147.5);
            $fnacimiento = "f._nacimiento_titular";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 02
            $it = 1;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_1, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_1, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_1";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 03
            $it = 2;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_2, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_2, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_2";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 04
            $it = 3;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_3, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_3, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_3";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 05
            $it = 4;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_4, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_4, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_4";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 06
            $it = 5;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_5, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_5, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_5";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 07
            $it = 6;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_6, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_6, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_6";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 08
            $it = 7;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_7, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_7, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_7";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            // asegurado 09
            $it = 8;
            $pdf->SetXY(40,$ybase + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->asegurado_adicional_8, 0, 0, 'L'); // nombre
            $pdf->SetXY(46,$ybase + 4.5 + $ydelta * $it);
            $pdf->Cell(20, 10, $fila->dni_asegurado_adicional_8, 0, 0, 'L'); // doc iden
            $pdf->SetXY(168.5,$ybase + 4.5 + $ydelta * $it);
            $fnacimiento = "f._nacimiento_asegurado_adicional_8";
            $sfecha = "";
            $vfechanac = explode(" ", $fila->$fnacimiento);
            if (count($vfechanac) > 0) {
                $vfechanac = explode("-", $vfechanac[0]);
                if (count($vfechanac) == 3) {
                    list($anio,$mes,$dia) = $vfechanac;
                    $sfecha = implode("/",[$dia,$mes,$anio]);
                }
            }
            $pdf->Cell(20, 10, $sfecha, 0, 0, 'L'); // fecha nac
            $tpl = $pdf->importPage(2);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
            $pdf->SetXY(42,238);
            $pdf->Cell(20, 10, $fila->inicio_de_vigencia, 0, 0, 'L'); // inicio vigencia
        for ($i = 3; $i <= 11; $i++) {
            $tpl = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
        }
        $pdf->Output($output_certificado, "F");
        return $output_certificado;
    }

    public static function CertificadoEmpleadoClave ($guia, $pdf_origen, $dni, $certificado) {
        $output_folder = implode("/", [env("APP_MAILER_FOLDER"), $guia, "empleados"]);
        if (!file_exists($output_folder)) mkdir($output_folder, 0755, true);
        // crea el archivo destino
        $filename = $dni . "_" . $certificado;
        $output_certificado_clave = $output_folder . "/" . $filename . "_cert.pdf";
        // crea el archivo
        $pdf = new FpdiProtection();
        // carga el certificado sin clave
        $pdf->setSourceFile($pdf_origen);
        for ($i = 1; $i <= 11; $i++) {
            $tpl = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
        }
        $pdf->SetProtection([], $dni);
        $pdf->Output($output_certificado_clave, "F");
        return $output_certificado_clave;
    }

    public static function MicroSeguro ($fila, $guia) {
        // prepara la ruta del archivo base
        $base_carta = implode("/", [env("APP_MAILER_FOLDER"), "base", "positiva", "formato.pdf"]);
        // prepara ruta destino
        $output_folder = implode("/", [env("APP_MAILER_FOLDER"), $guia]);
        if (!file_exists($output_folder)) mkdir($output_folder, 0755, true);
        // crea el archivo destino
        $filename = $fila->certificado;
        $output_carta = $output_folder . "/" . $filename . "_nopsw.pdf";
        // prepara la fecha
        $arrmeses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"];
        list($fdia, $fmes, $fanio) = explode("/", date("d/m/Y"));
        $str_fecha = "Lima, $fdia de " . $arrmeses[(int) $fmes] . " del $fanio";
        // prepara la carta
        $xi = 29;
        $yi = 26;
        if (strcmp($fila->sexo, "Masculino") == 0) {
            $tratamiento = "Señor";
            $saludo = "Estimado cliente:";
        }
        else {
            $tratamiento = "Señora";
            $saludo = "Estimada clienta:";
        }
        $pdf = new Fpdi();
            $pdf->setSourceFile($base_carta);
        $tpl = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
            $pdf->AddFont("calibri", "", "calibri.php", true);
            $pdf->AddFont("calibrib", "", "calibrib.php", true);
            $pdf->SetFont("calibri");
            $pdf->SetFontSize("11");
            $pdf->SetXY($xi, $yi);
            $pdf->Cell(50, 10, $str_fecha, 0, 0, 'L'); // fecha
            $pdf->SetXY($xi, $yi + 8);
            $pdf->Cell(50, 10, utf8_decode($tratamiento), 0, 0, 'L'); // tratamiento
            $pdf->SetXY($xi, $yi + 15);
            $pdf->Cell(50, 10, utf8_decode($fila->nombre_aseg), 0, 0, 'L'); // nombre
            $pdf->SetXY($xi, $yi + 23);
            $pdf->Cell(50, 10, utf8_decode($saludo), 0, 0, 'L'); // saludo
        $pdf->Output($output_carta, "F");
        return $output_carta;
    }

    public static function MicroSeguroStep2 ($fila, $guia) {
        // prepara la ruta del archivo base
        $base_carta = implode("/", [env("APP_MAILER_FOLDER"), "base", "positiva", "formato-02.pdf"]);
        // prepara ruta destino
        $output_folder = implode("/", [env("APP_MAILER_FOLDER"), $guia]);
        if (!file_exists($output_folder)) mkdir($output_folder, 0755, true);
        // crea el archivo destino
        $filename = $fila->certificado;
        $output_carta = $output_folder . "/" . $filename . "_nopsw.pdf";
        // prepara la fecha
        $arrmeses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"];
        list($fdia, $fmes, $fanio) = explode("/", date("d/m/Y"));
        $str_fecha = "Lima, $fdia de " . $arrmeses[(int) $fmes] . " del $fanio";
        // prepara la carta
        $xi = 20;
        $yi = 40;
        if (strcmp($fila->sexo, "Masculino") == 0) {
            $tratamiento = "Señor";
            $saludo = "Estimado cliente:";
        }
        else {
            $tratamiento = "Señora";
            $saludo = "Estimada clienta:";
        }
        // 
        $pdf = new Fpdi();
            $pdf->setSourceFile($base_carta);
            $pdf->SetTextColor(123,123,137);
        $tpl = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
            $pdf->AddFont("sansserif", "", "ms-sans-serif.php", true);
            $pdf->SetFont("sansserif");
            $pdf->SetFontSize("11");
            $pdf->SetXY($xi, $yi);
            $pdf->Cell(50, 10, $str_fecha, 0, 0, 'L'); // fecha
            $pdf->SetXY($xi, $yi + 12);
            $pdf->Cell(50, 10, utf8_decode($tratamiento), 0, 0, 'L'); // tratamiento
            $pdf->SetXY($xi, $yi + 18);
            $pdf->Cell(50, 10, utf8_decode($fila->nombre_aseg), 0, 0, 'L'); // nombre
        $pdf->Output($output_carta, "F");
        return $output_carta;
    }

    public static function MicroSeguroClave ($guia, $pdf_origen, $dni, $certificado) {
        $output_folder = implode("/", [env("APP_MAILER_FOLDER"), $guia, "empleados"]);
        if (!file_exists($output_folder)) mkdir($output_folder, 0755, true);
        // crea el archivo destino
        $filename = $dni . "_" . $certificado;
        $output_certificado_clave = $output_folder . "/" . $filename . "_cert.pdf";
        // crea el archivo
        $pdf = new FpdiProtection();
        // carga el certificado sin clave
        $pdf->setSourceFile($pdf_origen);
        $tpl = $pdf->importPage(1);
        $pdf->AddPage();
        $pdf->useTemplate($tpl);
        $pdf->SetProtection([], $dni);
        $pdf->Output($output_certificado_clave, "F");
        return $output_certificado_clave;
    }
    
    public static function CartaCompartamos ($fila, $guia) {
        // prepara la ruta del archivo base
        $base_carta = implode("/", [env("APP_MAILER_FOLDER"), "base", "compartamos", "carta.pdf"]);
        // prepara ruta destino
        $output_folder = implode("/", [env("APP_MAILER_FOLDER"), $guia]);
        if (!file_exists($output_folder)) mkdir($output_folder, 0755, true);
        // crea el archivo destino
        $filename = $fila->correlativos;
        $output_carta = $output_folder . "/" . $filename . ".pdf";
        // prepara la carta
        $pdf = new Fpdi();
            $pdf->setSourceFile($base_carta);
        $tpl = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
            $pdf->AddFont("calibri", "", "calibri.php", true);
            $pdf->AddFont("calibrib", "", "calibrib.php", true);
            $pdf->SetFont("calibri");
            $pdf->SetFontSize("10");
            $pdf->SetXY(29.5,40);
            $pdf->Cell(50, 10, $fila->fecha_de_envio, 0, 0, 'L'); // fecha
            $pdf->SetFont("calibrib","U");
            $pdf->SetFontSize("11");
            $pdf->SetXY(29.5,62);
            $pdf->Cell(50, 10, utf8_decode("Carta Nº " . $fila->correlativos . " - 2021/COMPARTAMOS-AL-CFS"), 0, 0, 'L'); // correlativo
            $pdf->SetFont("calibri");
            $pdf->SetFontSize("10");
            $pdf->SetXY(29.5,74);
            $pdf->Cell(50, 10, utf8_decode($fila->nombre_de_la_autoridad), 0, 0, 'L'); // nombre de la autoridad
            $pdf->SetXY(29.5,79);
            $pdf->Cell(50, 10, utf8_decode($fila->entidad_solicitante), 0, 0, 'L'); // Entidad_Solicitante
            $pdf->SetXY(29.5,84);
            $pdf->Cell(50, 10, utf8_decode($fila->direccion_autoridad), 0, 0, 'L'); // Dirección_Autoridad
            $pdf->SetXY(29.5,89);
            $pdf->Cell(50, 10, utf8_decode($fila->distrito) . " " . utf8_decode($fila->provincia), 0, 0, 'L'); // Distrito - Provincia
            $pdf->SetXY(29.5,94);
            $pdf->Cell(50, 10, utf8_decode($fila->departamento), 0, 0, 'L'); // Departamento
            $pdf->SetXY(65,107);
            $pdf->Cell(50, 10, utf8_decode($fila->n0_oficio_de_la_autoridad), 0, 0, 'L'); // n0_oficio_de_la_autoridad
            $pdf->SetXY(61.5,112.5);
            $pdf->Cell(50, 10, utf8_decode($fila->n0_expediente_carpeta_fiscal_caso), 0, 0, 'L'); // n0_expediente_carpeta_fiscal_caso
            $sdelito = utf8_decode($fila->delito_materia);
            $sdelito2 = "";
            if (strlen($sdelito) > 64) {
                $spos = strpos($sdelito, " ", 64);
                $sdelito2 = substr($sdelito, $spos + 1);
                $sdelito = substr($sdelito, 0, $spos);
            }
            $pdf->SetXY(65,118);
            $pdf->Cell(50, 10, $sdelito, 0, 0, 'L'); // delito_materia
            $pdf->SetXY(65,123);
            $pdf->Cell(50, 10, $sdelito2, 0, 0, 'L'); // delito_materia
            $pdf->SetXY(80,129);
            $pdf->Cell(50, 10, utf8_decode($fila->n0_expediente_sbs), 0, 0, 'L'); // n0_expediente_sbs
        $pdf->Output($output_carta, "F");
        return $output_carta;
    }

    public static function GeneraCargo ($registro, $basepath) {
        // prepara la ruta del archivo base
        $base_cargo = implode("/", [env("APP_MAILER_FOLDER"), "base", "cargo.pdf"]);
        // prepara ruta destino
        $output_folder = $basepath;
        // crea el archivo destino
        $filename = $registro->certificado;
        $output_carta = $output_folder . "/" . $filename . ".pdf";
        // fecha y hora actual
        $fecha_cargo = date("d/m/Y H:i:s");
        // recupera json
        $jsonData = json_decode($registro->jsondata);
        // prepara la carta
        $xi = 20;
        $yi = 15;
        $pdf = new Fpdi();
            $pdf->setSourceFile($base_cargo);
        $tpl = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);
            $pdf->AddFont("calibri", "", "calibri.php", true);
            $pdf->AddFont("calibrib", "", "calibrib.php", true);
            $pdf->SetFont("calibrib");
            $pdf->SetFontSize("14");
            $pdf->SetXY(48, $yi + 0.5);
            $pdf->Cell(50, 10, $registro->certificado, 0, 0, 'L'); // nro de certificado
            $pdf->SetFont("calibri");
            $pdf->SetFontSize("8");
            $pdf->SetXY(103, 25);
            $pdf->Cell(50, 10, "Fecha y hora de corte", 0, 0, 'L'); // nombre cliente
            $pdf->SetXY(103, 28);
            $pdf->Cell(50, 10, $fecha_cargo, 0, 0, 'L'); // nombre cliente
            $pdf->SetFontSize("10");
            $pdf->SetXY($xi, $yi + 18);
            $pdf->Cell(50, 10, utf8_decode(isset($jsonData->nombre_aseg) ? $jsonData->nombre_aseg : $jsonData->nombre_de_la_autoridad), 0, 0, 'L'); // nombre cliente
            $pdf->SetXY($xi, $yi + 31);
            $pdf->Cell(50, 10, utf8_decode($registro->email), 0, 0, 'L'); // email
            $pdf->SetXY($xi, $yi + 43);
            $pdf->Cell(50, 10, utf8_decode(strcmp($registro->envio,"") == 0 ? "-" : $registro->envio), 0, 0, 'L'); // fecha envio
            $pdf->SetXY($xi, $yi + 52);
            $pdf->Cell(50, 10, utf8_decode(strcmp($registro->feleido,"") == 0 ? "-" : $registro->feleido), 0, 0, 'L'); // fecha recepcion
            $pdf->SetXY($xi, $yi + 61);
            $pdf->Cell(50, 10, utf8_decode(strcmp($registro->fecarta,"") == 0 ? "-" : $registro->fecarta), 0, 0, 'L'); // fecha lectura
        $pdf->Output($output_carta, "F");
        return $output_carta;
    }
}