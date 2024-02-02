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
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:0 0 8px 0;text-align:center">Para asegurar la entrega de nuestros envíos, por favor agregue <b>{{ env("MAIL_FROM_ADDRESS") }}</b> a su libreta de direcciones.</p>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:0 0 8px 0;text-align:center">Si no visualiza bien este mensaje, haga clic <a href="{{ url('mailer/mail-preview-compartamos') }}?token={{ $token }}">aquí</a>.</p>
            <table style="border-collapse:collapse;border-spacing:0;width:100%">
                <tbody>
                    <tr>
                        <td>
                            <p style="font-family:Arial;font-size:14px;margin-bottom:8px">Estimados</p>
                            <p style="font-family:Arial;font-size:14px;margin-bottom:2px">Mediante la presente, se adjunta carta de respuesta al oficio indicado en el asunto.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:middle;padding:6px 0;">
                            <p style="margin:0;color:#808080;font-family:Arial;font-size:18px;text-align:left">{{ $asunto }}</p>
                        </td>
                        <td style="vertical-align:middle;padding:6px 0;text-align:right;">
                            <a href="{{ url('mailer/usdocs/carta') }}?token={{ $token }}" style="background-color:#d81b60;border-radius:4px;color:#fff;display:inline-block;margin:0;padding:4px 12px 8px;font-family:Arial;font-size:12px;text-decoration:none;text-align:center"><img src="{{ asset('images/logos/ic-pdf.png') }}" style="display:inline-block;margin:0 4px 0 0;height:16px;top:3px;position:relative">Descargar</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="font-family:Arial;font-size:14px;margin-bottom:12px">Por favor, confirmar la recepción de la misma.</p>
                            <p style="font-family:Arial;font-size:14px;margin-bottom:2px">Saludos cordiales</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img id="envio_{{ $id }}_mail" src="{{ url('mailer/ustar-logo') }}?token={{ $token }}&type=comparfin" alt="" style="margin:0 auto;width:320px">
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:8px 0 0 0;padding:0 6px;text-align:justify;">Este mensaje fue enviado a través de Union Star &reg; para Compartamos Financiera. Si Ud. no es el destinatario a quien se dirigió el mensaje, se le notifica por este medio que queda prohibido el uso, la divulgación, copia, distribución o cualquier actividad del mismo, bajo responsabilidad. Si Ud. ha recibido este mensaje por error por favor proceda a eliminarlo y notificar inmediatamente dando <a href="#" style="color:#d81b60;">clic aquí</a>.</p>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:8px 0 0 0;padding:0 6px;text-align:justify;">Recuerda que nunca solicitaremos tus datos confidenciales por correo, tales como cuentas bancarias, claves de tarjetas, DNI, o tu número de celular. Si tienes alguna duda acerca de la autenticidad de este correo envíalo a la dirección <a href="mailto:ctapiap@compartamos.pe" style="color:#d81b60;">ctapiap@compartamos.pe</a> y te responderemos.</p>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:8px 0 0 0;padding:0 6px;text-align:center;">Este correo electrónico ha sido enviado a #!email!# / Para anular su suscripción, haga <a href="#" style="color:#404040;">clic aquí</a>.</p>
        </div>
    </body>
</html>