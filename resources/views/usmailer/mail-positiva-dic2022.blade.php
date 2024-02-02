@php
// colores
$naranja = "#ff6d46";
$azul = "#8888c6";
$gris = "#7a7a87";
$texto = "#606060";
$melon = "#ffe3dc";
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Notificación</title>
    </head>
    <body style="background-color:#202020;padding:0;margin:0">
        <div style="width:98%;max-width:640px;margin:0 auto;background-color:#ffffff">
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:0 0 8px 0;text-align:center;padding-top:4px">Para asegurar la entrega de nuestros envíos, por favor agregue <b>{{ env("MAIL_FROM_ADDRESS") }}</b> a su libreta de direcciones.</p>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:0 0 6px 0;text-align:center">Si no visualiza bien este mensaje, haga clic <a href="{{ url('mailer/mail-preview-lapositiva') }}?token={{ $token }}">aquí</a>.</p>
            <table style="border-collapse:collapse;border-spacing:0;width:100%">
                <tbody>
                    <tr>
                        <td colspan="2" style="padding:0;">
                            <div style="background-color:{{ $naranja }};box-sizing:border-box;border-radius:10px 10px 0 0;text-align:center;width:100%;margin:0">
                                <img id="envio_{{ $id }}_mail" src="{{ asset('mailer/ustar-logo') }}?token={{ $token }}" alt="" style="margin:0 auto;height:60px">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:0;">
                            <div style="box-sizing:border-box;padding:0;margin:0">
                                <img src="{{ asset('images/mailer/img-header.png') }}" style="width:100%;">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="box-sizing:border-box;padding:16px 48px 8px">
                                <p style="color:{{ $naranja }};font-family:Arial;font-size:20px;margin:16px 0 0;text-align:center">Estimado cliente,</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="box-sizing:border-box;padding:16px 48px 48px;">
                                <p style="color:{{ $texto }};font-family:Arial;font-size:14px;margin:0 0 4px;text-align:center">Tenemos algo importante que comunicarle respecto a su póliza de</p>
                                <h4 style="color:{{ $texto }};font-family:Arial;font-size:14px;margin:0;text-align:center">Microseguro Vida Caja Plan III (Póliza Nº 207730)</h4>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 0 96px;">
                            <div style="background-color:{{ $melon }};box-sizing:border-box;padding:12px 36px;width:100%;border-radius:16px;">
                                <table style="border-collapse:collapse;border-spacing:0;width:100%">
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align:middle;padding:6px 0;">
                                                <p style="margin:0;color:{{ $azul }};font-family:Arial;font-size:14px;text-align:left">Podrás encontrar más información en la carta que está anexada al siguiente correo</p>
                                            </td>
                                            <td style="vertical-align:middle;padding:4px 0 4px 16px;text-align:right;">
                                                <a href="{{ url('mailer/usdocs/carta') }}?token={{ $token }}" style="color:#fff;display:inline-block;margin:0;padding:4px 0;font-family:Arial;font-size:12px;text-decoration:none;text-align:center">
                                                    <img src="{{ asset('images/mailer/ic-adjunto.png') }}" style="display:inline-block;margin:8px;height:40px" title="Descargar carta">
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="box-sizing:border-box;padding:32px 48px">
                                <p style="color:{{ $azul }};font-family:Arial;font-size:24px;margin:16px 0 0;text-align:center">#AsíDeSimple</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="box-sizing:border-box;padding:8px 20px">
                                <p style="margin:0 0 12px;color:#606060;font-family:Arial;font-size:14px;text-align:center">Empresa La Positiva Seguros y Reaseguros - RUC 20100210909</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:0">
                            <div style="background-color:{{ $gris }};box-sizing:border-box;margin:0;padding:6px 0;width:100%">
                                <p style="margin:0 auto;text-align:center">
                                    <a href="#" style="margin:0 6px;padding:0;"><img src="{{ asset('images/logos/ic-fb.png') }}" alt="facebook" style="height:36px;margin:0;padding:0;width:36px"></a>
                                    <a href="#" style="margin:0 6px;padding:0;"><img src="{{ asset('images/logos/ic-ig.png') }}" alt="instagram" style="height:36px;margin:0;padding:0;width:36px"></a>
                                    <a href="#" style="margin:0 6px;padding:0;"><img src="{{ asset('images/logos/ic-yt.png') }}" alt="youtube" style="height:36px;margin:0;padding:0;width:36px"></a>
                                </p>
                            </div>
                            <div style="background-color:#f6f6f6;box-sizing:border-box;margin:0;text-align:center;width:100%">
                                <img src="{{ asset('images/mailer/img-footer.png') }}" alt="la-positiva-logo" style="height:72px;margin:12px 0 8px">
                                <p style="font-family:Arial;font-size:14px;margin:0;text-align:center">
                                    <a href="#" style="border-radius:24px;border:2px solid {{ $naranja }};color:{{ $naranja }};font-family:Arial;font-size:14px;padding:6px 12px;display:inline-block;margin-bottom:20px;text-decoration:none">lapositiva.pe</a>
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!--p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:8px 0 0 0;padding:0 6px;text-align:justify;">Este mensaje fue enviado a través de Union Star &reg; por La Positiva. Este mensaje ha sido enviado a #!email!#. Este mensaje se encuentra dirigido exclusivamente para el uso del destinatario previsto y contiene información confidencial y/o privilegiada perteneciente a La Positiva (La Positiva Generales, La Positiva Vida y La Positiva EPS, Dirección <span style="color:{{ $naranja }};">Calle Francisco Masías 370 - San Isidro</span>). Si Ud. no es el destinatario a quien se dirigió el mensaje, se le notifica por este medio que queda prohibido el uso, la divulgación, copia, distribución o cualquier actividad del mismo, bajo responsabilidad. Si Ud. ha recibido este mensaje por error por favor proceda a eliminarlo y notificar inmediatamente dando <a href="#" style="color:{{ $naranja }};">clic aquí</a>.</p>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:8px 0 0 0;padding:0 6px;text-align:justify;">Recuerda que nunca solicitaremos tus datos confidenciales por correo, tales como cuentas bancarias, claves de tarjetas, DNI, o tu número de celular. Si tienes alguna duda acerca de la autenticidad de este correo envíalo a la dirección <a href="mailto:lineapositiva@lapositiva.com.pe" style="color:{{ $naranja }};">lineapositiva@lapositiva.com.pe</a> y te responderemos.</p>
            <p style="color:#b0b0b0;font-family:Arial;font-size:10px;margin:8px 0 0 0;padding:0 6px;text-align:center;">Este correo electrónico ha sido enviado a #!email!# / Para anular su suscripción, haga <a href="#" style="color:#404040;">clic aquí</a>.</p-->
        </div>
    </body>
</html>