<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Notificación</title>
    </head>
    <body>
        <div style="width:98%;max-width:800px;margin:0 auto;">
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:0;text-align:center">Para asegurar la entrega de nuestros envíos, por favor agregue <b>{{ env("MAIL_FROM_ADDRESS") }}</b> a su libreta de direcciones.</p>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:0 0 8px 0;text-align:center">Si no visualiza bien este mensaje, haga clic <a href="{{ url('mailer/mail-preview') }}">aquí</a>.</p>
            <table style="border-collapse:collapse;border-spacing:0;width:100%">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <div style="background-color:rgb(238,118,32);box-sizing:border-box;border-radius:10px 10px 0 0;text-align:center;width:100%">
                                <img id="envio_89_mail" src="{{ url('mailer/ustar-logo') }}" alt="" style="margin:0 auto;height:80px">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="65%">
                            <div style="box-sizing:border-box;padding:16px 48px">
                                <h4 style="color:#7879b9;font-family:Arial;font-size:20px;margin:0 0 8px">¡Gracias por seguir confiando en tu seguro ONCOMAX!</h4>
                                <p style="color:#606060;font-family:Arial;font-size:16px;margin:0">Conoce las condiciones de la renovación de tu Seguro Oncológico Oncomax (Código SBS AE0416400250), el cual será renovado con nuevas condiciones a partir del 01 de Noviembre de 2020 hasta el 31 de Octubre de 2021, según tu fecha de renovación.</p>
                            </div>
                        </td>
                        <td width="35%">
                            <img src="{{ asset('images/logos/ic-banner.png') }}" alt="" style="width:90%;margin:24px auto">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="background-color:#f2f2f3;box-sizing:border-box;padding:24px 64px;width:100%">
                                <h3 style="margin-bottom:6px;color:#606060;font-family:Arial;font-size:20px;text-align:center">Para mayor información, revisar los siguientes documentos</h3>
                                <table style="border-collapse:collapse;border-spacing:0;width:100%">
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align:middle;padding:6px 0;">
                                                <p style="margin:0;color:#606060;font-family:Arial;font-size:16px;text-align:left">Carta de renovación con nuevas condiciones</p>
                                            </td>
                                            <td style="vertical-align:middle;padding:6px 0;text-align:right;">
                                                <a href="{{ url('mailer/usdocs/carta') }}" target="_blank" style="background-color:#ff7952;border-radius:4px;color:#fff;display:inline-block;margin:0;padding:4px 12px 8px;font-family:Arial;font-size:12px;text-decoration:none;text-align:center"><img src="{{ asset('images/logos/ic-pdf.png') }}" style="display:inline-block;margin:0 4px 0 0;height:16px;top:3px;position:relative">Descargar</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align:middle;padding:6px 0;">
                                                <p style="margin:0;color:#606060;font-family:Arial;font-size:16px;text-align:left">Certificado del seguro con nuevas primas</p>
                                            </td>
                                            <td style="vertical-align:middle;padding:6px 0;text-align:right;">
                                                <a href="{{ url('mailer/usdocs/contrato') }}" target="_blank" style="background-color:#ff7952;border-radius:4px;color:#fff;display:inline-block;margin:0;padding:4px 12px 8px;font-family:Arial;font-size:12px;text-decoration:none;text-align:center"><img src="{{ asset('images/logos/ic-pdf.png') }}" style="display:inline-block;margin:0 4px 0 0;height:16px;top:3px;position:relative">Descargar</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p style="margin-bottom:6px;color:#7879b9;font-family:Arial;font-weight:bold;font-size:14px;text-align:center">También podrás ingresar a www.lapositiva.com.pe para acceder a las condiciones generales del producto contratado.</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="box-sizing:border-box;padding:8px 20px">
                                <p style="margin:0 0 12px;color:#606060;font-family:Arial;font-size:14px">En caso de no estar conforme con los cambios propuestos, sírvase a comunicárnoslo por escrito en un plazo no mayor a 30 días calendario previo al vencimiento de su seguro. Transcurrido este plazo sin que se produzca esta comunicación, emitiremos la renovación de tu Póliza de Seguro, generándose la obligación de cancelar la nueva prima correspondiente al nuevo periodo.</p>
                                <p style="margin:0 0 12px;color:#606060;font-family:Arial;font-size:14px">Es importante tomar en consideración que, de no estar conforme con los cambios propuestos, el seguro no será renovado de conformidad con le artículo 7º de la Ley Nº 29946 - Ley del Contrato de Seguro, quedando liberado de la obligación de pago antes indicada.</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="background-color:#5dcbc4;box-sizing:border-box;margin: 0;padding:16px 32px;width:100%">
                                <h3 style="color:#fff;font-family:Arial;font-size:20px;margin:0;text-align:center">Para mayor información podrás comunicarte con nosotros al (01) 211-0213</h3>
                            </div>
                            <div style="background-color:#a2a2aa;box-sizing:border-box;margin:0;padding:6px 0;width:100%">
                                <p style="margin:0 auto;text-align:center">
                                    <a href="#" style="margin:0 6px;padding:0;"><img src="{{ asset('images/logos/ic-fb.png') }}" alt="facebook" style="height:36px;margin:0;padding:0;width:36px"></a>
                                    <a href="#" style="margin:0 6px;padding:0;"><img src="{{ asset('images/logos/ic-ig.png') }}" alt="instagram" style="height:36px;margin:0;padding:0;width:36px"></a>
                                    <a href="#" style="margin:0 6px;padding:0;"><img src="{{ asset('images/logos/ic-yt.png') }}" alt="youtube" style="height:36px;margin:0;padding:0;width:36px"></a>
                                </p>
                            </div>
                            <div style="background-color:#f2f2f3;box-sizing:border-box;border-radius:0 0 10px 10px;margin:0;text-align:center;width:100%">
                                <img src="{{ asset('images/logos/lapositiva.png') }}" alt="la-positiva-logo" style="height:72px;margin:12px 0 0">
                                <h3 style="color:#606060;font-family:Arial;font-size:14px;margin:0 0 12px">Así de simple</h3>
                                <p style="font-family:Arial;font-size:14px;margin:0;text-align:center">
                                    <a href="#" style="border-radius:24px;border:2px solid #ff7952;color:#ff7952;font-family:Arial;font-size:14px;padding:6px 12px;display:inline-block;margin-bottom:20px;text-decoration:none">lapositiva.pe</a>
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:8px 0 0 0;padding:0 6px;text-align:justify;">Este mensaje fue enviado a través de Union Star &reg; por La Positiva. Este mensaje ha sido enviado a #!email!#. Este mensaje se encuentra dirigido exclusivamente para el uso del destinatario previsto y contiene información confidencial y/o privilegiada perteneciente a La Positiva (La Positiva Generales, La Positiva Vida y La Positiva EPS, Dirección <span style="color:#ff7952;">Calle Francisco Masías 370 - San Isidro</span>). Si Ud. no es el destinatario a quien se dirigió el mensaje, se le notifica por este medio que queda prohibido el uso, la divulgación, copia, distribución o cualquier actividad del mismo, bajo responsabilidad. Si Ud. ha recibido este mensaje por error por favor proceda a eliminarlo y notificar inmediatamente dando <a href="#" style="color:#ff7952;">clic aquí</a>.</p>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:8px 0 0 0;padding:0 6px;text-align:justify;">Recuerda que nunca solicitaremos tus datos confidenciales por correo, tales como cuentas bancarias, claves de tarjetas, DNI, o tu número de celular. Si tienes alguna duda acerca de la autenticidad de este correo envíalo a la dirección <a href="mailto:lineapositiva@lapositiva.com.pe" style="color:#ff7952;">lineapositiva@lapositiva.com.pe</a> y te responderemos.</p>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:8px 0 0 0;padding:0 6px;text-align:center;">Este correo electrónico ha sido enviado a #!email!# / Para anular su suscripción, haga <a href="#" style="color:#404040;">clic aquí</a>.</p>
        </div>
    </body>
</html>